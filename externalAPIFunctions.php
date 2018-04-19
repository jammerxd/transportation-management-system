<?php
#region TruckersMP API REFERENCES


#region Get TruckersMP name
function GetTruckersMPName($steamid)
{
	$result = json_decode(file_get_contents("http://api.truckersmp.com/v2/player/" . $steamid . "/"),true);
	if($result["error"])
		return NULL;
	else
		return $result["response"]["name"];
}
#endregion

#region GetUserBans
function GetUserBans($steamid)
{
	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL,);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $settings->vtcName);
	$result = curl_exec($curl_handle);
	curl_close($curl_handle);
	if(!strpos($result,"error") && !strpos($result,"Error") && !strpos($result,"ERROR"))
	{
	}
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
		curl_setopt($curl_handle, CURLOPT_URL,'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
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
		curl_setopt($curl_handle, CURLOPT_URL,'http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
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
		curl_setopt($curl_handle, CURLOPT_URL,'http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
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
		curl_setopt($curl_handle, CURLOPT_URL,'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $settings->steamAPIKey . '&' . 'steamids=' . $steamid . '&' . 'format=json');
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
function GetDisplayName($steamid)
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
    return $idToReturn;
}
#endregion

?>