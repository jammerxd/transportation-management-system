<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');


if (!isset($_SESSION['steamUser']))
{
	header('Location: login.php');
	//echo "not logged in";
}
else if(isset($_POST["btnResetLoggerKey"]))
{	
	if($_POST["btnResetLoggerKey"] == "Reset")
	{
		$_SESSION['steamUser']->loggerKey = ResetLoggerKey($_SESSION['steamUser']->steamID);
		LogAction($_SESSION['steamUser']->steamID,"Logger Key Reset","A new logger key was generated for " .  $_SESSION['steamUser']->userName . ".", $_SESSION['steamUser']->userName);
	}
}

?>
<div class="align-center padding-10 full-height v-align-middle">	
<?php
if(!in_array($_SESSION['steamUser']->status,$statusesSignIn))
{
	if($_SESSION['steamUser']->status == "Pending")
	{
		
		?>
		<h3 class="fg-orange"><?php echo $lang['error']; ?></h3>
		<p class="fg-orange"><?php echo $lang['sits_user_Pending']; ?></p>
		<?php
	}
	else if($_SESSION['steamUser']->status == "Banned")
	{
		?>
		<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
		<p class="fg-red"><?php echo $lang['sits_user_Banned']; ?></p>
		<?php
	}
	else if($_SESSION['steamUser']->status == "Suspended")
	{
		?>
		<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
		<p class="fg-red"><?php echo $lang['sits_user_Suspended']; ?></p>
		<?php
	}
	else if($_SESSION['steamUser']->status == "Rejected")
	{
		?>
		<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
		<p class="fg-red"><?php echo $lang['sits_user_Rejected']; ?></p>
		<?php
	}
	else if($_SESSION['steamUser']->status == "In Active")
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
else
{
?>
	<img id="profilePIC" src="" alt="Profile PIC" class="img-center animated fadeIn "/>
	<br />
	<table class="table border bordered animated fadeIn" id="profile_table">
	<thead>
		<tr>
			<td colspan="2">Profile Information</td>
			
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="v-align-middle"><strong><?php echo $lang['steamID'] . ":";?></strong></td>
			<td class="v-align-middle"><?php echo $_SESSION['steamUser']->steamID; ?></td>
		</tr>
		<tr>
			<td class="v-align-middle"><strong><?php echo $lang['profile_username'];?></strong></td>
			<td class="v-align-middle"><?php echo $_SESSION['steamUser']->userName; ?></td>
		</tr>
		<tr>
			<td class="v-align-middle"><strong><?php echo $lang['profile_api_link']; ?> </strong></td>
			<td class="v-align-middle"><?php echo GetAPILink(); ?></td>
		</tr>
		
		<tr>
			<td class="v-align-middle"><strong><?php echo $lang['profile_logger_key']; ?> </strong></td>
			<td class="v-align-middle"><form action="profile.php" method="POST" name="loggerKey_reset"><p><?php echo $_SESSION['steamUser']->loggerKey ?></p><input type="submit" value="Reset" name="btnResetLoggerKey"></form></td>
			
		</tr>

		<tr>
			<td class="v-align-middle"><strong>ETCARS Download: <br />Version: 0.7</strong></td>
			<td class="v-align-middle"><a href="download.php?file=etcarsx86" class="button">x86</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="download.php?file=etcarsx64" class="button">x64</a></td>
			
		</tr>
		<tr>
			<td class="v-align-middle"><strong>Autologger Download: <br />Version: 0.0.0.7</strong></td>
			<td class="v-align-middle"><a href="download.php?file=auto" class="button">Download</a></td>
			
		</tr>
	</tbody>
	</table>
	<br />
	<br />
	<script type="text/javascript">
		document.getElementById("profilePIC").src = getGravatarURL();
	</script>
<?php
}
?>
</div>
<?php
include_once('footer.php');
?>