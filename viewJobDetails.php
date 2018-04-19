<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');


if (!isset($_SESSION['steamUser']))
{
	header('Location: login.php');
	//echo "not logged in";
}

?>
<div class="align-center no-padding full-height v-align-middle">	

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

<?php
}
?>
</div>
<?php
include_once('footer.php');
?>