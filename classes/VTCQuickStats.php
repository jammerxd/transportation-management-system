<?php
class VTCQuickStats
{
	public $totalATSDeliveries;
	public $totalETS2Deliveries;
	public $totalATSIncome;
	public $totalETS2Income;
	public $totalATSExpenses;
	public $totalETS2Expenses;
	public $totalATSDistanceDriven;
	public $totalETS2DistanceDriven;
	
	public function __construct()
	{
		$this->totalATSDeliveries = 0;
		$this->totalETS2Deliveries = 0;
		$this->totalATSIncome = 0.00;
		$this->totalETS2Income = 0.00;
		$this->totalATSExpenses = 0.00;
		$this->totalETS2Expenses = 0.00;
		$this->totalATSDistanceDriven = 0.00;
		$this->totalETS2DistanceDriven = 0.00;
	}
}
?>