<?php
$jobStatuses = ['4'=>'Finished','3'=>'In Progress','5'=>'Cancelled'];
class MinimalJob
{
    public $jobID;
	public $gameAbbreviation;
	public $status;//3,4,5
	public $sourceCity;
    public $destinationCity;
    public $cargo;
    public $truckMake;
    public $truckModel;
    public $income;
    public $trailerMass;
    public $damage;
    public $distanceDriven;
    public $fuelBurned;
    public $fuelPurchased;
    public $displayName;
    public $steamID;
	public function __construct($_jobID,$_gameAbbreviation,$_status,$_sourceCity,$_destinationCity,$_cargo,$_truckMake,$_truckModel,$_income,$_trailerMass,$_damage,$_distanceDriven,$_fuelBurned,$_fuelPurchased,$_displayName,$_steamID)
	{
        $this->jobID = $_jobID;
        $this->gameAbbreviation = $_gameAbbreviation;
        $this->status = $_status;
        $this->sourceCity = $_sourceCity;
        $this->destinationCity = $_destinationCity;
        $this->cargo = $_cargo;
        $this->truckMake = $_truckMake;
        $this->truckModel = $_truckModel;
        $this->income = $_income;
        $this->trailerMass = $_trailerMass;
        $this->damage = $_damage;
        $this->distanceDriven=$_distanceDriven;
        $this->fuelBurned=$_fuelBurned;
        $this->fuelPurchased=$_fuelPurchased;
        $this->displayName=$_displayName;
        $this->steamID=$_steamID;
	}
}
?>