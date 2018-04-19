<?php
require('header.php');
?>
<div class="padding10 no-margin">
	<h2><?php echo $lang['welcome_to']; ?> <strong class="fg-red"><?php echo $settings->VTCName; ?></strong></h1>
	<h4><?php echo $settings->VTCSlogan; ?></h4>
</div>


<div class="padding40 ">
	<h3><?php echo $lang['GameATS']; ?> <?php echo $lang['Quick_VTC_Statistics']; ?></h3>
		
	<div class="padding10">
		<ul class="bg-dark fg-grayLighter horizontal-menu" id="ATS_QUICK_VTC_STATS">
			<li>
				<h4><?php echo $lang['TotalDeliveries']; ?></h4>
				<p>0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalIncome']; ?></h4>
				<p>$ 0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalExpenses']; ?></h4>
				<p>$ 0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalDistanceDriven']; ?></h4>
				<p>0 Miles</p>
			</li>
		</ul>
	</div>
	<br />
	<hr />
	<br />
	<h3><?php echo $lang['GameETS']; ?> <?php echo $lang['Quick_VTC_Statistics']; ?></h3>
	<div class="padding10">
		<ul class="bg-dark fg-grayLighter horizontal-menu" id="ETS2_QUICK_VTC_STATS">
			<li>
				<h4><?php echo $lang['TotalDeliveries']; ?></h4>
				<p>0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalIncome']; ?></h4>
				<p>&euro; 0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalExpenses']; ?></h4>
				<p>&euro; 0</p>
			</li>
			<li>
				<h4><?php echo $lang['TotalDistanceDriven']; ?></h4>
				<p>0 Kilometers</p>
			</li>
		</ul>
	
	</div>
	<br />
	<hr />
	<br />
	<h3>Live Drivers</h3>
	<div class="padding10">
		<table class="table  striped hovered border bordered" id="liveDeliveriesTable">
			<thead align="center">
				<tr>
                    <th align="center" style="text-align: center">Job ID</th>
					<th align="center" style="text-align: center">Username</th>
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
				
					foreach(GetAllDeliveriesInProgress() as $tempJob)
					{
						echo "<tr>";
						echo "<td>" . $tempJob->jobID . "</td>";
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
</div>
<!--<div class="padding40">
<iframe allowfullscreen src="https://mixer.com/embed/player/392210" width="1920" height="1080"></iframe>
</div>-->

<?php
require('footer.php');
?>