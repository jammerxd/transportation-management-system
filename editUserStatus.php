<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');
$hasError = '';
$updateSuccess = false;
if (!isset($_SESSION['steamUser']))
{
	header('Location: login.php');
}
else
{
	if (!$_SESSION['steamUser']->permissions->canEditUserStatus)
	{
		?>
			<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
			<p class="fg-red"><?php echo $lang['permission_denied']; ?></p>
		<?php
	}
	else
	{

		if(isset($_POST['updateUser']) && isset($_POST['steamID']) && isset($_POST['status']))
		{
			$up_steamID = $_POST['steamID'];
			$up_status = $_POST['status'];
			$up_submitJobs = 0;
			$up_approveJobs = 0;
			$up_editUserStatus = 0;
			$up_editVTC = 0;

			if(isset($_POST['canSubmitJobs']))
				$up_submitJobs = 1;
			

			if(isset($_POST['canApproveJobs']))
				$up_approveJobs = 1;
			if(isset($_POST['canEditUserStatus']))
				$up_editUserStatus = 1;
			if(isset($_POST['canEditVTCSettings']))
				$up_editVTC = 1;
			if($_POST['steamid']!= '76561198352265945' && $_POST['steamid'] != '76561198131380603')
			{
				//UpdateUser($steamID,$status,$canSubmit,$canApprove,$canEdit,$canVTC)
				if(UpdateUser($up_steamID,$up_status,$up_submitJobs,$up_approveJobs,$up_editUserStatus,$up_editVTC))
				{
					$updateSuccess = true;
				}
				else
				{
					$hasError = 'Failed to update user!';
				}
			}
			//var_dump($_POST);
		}
		//else
		//{
		?>
			

<script src="custom_js/VTCSettings_validate.js" type="text/javascript"></script>
<div class="center padding 10 full-height v-align-middle">
	<div class="container" id="userContainer">
		<?php
			if($hasError == '' && $updateSuccess == true)
			{
				?>
					<h4 class="fg-green h4"><b>User Updated</b></h4>
				<?php
			}
			else if($hasError != '' && $updateSuccess == false)
			{
				?>
					<h4 class="fg-red h4"><b>An error has ocurred: <?php echo $hasError; ?></b></h4>
				<?php
			}
		?>
		<table class="table dataTable  striped hovered border bordered" id="usersTable"  data-role="datatable"  data-searching="true">
			<thead align="center">
				<tr>
					<th align="center" style="text-align: center">Steam ID</th>
					<th align="center" style="text-align: center">Steam Username</th>
					<th align="center" style="text-align: center">Status</th>
					<th align="center" style="text-align: center">Can Submit Jobs</th>
					<th align="center" style="text-align: center">Can Approve Jobs</th>
					<th align="center" style="text-align: center">Can Edit Users</th>
					<th align="center" style="text-align: center">Can Edit VTC Settings</th>
					<th align="center" style="text-align: center">Update User</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
					foreach(GetAllSteamAccounts($_SESSION['steamUser']->steamID) as $tempUser)
					{
						echo "<tr>";
						echo "<form method=\"POST\" action=\"#\">";
						echo "<td>" . $tempUser->steamID . "</td>";
						echo "<td>" . $tempUser->userName . "</td>";
						echo "<td><div class=\"input-control select\">" . GetAllStatuses($tempUser->status) . "</div></td>";
						echo "<td>" . GetCheckBoxBool($tempUser->permissions->canSubmitJobs,"canSubmitJobs") . "</td>";
						echo "<td>" . GetCheckBoxBool($tempUser->permissions->canApproveJobs,"canApproveJobs") . "</td>";
						echo "<td>" . GetCheckBoxBool($tempUser->permissions->canEditUserStatus,"canEditUserStatus") . "</td>";
						echo "<td>" . GetCheckBoxBool($tempUser->permissions->canEditVTCSettings,"canEditVTCSettings") . "</td>";
						echo "<input type=\"hidden\" name=\"steamID\" id=\"steamID\" value=\"" . $tempUser->steamID . "\">";
						echo "<td><button class=\"button success\" type=\"submit\" value=\"updateUser\" name=\"updateUser\">Update User</button></td>";
						echo "</form>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>


<?php
		//}
	}
}
include_once('footer.php');
?>