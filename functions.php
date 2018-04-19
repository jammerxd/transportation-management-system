<?php
/*
Session Vars:
userid - string
steamUser - class object of User
*/

/*Database User Fields*/
/*
steamID
userName
status -> Pending, Driver, LOA, Retired, Banned, Suspended, Rejected

registeredSince
lastLogin
lastJob
lastSteamCheck

canSubmitJob
canApproveJobs
canEditUserStatus
canEditVTCSettings


*/
/*Database TMPBans Fields*/
/*
steamID,
expiration,
timeAdded,
active,
reason,
adminName,
adminID
*/

/*Database log Fields*/
/*
id
time
action
detail
steamID
userName
*/

/*User Statuses*/
/*
Pending
Driver
Banned 
Suspended
Rejected
LOA
Retired
*/

function getPDO()
{
	try
	{
		$settings = getSettings();
		$database = 'mysql:host='.$settings->dbHost.';dbname='.$settings->dbName.';charset=utf8';

		$pdo = new PDO($database, $settings->dbUser, $settings->dbPassword);	
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}
	catch(PDOException $e)
	{
		return false;
	}
}
function getUser($steamid)
{
	$pdo = getPDO();
	$queryStr = "SELECT userName, loggerKey, status, canSubmitJobs, canApproveJobs, 
		canEditUserStatus, canEditVTCSettings, lastSteamCheck, DATEDIFF(NOW(),lastSteamCheck) AS daysSinceLastSteamCheck FROM users WHERE steamid=:steamid";
	$query = $pdo->prepare($queryStr);
	$query->execute([":steamid"=>$steamid]);
	
	if($query->rowCount() === 1)
	{
		$usr = new User();
		$usr->steamID = $steamid;
		
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result["daysSinceLastSteamCheck"] > 0 || !isset($result['lastSteamCheck']))
		{
			//run steam and tmp update
			$usr->userName = API_GetDisplayName($steamid);
			$usr->TMPBans = API_GetTMPBans($steamid);
			$updateStr = "UPDATE users SET lastSteamCheck=NOW() WHERE steamid=:steamid";
			$updateQuery = $pdo->prepare($updateStr);
			
			$updateQuery->execute([":steamid"=>$steamid]);
			
		}
		else
		{
			$usr->userName = $result["userName"];
			$usr->TMPBans = getTMPBans($usr->steamID);
		}
		
		
		
		
		$usr->status = $result["status"];
		$usr->permissions->canSubmitJobs = $result["canSubmitJobs"] == 0 ? false : true;
		$usr->permissions->canApproveJobs = $result["canApproveJobs"] == 0 ? false : true;
		$usr->permissions->canEditUserStatus = $result["canEditUserStatus"] == 0 ? false : true;
		$usr->permissions->canEditVTCSettings = $result["canEditVTCSettings"] == 0 ? false : true;
		$usr->ATSStats = new Statistics();
		$usr->ATSStats = getStats($usr->steamID, 1);
		$usr->ETS2Stats = new Statistics();
		$usr->ETS2Stats = getStats($usr->steamID,0);
		$usr->loggerKey = $result["loggerKey"];
		return $usr;
		
	}
	else
	{
		return false;
	}
}
function getTMPBans($steamid)
{
	$bans = array();
	$pdo = getPDO();
	$queryStr = "SELECT * FROM tmpbans WHERE steamID=:steamid";
	$query = $pdo->prepare($queryStr);
	$query->execute([":steamid"=>$steamid]);
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $banResult)
	{
		$tempBan = new TMPBan();
		$tempBan->expiration = $banResult["expiration"];
		$tempBan->timeAdded = $banResult["timeAdded"];
		$tempBan->active = $banResult["active"];
		$tempBan->reason = $banResult["reason"];
		$tempBan->adminName = $banResult["adminName"];
		$tempBan->adminID = $banResult["adminID"];
		$bans[] = $tempBan;
	}
	return $bans;
}
function getGame($game,$type)
{
	$val = 0;
	//Returns 1 for ATS, 0 for ETS2, 2 for unknown for type 1
	//Returns string for type 2
	if($game == 1 || strpos($game,"ATS") !== false || $game == "ATS" || $game == "ats" || strpos($game,"American") !== false || strpos($game,"american"))
	{
		$val = 1;
	}
	else if($game == 0 || strpos($game, "Euro") !== false || $game == "Ets2" || $game == "ETS2" || strpos($game,"2") !== false)
	{
		$val= 0;
	}
	else
	{
		$val= 2;
	}
	
	if($type == 2)
	{
		if($val == 1)
			$val = "American Truck Simulator";
		else if($val == 0)
			$val = "Eruo Truck Simulator 2";
		else
			$val = "Unknown";
	}
	
	return $val;
}
function getStats($steamid, $game)
{
	$game = getGame($game, 1);
	$gameStr = getGame($game,2);
	$stats = new Statistics();
	$pdo = getPDO();
	if($game != 2 && $gameStr != "Unknown")
	{
		
		if($steamid !== false)
		{
			$queryStr = "
			select 
				COALESCE(sum(case when (game=:game0 AND status=\"Cancelled\" AND steamID=:steamID0) then 1 else 0 end),0) totalCancelledJobs,
				COALESCE(sum(case when (game=:game19 AND status=\"Finished\" AND approved=0 AND steamID=:steamID1) then 1 else 0 end), 0) totalRejectedJobs,
				COALESCE(sum(case when (game=:game1 AND status=\"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID2) then 1 else 0 end),0) totalSuccessfulJobs,
				COALESCE(sum(case when (game=:game2 AND status != \"Started\" AND status != \"In Progress\" AND steamID=:steamID3) then 1 else 0 end),0) totalJobs,
				COALESCE(sum(case when (game=:game3 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID4) then litresBurned else 0 end)/sum(case when (game=:game4 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID5) then 1 else 0 end),0.00) avgLitresBurned,
				COALESCE(sum(case when (game=:game5 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID6) then income else 0 end)/sum(case when (game=:game6 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID7) then 1 else 0 end),0.00) avgIncome,
				COALESCE(sum(case when (game=:game7 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID8) then travelExpenses else 0 end)/sum(case when (game=:game8 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID9) then 1 else 0 end),0.00) avgExpenses,
				COALESCE(sum(case when (game=:game9 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID10) then trailerDamage else 0 end)/sum(case when (game=:game10 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID11) then 1 else 0 end),0.00) avgTrailerDamage,
				COALESCE(sum(case when (game=:game11 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID12) then trailerDamageCost else 0 end)/sum(case when (game=:game12 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID13) then 1 else 0 end),0.00) avgDamageCost,
				COALESCE(sum(case when (game=:game13 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID14) then fuelCost else 0 end)/sum(case when (game=:game14 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID15) then 1 else 0 end),0.00) avgFuelCost,
				COALESCE(sum(case when (game=:game15 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID16) then distanceDriven else 0 end)/sum(case when (game=:game16 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID17) then 1 else 0 end),0.00) avgDistanceDriven,
				COALESCE(sum(case when (game=:game17 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID18) then income else 0 end)/sum(case when (game=:game18 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0 AND steamID=:steamID19) then distanceDriven else 0 end),0.00) avgIncomePerDistance
			from jobs
			";
			$query = $pdo->prepare($queryStr);
			$query->execute([
				":game0"=>$gameStr, ":steamID0"=>$steamid,
				":game1"=>$gameStr, ":steamID1"=>$steamid,
				":game2"=>$gameStr, ":steamID2"=>$steamid,
				":game3"=>$gameStr, ":steamID3"=>$steamid,
				":game4"=>$gameStr, ":steamID4"=>$steamid,
				":game5"=>$gameStr, ":steamID5"=>$steamid,
				":game6"=>$gameStr, ":steamID6"=>$steamid,
				":game7"=>$gameStr, ":steamID7"=>$steamid,
				":game8"=>$gameStr, ":steamID8"=>$steamid,
				":game9"=>$gameStr, ":steamID9"=>$steamid,
				":game10"=>$gameStr, ":steamID10"=>$steamid,
				":game11"=>$gameStr, ":steamID11"=>$steamid,
				":game12"=>$gameStr, ":steamID12"=>$steamid,
				":game13"=>$gameStr, ":steamID13"=>$steamid,
				":game14"=>$gameStr, ":steamID14"=>$steamid,
				":game15"=>$gameStr, ":steamID15"=>$steamid,
				":game16"=>$gameStr, ":steamID16"=>$steamid,
				":game17"=>$gameStr, ":steamID17"=>$steamid,
				":game18"=>$gameStr, ":steamID18"=>$steamid,
				":game19"=>$gameStr, ":steamID19"=>$steamid
			]);
		}
		else
		{
			$queryStr = "
			select 
				COALESCE(sum(case when (game=:game0 AND status=\"Cancelled\") then 1 else 0 end),0) totalCancelledJobs,
				COALESCE(sum(case when (game=:game19 AND status=\"Finished\" AND approved=0) then 1 else 0 end), 0) totalRejectedJobs,
				COALESCE(sum(case when (game=:game1 AND status=\"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0) totalSuccessfulJobs,
				COALESCE(sum(case when (game=:game2 AND status != \"Started\" AND status != \"In Progress\") then 1 else 0 end),0) totalJobs,
				COALESCE(sum(case when (game=:game3 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then litresBurned else 0 end)/sum(case when (game=:game4 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgLitresBurned,
				COALESCE(sum(case when (game=:game5 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then income else 0 end)/sum(case when (game=:game6 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgIncome,
				COALESCE(sum(case when (game=:game7 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then travelExpenses else 0 end)/sum(case when (game=:game8 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgExpenses,
				COALESCE(sum(case when (game=:game9 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then trailerDamage else 0 end)/sum(case when (game=:game10 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgTrailerDamage,
				COALESCE(sum(case when (game=:game11 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then trailerDamageCost else 0 end)/sum(case when (game=:game12 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgDamageCost,
				COALESCE(sum(case when (game=:game13 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then fuelCost else 0 end)/sum(case when (game=:game14 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgFuelCost,
				COALESCE(sum(case when (game=:game15 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then distanceDriven else 0 end)/sum(case when (game=:game16 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then 1 else 0 end),0.00) avgDistanceDriven,
				COALESCE(sum(case when (game=:game17 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then income else 0 end)/sum(case when (game=:game18 AND status = \"Finished\" AND approved=1 AND income > 0 AND income < 1000000 AND distanceDriven > 10 AND litresBurned > 0) then distanceDriven else 0 end),0.00) avgIncomePerDistance
			from jobs
			";
			$query = $pdo->prepare($queryStr);
			$query->execute([
				":game0"=>$gameStr,
				":game1"=>$gameStr,
				":game2"=>$gameStr,
				":game3"=>$gameStr,
				":game4"=>$gameStr,
				":game5"=>$gameStr,
				":game6"=>$gameStr,
				":game7"=>$gameStr,
				":game8"=>$gameStr,
				":game9"=>$gameStr,
				":game10"=>$gameStr,
				":game11"=>$gameStr,
				":game12"=>$gameStr,
				":game13"=>$gameStr,
				":game14"=>$gameStr,
				":game15"=>$gameStr,
				":game16"=>$gameStr,
				":game17"=>$gameStr,
				":game18"=>$gameStr,
				":game19"=>$gameStr
			]);
		}
		$data = $query->fetch(PDO::FETCH_ASSOC);
		$stats->totalJobs = $data["totalJobs"];
		$stats->totalSuccessfulJobs = $data["totalSuccessfulJobs"];
		$stats->totalCancelledJobs = $data["totalCancelledJobs"];
		$stats->totalRejectedJobs = $data["totalRejectedJobs"];
		$stats->avgIncome = $data["avgIncome"];
		$stats->avgLitresBurned = $data["avgLitresBurned"];
		$stats->avgDistanceDriven = $data["avgDistanceDriven"];
		$stats->avgExpenses = $data["avgExpenses"];
		$stats->avgDamage = $data["avgTrailerDamage"];
		$stats->avgDamageCost = $data["avgDamageCost"];
		$stats->avgFuelCost = $data["avgFuelCost"];
		$stats->avgIncomePerDistance = $data["avgIncomePerDistance"];
		
	}
	return $stats;
}


#region TruckersMP API REFERENCES

#region Get TruckersMP name
function GetTruckersMPName($steamid)
{
	/*$result = json_decode(file_get_contents("http://api.truckersmp.com/v2/player/" . $steamid . "/"),true);
	if($result["error"])
		return NULL;
	else
		return $result["response"]["name"];
	*/
	try
	{
		//extract data from the post
		//set POST variables
		$url = 'http://jammerxdproductions.com/fetchUserName.php';
		$fields = array(
			'fjkdfhjhdskjhdjfkjh'=>$steamid
		);
		$fields_string = "";
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_USERAGENT,"TruckersData Logger");
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		//execute post
		$apiResult = curl_exec($ch);

		//close connection
		curl_close($ch);
		if($apiResult == "" || $apiResult == NULL)
		{
			return NULL;
		}
		else
		{
			return $apiResult;
		}
	}
	catch (Exception $e)
	{
		return NULL;
	}
}
#endregion

#region GetUserBans
function API_GetTMPBans($steamid)
{
	$settings = getSettings();
	$bans = array();
	/*$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL,"https://api.truckersmp.com/v2/bans/" . $steamid);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->VTCName);
	$result = curl_exec($curl_handle);
	curl_close($curl_handle);*/
	
	//extract data from the post
	//set POST variables
	$url = 'http://jammerxdproductions.com/fetchUserName.php';
	$fields = array(
		'sfdkjhdskjsdhkjh'=>$steamid
	);
	$fields_string = "";
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_USERAGENT,"TruckersData Logger");
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);
	
	$jsonResult = json_decode($result,true);
	$pdo = getPDO();
	if(!$jsonResult["error"])
	{
		foreach($jsonResult["response"] as $res)
		{
			$tempBan = new TMPBan();
			
			$tempBan->expiration = $res["expiration"];
			$tempBan->timeAdded = $res["timeAdded"];
			$tempBan->active = $res["active"];
			$tempBan->reason = $res["reason"];
			$tempBan->adminName = $res["adminName"];
			$tempBan->adminID = $res["adminID"];
			
			$queryStr = "SELECT * FROM tmpbans WHERE 
			steamid=:steamid AND 
			expiration=:expiration AND
			timeAdded=:timeAdded AND 
			active=:active AND
			reason=:reason AND
			adminName=:adminName AND
			adminID=:adminID";
			$query = $pdo->prepare($queryStr);
			$query->execute([
			":steamid"=>$steamid,
			":expiration"=>$tempBan->expiration,
			":timeAdded"=>$tempBan->timeAdded,
			":active"=>$tempBan->active,
			":reason"=>$tempBan->reason,
			":adminName"=>$tempBan->adminName,
			":adminID"=>$tempBan->adminID
			]);
			
			if($query->rowCount() === 0)
			{
				$insertBanStr = "INSERT INTO tmpbans (steamID,expiration,timeAdded,active,reason,adminName,adminID) VALUES (:steamid,:expiration,:timeAdded,:active,:reason,:adminName,:adminID)";
				
				$insertQuery = $pdo->prepare($insertBanStr);
				$insertQuery->execute([
				":steamid"=>$steamid,
				":expiration"=>$tempBan->expiration,
				":timeAdded"=>$tempBan->timeAdded,
				":active"=>$tempBan->active,
				":reason"=>$tempBan->reason,
				":adminName"=>$tempBan->adminName,
				":adminID"=>$tempBan->adminID
				]);
			}
			
			$bans[] = $tempBan;
		}
	}
	return $bans;
}
#endregion

#endregion


#region STEAM API REFERENCES

#region Get Steam Profile
function GetSteamProfile($steamid)
{
    $settings = new Settings();
    $settings = getSettings();
    if(trim($settings->steamAPIKey) != "")
    {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->vtcName);
		$result = curl_exec($curl_handle);
		curl_close($curl_handle);
        if(!strpos($result,"error") && !strpos($result,"Error") && !strpos($result,"ERROR"))
        {

            $result = json_decode($result,true);
            $result = $result["response"]["players"];

            return $result[0];

        }
        else
        {
            //error found
            return NULL;
        }
    }
    else
    {
        return NULL;
    }
}
#endregion

#region Get ETS2Time
function GetSteamETS2Time($steamid)
{
    $settings = new Settings();
    $settings = getSettings();

    if(trim($settings->steamAPIKey) != "")
    {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->vtcName);
		$result = curl_exec($curl_handle);
		curl_close($curl_handle);
        if(!strpos($result,"error") && !strpos($result,"Error") && !strpos($result,"ERROR"))
        {

            $result = json_decode($result,true);
            if(isset($result["response"]["games"]))
            {
                $result = $result["response"]["games"];

                foreach($result as $rawGame)
                {
                    if($rawGame["appid"] == 227300)
                        return $rawGame["playtime_forever"];
                }
            }
            return 0;

        }
        else
        {
            return 0;
        }
    }
    else
    {
        return 0;
    }
}
#endregion

#region Get ATSTime
function GetSteamATSTime($steamid)
{
    $settings = new Settings();
    $settings = getSettings();

    if(trim($settings->steamAPIKey) != "")
    {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->vtcName);
		$result = curl_exec($curl_handle);
        if(!strpos($result,"error") && !strpos($result,"Error") && !strpos($result,"ERROR"))
        {

            $result = json_decode($result,true);
            if(isset($result["response"]["games"]))
            {
                $result = $result["response"]["games"];

                foreach($result as $rawGame)
                {
                    if($rawGame["appid"] == 270880)
                        return $rawGame["playtime_forever"];
                }
            }
            return 0;

        }
        else
        {
            return 0;
        }
    }
    else
    {
        return 0;
    }

}
#endregion

#region Get Steam PersonaName
function GetSteamName($steamid)
{
    $settings = new Settings();
    $settings = getSettings();
    $idToReturn = "";
    if(trim($settings->steamAPIKey) != "")
    {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->vtcName);
		$result = curl_exec($curl_handle);
		curl_close($curl_handle);
        if($result !== false)
        {
		    if(!strpos($result,"error") && !strpos($result,"Error") && !strpos($result,"ERROR"))
		    {
                $result = json_decode($result,true);
			    $idToReturn = $result["response"]["players"][0]["personaname"];
		    }
        }
    }
    else
    {
        $idToReturn = $steamid;
    }
    return $idToReturn;
}
#endregion

#endregion

#region Get DisplayName
function API_GetDisplayName($steamid)
{

    $idToReturn = ""; //String we are going to return
    if($steamid != "")
    {

        $idToReturn = GetTruckersMPName($steamid);//check TruckersMP API first, then steam...

        if($idToReturn == "")
        {
            $idToReturn = GetSteamName($steamid); //check steam...

            if($idToReturn == "")
            {
                $idToReturn == "Unknown";
            }
        }
    }
	$pdo = getPDO();
	$queryStr = "UPDATE users SET userName=:userName WHERE steamid=:steamid";
	$query = $pdo->prepare($queryStr);
	$query->execute([":userName"=>$idToReturn,":steamid"=>$steamid]);
    return $idToReturn;
}
#endregion

#region Reset Logger Key
function ResetLoggerKey($steamid)
{
	$validNewKey = false;
	$newKey = "";
	$pdo = getPDO();
	$queryStr = "SELECT loggerKey FROM users";
	while($validNewKey == false)
	{
		$newKey =  bin2hex(openssl_random_pseudo_bytes(20));
		$query = $pdo->prepare($queryStr);
		$query->execute([]);
		foreach($query->fetchAll(PDO::FETCH_ASSOC) as $result)
		{
			if($result["loggerKey"] == $newKey)
			{
				break;
			}
		}
		$validNewKey = true;
	}
	$queryStr = "UPDATE users SET loggerKey=:newKey WHERE steamid=:steamid";
	$query = $pdo->prepare($queryStr);
	$query->execute([":newKey"=>$newKey,":steamid"=>$steamid]);
	return $newKey;
	//$pdo = getPDO();
	
}
#endregion

#region Add Log Item
function LogAction($steamid, $action, $detail, $userName)
{
	$pdo = getPDO();
	$queryStr = "INSERT INTO log (action, detail, steamID, userName) VALUES (:action, :detail, :steamID, :userName)";
	$query = $pdo->prepare($queryStr);
	$query->execute([":action"=>$action,":detail"=>$detail,":steamID"=>$steamid,":userName"=>$userName,]);
}
#endregion


#region Get API Link
function GetAPILink()
{
	$url = $_SERVER['HTTP_HOST'] ;
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$url = "https://" . $url;
		if($_SERVER['SERVER_PORT'] != 443 && $_SERVER['SERVER_PORT'] != 80)
		{
			$url .= ":" . $_SERVER['SERVER_PORT'];
		}
	}
	else
	{
		$url = "http://" . $url;
		if($_SERVER['SERVER_PORT'] != 80)
		{
			$url .= ":" . $_SERVER['SERVER_PORT'];
		}
	}
	$url .= $_SERVER["REQUEST_URI"];
	$parts = explode('/',$url);
	$parts[count($parts) - 1] = "";
	$url = implode('/',$parts);
	
	$url .=  "api/index.php";
	return $url;
}
#endregion

#region Register User
function RegisterSteamID($steamid)
{
	$settings = new Settings();
    $settings = getSettings();
	if($settings->VTCRegistrationOpen === true)
	{
		LogAction($steamid, "Register User", "Attempting to register user with steam id " + $steamid, "N/A");
		$pdo = getPDO();
		$newUser = new User();
		$newUser->steamID = $steamid;
		$queryStr = "INSERT INTO users (steamID,userName) VALUES (:steamID,'')";
		$query = $pdo->prepare($queryStr);
		$query->execute([":steamID"=>$newUser->steamID]);
		ResetLoggerKey($steamid);
		$newUser = getUser($steamid);
		
		return true;
	}
	else
	{
		return false;
	}
}
#endregion

#region Get All Accounts Registered
function GetAllSteamAccounts($steamID)
{
	$pdo = getPDO();
	$queryStr = "SELECT steamID, userName, status, canSubmitJobs, canApproveJobs, canEditUserStatus, canEditVTCSettings FROM users WHERE steamID != :steamID and steamID != :protected1 AND steamID != :protected2";
	$query = $pdo->prepare($queryStr);
	$query->execute([
		":steamID"=>$steamID,
		":protected1"=>"76561198131380603",
		":protected2"=>"76561198352265945"]);
	$userList = array();
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $result)
	{
		$userList[] = new MinimalUser($result["steamID"], $result["userName"],$result["status"],new Permissions($result["canSubmitJobs"],$result["canApproveJobs"],$result["canEditUserStatus"],$result["canEditVTCSettings"]));
	}
	return $userList;
}
function GetAllStatuses($selected="")
{
	$stat = "<select name=\"status\" id=\"status\">";
	$stat .= "<option value=\"Pending\" ";
	if($selected == "Pending")
		$stat .= "selected";
	$stat.=">Pending</option>";

	$stat .= "<option value=\"Driver\" ";
	if($selected == "Driver")
		$stat .= "selected";
	$stat.=">Driver</option>";


	$stat .= "<option value=\"LOA\" ";
	if($selected == "LOA")
		$stat .= "selected";
	$stat.=">LOA</option>";


	$stat .= "<option value=\"Retired\" ";
	if($selected == "Retired")
		$stat .= "selected";
	$stat.=">Retired</option>";


	$stat .= "<option value=\"Banned\" ";
	if($selected == "Banned")
		$stat .= "selected";
	$stat.=">Banned</option>";


	$stat .= "<option value=\"Suspended\" ";
	if($selected == "Suspended")
		$stat .= "selected";
	$stat.=">Suspended</option>";


	$stat .= "<option value=\"Rejected\" ";
	if($selected == "Rejected")
		$stat .= "selected";
	$stat.=">Rejected</option>";


	$stat .= "<option value=\"InActive\" ";
	if($selected == "InActive")
		$stat .= "selected";
	$stat.=">In Active</option>";
	$stat .= "</select>";
	return $stat;
}
function GetCheckBoxBool($isChecked,$id)
{
	$chbx = "<label class=\"input-control checkbox\">";
	$chbx .="<input type=\"checkbox\" id=\"" . $id . "\" name=\"" . $id . "\" value=\"1\"" ;
	if($isChecked == "1")
	{

		$chbx .= "checked=\"\"";
	}
	//$chbx .= ">";
	$chbx .= "><span class=\"check\"></span>";
	$chbx .= "</label>";
	return $chbx;
}
#endregion

#region Update A User
function UpdateUser($steamID,$status,$canSubmit,$canApprove,$canEdit,$canVTC)
{
	$pdo = GetPDO();
	$queryStr = "UPDATE users SET status=:status, canSubmitJobs=:canSubmitJobs, canApproveJobs=:canApproveJobs, canEditUserStatus=:canEditUserStatus, canEditVTCSettings=:canEditVTCSettings WHERE steamID=:steamID";
	$query = $pdo->prepare($queryStr);
	return $query->execute([
		":steamID"=>$steamID,
		":status"=>$status,
		":canSubmitJobs"=>$canSubmit,
		":canApproveJobs"=>$canApprove,
		":canEditUserStatus"=>$canEdit,
		":canEditVTCSettings"=>$canVTC
	]);

}
#endregion

#region Get AN api user
function GetAPIUser($loggerKey)
{
	$pdo = getPDO();
	$queryStr = "SELECT id, steamID, userName, status, canSubmitJobs FROM users WHERE loggerKey = :loggerKey";
	$query = $pdo->prepare($queryStr);
	$query->execute([":loggerKey"=>$loggerKey]);
	$result = array();
	$result["error"] = true;//error
	$result["errorMessage"] = "No values set";//errormessage
	$result["steamID"] = "";//steamID
	$result["userName"] = "";//userName
	$result["status"] = "";//status
	$result["userid"] = "";//user id
	if($query->rowCount() === 1)
	{
		$row = $query->fetch(PDO::FETCH_ASSOC);
		$result["userid"] = $row["id"];
		$result["steamID"] = $row["steamID"];
		$result["userName"] = $row["userName"];
		$result["status"] = $row["status"];
		if($row["canSubmitJobs"] != "1")
		{
			$result["error"] = true;
			$result["errorMessage"] = "Unauthorized";
		}
		else if($row["status"] != "Driver")
		{
			$result["error"] = true;
			$result["errorMessage"] = "Unauthorized";
		}
		else
		{
			$result["error"] = false;
			$result["errorMessage"] = "";
		}
	}
	else
	{
		$result["error"] = true;
		$result["errorMessage"] = "Unauthorized";
	}
	return $result;
}
#endregion


#region Get All Deliveries by user
function GetAllDeliveriesByUser($steamID)
{
	$pdo = getPDO();
	$queryStr = "SELECT new_jobs.id, users.userName, games.abbreviation, new_jobs.status, new_jobs.sourceCity, new_jobs.destinationCity, new_jobs.cargo, new_jobs.truckMake, new_jobs.truckModel, new_jobs.income, new_jobs.trailerMass, new_jobs.finishTrailerDamage,new_jobs.distanceDriven, new_jobs.fuelBurned, new_jobs.fuelPurchased FROM new_jobs, users, games WHERE new_jobs.userID = users.id AND new_jobs.gameID = games.id AND users.steamID = :userid ORDER BY new_jobs.id DESC";
	$query = $pdo->prepare($queryStr);
	$query->execute([':userid'=>$steamID]);
	$jobList = array();
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $result)
	{
		$jobList[] = new MinimalJob($result['id'],$result['abbreviation'],$result['status'],$result['sourceCity'],$result['destinationCity'],$result['cargo'],$result['truckMake'],$result['truckModel'],$result['income'],$result['trailerMass'],$result['finishTrailerDamage'],$result['distanceDriven'],$result['fuelBurned'],$result['fuelPurchased'],$result['userName'],$steamID);
	}
	return $jobList;
}
#endregion


#region Get All Deliveries
function GetAllDeliveries()
{
	$pdo = getPDO();
	$queryStr = "SELECT new_jobs.id, users.steamID,users.userName, games.abbreviation, new_jobs.status, new_jobs.sourceCity, new_jobs.destinationCity, new_jobs.cargo, new_jobs.truckMake, new_jobs.truckModel, new_jobs.income, new_jobs.trailerMass, new_jobs.finishTrailerDamage,new_jobs.distanceDriven, new_jobs.fuelBurned, new_jobs.fuelPurchased FROM new_jobs, users, games WHERE new_jobs.userID = users.id AND new_jobs.gameID = games.id ORDER BY new_jobs.id DESC";
	$query = $pdo->prepare($queryStr);
	$query->execute([]);
	$jobList = array();
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $result)
	{
		$jobList[] = new MinimalJob($result['id'],$result['abbreviation'],$result['status'],$result['sourceCity'],$result['destinationCity'],$result['cargo'],$result['truckMake'],$result['truckModel'],$result['income'],$result['trailerMass'],$result['finishTrailerDamage'],$result['distanceDriven'],$result['fuelBurned'],$result['fuelPurchased'],$result['userName'],$result['steamID']);
	}
	return $jobList;
}
#endregion

#region Get All Deliveries in progress
function GetAllDeliveriesInProgress()
{
	$pdo = getPDO();
	$queryStr = "SELECT new_jobs.id, users.steamID,users.userName, games.abbreviation, new_jobs.status, new_jobs.sourceCity, new_jobs.destinationCity, new_jobs.cargo, new_jobs.truckMake, new_jobs.truckModel, new_jobs.income, new_jobs.trailerMass, new_jobs.finishTrailerDamage,new_jobs.distanceDriven, new_jobs.fuelBurned, new_jobs.fuelPurchased FROM new_jobs, users, games WHERE new_jobs.userID = users.id AND new_jobs.gameID = games.id AND new_jobs.status = :inprogStat AND TIMESTAMPDIFF(MINUTE,new_jobs.lastUpdated,NOW()) <= 15";
	$query = $pdo->prepare($queryStr);
	$query->execute([':inprogStat'=>3]);
	$jobList = array();
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $result)
	{
		$jobList[] = new MinimalJob($result['id'],$result['abbreviation'],$result['status'],$result['sourceCity'],$result['destinationCity'],$result['cargo'],$result['truckMake'],$result['truckModel'],$result['income'],$result['trailerMass'],$result['finishTrailerDamage'],$result['distanceDriven'],$result['fuelBurned'],$result['fuelPurchased'],$result['userName'],$result['steamID']);
	}
	return $jobList;
}
#endregion
?>