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
<script src="custom_js/VTCSettings_validate.js" type="text/javascript"></script>
	<div class="container">
	
		

		<table class="table dataTable striped hovered border bordered" id="deliveriesTable"  data-role="datatable"  data-searching="true">
			<thead align="center">
				<tr>
                    <th align="center" style="text-align: center">Job ID</th>
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
				
					foreach(GetAllDeliveriesByUser($_SESSION['steamUser']->steamID) as $tempJob)
					{
						echo "<tr>";
						echo "<td><a href=viewJobDetails.php?id=" . $tempJob->jobID . ">" . $tempJob->jobID . "</a></td>";
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
</div>


<?php
		//}
	}

include_once('footer.php');
?>