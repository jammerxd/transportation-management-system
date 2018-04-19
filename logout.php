<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');
?>
<div class="center padding 10 full-height v-align-middle">
<?php
	unset($_SESSION['steamUser']);
	if(!isset($_SESSION['steamUser']))
	{
		?>
			<p class="success"><?php echo $lang['sits_logout_success'];?></p>
		<?php
	}
	else
	{
		?>
			<p class="error"><?php echo $lang['sits_logout_failed'];?></p>
		<?php
	}
	header('Refresh: 3; url=index.php');
?>
</div>
<?php
include_once('footer.php');
?>