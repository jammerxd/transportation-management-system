<?php
class Permissions
{
	public $canSubmitJobs;
	public $canApproveJobs;
	public $canEditUserStatus;
	public $canEditVTCSettings;


	public function __construct($_jobs=false,$_approve=false,$_users=false,$_vtcsettings=false)
	{
		$this->canSubmitJobs = $_jobs;
		$this->canApproveJobs = $_approve;
		$this->canEditUserStatus = $_users;
		$this->canEditVTCSettings = $_vtcsettings;
	}
}
?>