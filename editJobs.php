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
	if (!$_SESSION['steamUser']->permissions->canApproveJobs)
	{
		?>
			<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
			<p class="fg-red"><?php echo $lang['permission_denied']; ?></p>
		<?php
	}
	else
	{

		if(isset($_POST['updateJob']) && isset($_POST['jobID']) && isset($_POST['status']))
		{
			/*$up_steamID = $_POST['steamID'];
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
			//UpdateUser($steamID,$status,$canSubmit,$canApprove,$canEdit,$canVTC)
			if(UpdateUser($up_steamID,$up_status,$up_submitJobs,$up_approveJobs,$up_editUserStatus,$up_editVTC))
			{
				$updateSuccess = true;
			}
			else
			{
				$hasError = 'Failed to update user!';
			}
			//var_dump($_POST);*/
		}
		//else
		//{
		?>
			

<script src="custom_js/VTCSettings_validate.js" type="text/javascript"></script>
<div class="container">


	

	<table class="table dataTable striped hovered border bordered" id="deliveriesTable" data-role="datatable"  data-searching="true">
		<thead align="center">
			<tr>
				<th align="center" style="text-align: center">Job ID</th>
				<th align="center" style="text-align: center">Steam ID</th>
				<th align="center" stlye="text-align: center">Username</th>
				<th align="center" style="text-align: center">Game</th>
				<th align="center" style="text-align: center">Source City</th>
				<th align="center" style="text-align: center">Destination City</th>
				
				<th align="center" style="text-align: center">Cargo</th>
				<th align="center" style="text-align: center">Est. Income</th>
				<th align="center" style="text-align: center">Distance Driven</th>
				<th align="center" style="text-align: center">Fuel Burned</th>
				<th align="center" style="text-align: center">Fuel Purchased</th>
				<th align="center" style="text-align: center">Damage</th>
				<th align="center" style="text-align: center">Status</th>
			</tr>
		</thead>
		<tbody>
			<?php
			
				foreach(GetAllDeliveries() as $tempJob)
				{
					echo "<tr>";
					echo "<td><a href=viewJobDetails.php?id=" . $tempJob->jobID . ">" . $tempJob->jobID . "</a></td>";
					echo "<td>" . $tempJob->steamID . "</td>";
					echo "<td>" . $tempJob->displayName . "</td>";
					echo "<td>" . strtoupper($tempJob->gameAbbreviation) . "</td>";
					echo "<td>" . $tempJob->sourceCity . "</td>";
					echo "<td>" . $tempJob->destinationCity . "</td>";
					echo "<td>" . $tempJob->cargo . "</td>";
					echo "<td>";  
					if(strcmp($tempJob->gameAbbreviation,"ats")===0)  
						echo "&#36;";
					else
						echo "&euro;"; 
					echo number_format(round($tempJob->income,2),2) . "</td>";
					echo "<td>" ;
					
					if(strcmp($tempJob->gameAbbreviation,"ats")===0)  
						echo   number_format(round($tempJob->distanceDriven*0.621371,2),2) . "  MI";
					else
						echo number_format(round($tempJob->distanceDriven,2),2) . " KM"; 

					echo "</td>";
					
					
					if(strcmp($tempJob->gameAbbreviation,"ats")===0)  
					{
						echo "<td>" . number_format(round($tempJob->fuelBurned*0.264172,2),2) ;
						echo " GL";
					}
					else
					{
						echo "<td>" . number_format(round($tempJob->fuelBurned,2),2) ;
						echo " L"; 
					}
					echo "</td>";
					
					
					if(strcmp($tempJob->gameAbbreviation,"ats")===0) 
					{
						echo "<td>" . number_format(round($tempJob->fuelPurchased*0.264172,2),2) ; 
						echo " GL";
					}
					else
					{
						echo "<td>" . number_format(round($tempJob->fuelPurchased,2),2) ;
						echo " L"; 
					}
					echo "</td>";
					 echo "<td>" . number_format(round($tempJob->damage*100,2),2) . "%</td>";
					echo "<td>" . $jobStatuses[$tempJob->status] . "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>
<?php
		//}
	}
}
include_once('footer.php');
?>