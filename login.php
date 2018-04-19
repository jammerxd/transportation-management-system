
<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');
?>
<div class="center padding 10 full-height v-align-middle">
<?php
$callbackReceived = false;
if(!isset($_SESSION["steamUser"]))
{
	$auth = new SteamAuth();
	
	$auth->SetOnLoginCallback(function($steamid){
		include('lang/index.php');//must re-include due to callback
		/*Check steamid against users db, set the steamUser session, then redirect to profile page*/
		$callbackReceived = true;
		//echo "Success! Steamid: " . $steamid;
		$usr = getUser($steamid);
		if($usr !== false )
		{
			/*$queryStr = "UPDATE users SET lastLogin=NOW() WHERE steamID=:steamID; INSERT INTO log (action,detail,steamID,userName) VALUES (:action,:detail,:steamID1,:userName)";
			$pdo = getPDO();
			$query = $pdo->prepare($queryStr);
			$query->execute([":steamID"=>$usr->steamID,":steamID1"=>$usr->steamID,":action"=>"User Login",":detail"=>$usr->steamID + " is attempting to login with username " + $usr->userName + ".", ":userName"=>$usr->userName]);
			*/
			$queryStr = "UPDATE users SET lastLogin=NOW() WHERE steamID=:steamID;";
			$pdo = getPDO();
			$query = $pdo->prepare($queryStr);
			$query->execute([":steamID"=>$usr->steamID]);
			
			
			$queryStr = "INSERT INTO log (action,detail,steamID,userName) VALUES (:action,:detail,:steamID,:userName)";
			$query = $pdo->prepare($queryStr);
			
			$query->execute([":steamID"=>$usr->steamID,":action"=>"User Login",":detail"=>"User sigin attempt", ":userName"=>$usr->userName]);
			
			if($usr->status == "Driver")
			{
				
				$_SESSION['steamUser'] = $usr;
			
				header('Location: profile.php');
			}
			else
			{
				if($usr->status == "Pending")
				{
					
					?>
					<h3 class="fg-orange"><?php echo $lang['error']; ?></h3>
					<p class="fg-orange"><?php echo $lang['sits_user_Pending']; ?></p>
					<?php
				}
				else if($usr->status == "LOA")
				{
					?>
					<h3 class="fg-orange"><?php echo $lang['error']; ?></h3>
					<p class="fg-orange"><?php echo $lang['sits_user_LOA']; ?></p>
					<?php
				}
				else if($usr->status == "Retired")
				{
					?>
					<h3 class="fg-orange"><?php echo $lang['error']; ?></h3>
					<p class="fg-orange"><?php echo $lang['sits_user_Retired']; ?></p>
					<?php
				}
				else if($usr->status == "Banned")
				{
					?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['sits_user_Banned']; ?></p>
					<?php
				}
				else if($usr->status == "Suspended")
				{
					?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['sits_user_Suspended']; ?></p>
					<?php
				}
				else if($usr->status == "Rejected")
				{
					?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['sits_user_Rejected']; ?></p>
					<?php
				}
				else if($usr->status == "InActive")
				{
					?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['sits_user_InActive']; ?></p>
					<?php
				}
				else
				{
					?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['err_unknown']; ?></p>
					<?php
				}
			}
		}
		else
		{
			?>
			<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
			<p class="fg-red"><?php echo $lang['sits_user_not_found']; ?></p>
			<?php
		}
		
		
		return true;
	});

	$auth->SetOnLoginFailedCallback(function(){
		/*Return Error*/
		include('lang/index.php');//must re-include due to callback
		$callbackReceived = true;
		?>
		<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
		<p class="fg-red"><?php echo $lang['sits_general_error']; ?></p>
		<?php
		return true;
	});


	$auth->SetOnLogoutCallback(function($steamid){
		return true; 
	});
	
	$auth->Init();
	
	if($auth->IsUserLoggedIn())
	{
		//$auth->Logout();
	}
	else
	{

	?>
		<h3><?php echo $lang["sign_in_through_steam"]; ?></h3>
		<a href="<?php echo $auth->GetLoginURL(); ?>"><img src="img/sits_02.png" alt="<?php echo $lang["sign_in_through_steam_img"]; ?>"/></a>
	<?php
	}
}
else
{
	header("Location: index.php");
	exit;
}	
?>
</div>
<?php
include_once('footer.php');
?>