<?php
/*HOSTING24.com SHARED HOSTING DOES NOT ALLOW INI_SET OR SESSION CONFIGURATION....

//ini_set('display_errors', '1');
//ini_set('log_errors','1');
// Prevents javascript XSS attacks aimed to steal the session ID
//ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
//ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
//ini_set('session.cookie_secure', 1);


HOSTING24.com SHARED HOSTING DOES NOT ALLOW INI_SET OR SESSION CONFIGURATION...
*/
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
header('Content-Type:text/html; charset=UTF-8');
include_once('lang/index.php');
include_once('settings.php');
include_once('classes/Settings.php');
include_once('classes/User.php');
include_once('classes/Permissions.php');
include_once('classes/Job.php');
include_once('functions.php');

/*if(!file_exists('config.php'))
{
	header('Location: /setup');
	exit;
}
if(file_exists('setup'))
{
	echo "<title>TMS - Transportation Management System</title>";
	echo $lang['err_1'];
	exit;
}
*/
$statusesSignIn = ["Driver", "Retired", "LOA"];//statuses allowed to "sign in"

$settings = getSettings();
$vtcATSStats = getStats(false,1);
$vtcETS2Stats = getStats(false,0);
session_start();

?>

<!DOCTYPE html>

<html>
	<head>
		<title><?php echo $lang['title'] ;?></title>
		<link href="css/metro.css" rel="stylesheet"/>
		<link href="css/metro-icons.css" rel="stylesheet"/>
		<link href="css/metro-responsive.css" rel="stylesheet"/>
		<link href="css/metro-schemes.css" rel="stylesheet"/>
		<link href="css/animate.css" rel="stylesheet"/>
		<link href="custom_css/style.css" rel="stylesheet" />
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/metro.js"></script>
		<script src="custom_js/get_gravatar.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
	</head>
	
	<body>
		

		<div class="fg-dark bg-grayLighter padding10 align-center" id="secondContainer" style="padding-bottom: 30px;">
		
			<div class="app-bar no-padding no-margin" data-role="appbar">
				<a class="app-bar-element branding" href="<?php echo $settings->VTCUrl . "/index.php"; ?>"><?php echo $settings->VTCName; ?></a>
				<span id="divider" class="app-bar-divider"></span>
				
				<ul class="app-bar-menu">
					
				</ul>
				<ul class="app-bar-menu place-right">
					<?php
					if(!isset($_SESSION['steamUser']))
					{
					?>
						<li><a href="login.php">Login</a></li>
						<?php
						if($settings->VTCRegistrationOpen)
						{
						?>
						<li><a href="register.php">Register</a></li>
						
					<?php
						}
					}
					else
					{
						$_SESSION['steamUser'] = getUser($_SESSION['steamUser']->steamID);
						if(in_array($_SESSION['steamUser']->status, $statusesSignIn))
						{
					?>
						
						    <li>
								<a href="#" class="dropdown-toggle">&nbsp;<?php echo $_SESSION['steamUser']->userName;?>&nbsp;</a>
								<ul  class="d-menu" data-role="dropdown">
									<li><a href="drivingRecord.php"><?php echo $lang['driving_record']; ?></a></li>
									<li><a href="viewJobs.php"><?php echo $lang['view_jobs']; ?></a></li>
									<li><a href="profile.php"><?php echo $lang['account_settings']; ?></a></li>
								</ul>
							</li>
							
							
					<?php
						}
						if($_SESSION['steamUser']->permissions->canApproveJobs ||$_SESSION['steamUser']->permissions->canEditUserStatus || $_SESSION['steamUser']->permissions->canEditVTCSettings )
						{
							?>
								<li>
								<a href="#" class="dropdown-toggle"><?php echo $lang["administration"]; ?></a>
								<ul  class="d-menu" data-role="dropdown">
									<?php
										if($_SESSION['steamUser']->permissions->canApproveJobs)
										{
											?>
												<li><a href="editJobs.php"><?php echo $lang['edit_jobs']; ?></a></li>
											<?php
										}
										
										if($_SESSION['steamUser']->permissions->canEditUserStatus)
										{
											?>
												<li><a href="editUserStatus.php"><?php echo $lang['edit_users']; ?></a></li>
											<?php
										}
										
										if($_SESSION['steamUser']->permissions->canEditVTCSettings)
										{
											?>
												<li><a href="editVTCSettings.php"><?php echo $lang['edit_VTC_settings']; ?></a></li>
											<?php
										}
										
							
									?>
								</ul>
							</li>
							<?php
						}
						?>
						<li><a href="logout.php">&nbsp; <?php echo $lang['logout']; ?> &nbsp;</a></li>
						<?php
						
					}
					?>
				</ul>
			</div>