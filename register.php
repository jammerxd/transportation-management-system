<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');

$callbackReceived = false;
if (isset($_SESSION['steamUser']))
{
	header('Location: profile.php');
}
else
{
	
	$auth = new SteamAuth();
	
	$auth->SetOnLoginCallback(function($steamid){
		include('lang/index.php');//must re-include due to callback
		$callbackReceived = true;
		$usr = getUser($steamid);
		if($usr !== false )
		{
		?>
		<div class="center padding-10 full-height v-align-middle">
			<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
			<p class="fg-red"><?php echo $lang['sits_register_user_exists']; ?></p>
		</div>
		<?php
		}
		else
		{	
			if(RegisterSteamID($steamid))
			{
				?>
				<div class="center padding-10 full-height v-align-middle">
					<h3 class="fg-green"><?php echo $lang['success']; ?></h3>
					<p class="fg-green"><?php echo $lang['user_register_success']; ?></p>
				</div>
				<?php
			}
			else
			{
				?>
				<div class="center padding-10 full-height v-align-middle">
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo $lang['user_register_error']; ?></p>
				</div>
				<?php
			}
		}
		return true;
	});
	
	$auth->SetOnLoginFailedCallback(function($steamid){
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
	<div class="center padding-10 full-height v-align-middle">
		<?php
			if($settings->VTCRegistrationOpen === true)
			{ ?>
		<h3><?php echo $lang["register_through_steam"]; ?></h3>
		<a href="<?php echo $auth->GetLoginURL(); ?>"><img src="img/sits_02.png" alt="<?php echo $lang["sign_in_through_steam_img"]; ?>"/></a>
		<?php
			}
			else
			{
				?>
					<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
					<p class="fg-red"><?php echo "Registration Is Closed"; ?></p>
				<?php
			}
		?>
	</div>
	<?php
	}	
}
include_once('footer.php');
?>