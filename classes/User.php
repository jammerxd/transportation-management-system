<?php
include_once("Permissions.php");
include_once("Statistics.php");
include_once("TMPBan.php");
class User
{
	public $steamID;
	public $userName;
	public $status;//Driver, Pending, Probationary Driver, Senior Driver, Banned, Suspended, Rejected, LOA
	public $permissions;
	public $ATSStats;
	public $ETS2Stats;
	public $TMPBans;
	public $loggerKey;
	public $userid;
	public function __construct()
	{
		$this->steamID = "";
		$this->userName = "";
		$this->permissions = new Permissions();
		$this->ATSStats = new Statistics();
		$this->ETS2Stats = new Statistics();
		$this->status="Banned";
		$this->TMPBans = array();
		$this->loggerKey = null;
		$this->userid = null;
	}
}

class MinimalUser
{
	public $steamID;
	public $userName;
	public $status;//Driver, Pending, Probationary Driver, Senior Driver, Banned, Suspended, Rejected, LOA
	public $permissions;
	public $userid;
	public function __construct($_id,$_userName,$_status,$_permissions)
	{
		$this->steamID = $_id;
		$this->userName = $_userName;
		$this->permissions = $_permissions;
		$this->status=$_status;
	}
}
?>