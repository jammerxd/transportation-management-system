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
<center>
<div class="accordion large-heading" data-role="accordion">
	<div class="frame active" style="width: 80%;">
		<div class="heading"><?php echo $_SESSION['steamUser']->userName;?> Overall Stats</div>
			<div class="content">
				<table class="table sortable-markers-on-left striped hovered cell-hovered border bordered">
				    <thead>
				        <tr>
				            <th class="sortable-column">Total Deliveries</th>
				            <th class="sortable-column">Total Income</th>
				            <th class="sortable-column">Total Expenses</th>
				            <th class="sortable-column">Total Distance Driven</th>
				        </tr>
				    </thead>
				</table>
			</div>
		</div>
	</div>
</center>
<?php
}
?>
</div>
<?php
include_once('footer.php');
?>