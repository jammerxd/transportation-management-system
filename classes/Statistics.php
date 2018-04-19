<?php
class Statistics
{
	public $totalJobs;
	public $totalSuccessfulJobs;
	public $totalCancelledJobs;
	public $totalRejectedJobs;
	
	public $avgIncome;
	public $avgLitresBurned;
	public $avgDistanceDriven;
	public $avgExpenses;
	
	public $avgDamage;
	public $avgDamageCost;
	public $avgFuelCost;
	public $avgIncomePerDistance;
	public function __construct()
	{
		$this->totalJobs = 0;
		$this->totalSuccessfulJobs = 0;
		$this->totalCancelledJobs = 0;
		$this->totalRejectedJobs = 0;

		$this->avgIncome = 0.00;
		$this->avgLitresBurned = 0.00;
		$this->avgDistanceDriven = 0.00;
		$this->avgExpenses = 0.00;

		$this->avgDamage = 0.00;
		$this->avgDamageCost = 0.00;
		$this->avgFuelCost = 0.00;
		$this->avgIncomePerDistance = 0.00;
	}
}
?>