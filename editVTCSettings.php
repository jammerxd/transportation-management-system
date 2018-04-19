<?php
include_once('header.php');
include_once('lang/index.php');
include_once('classes/SteamAuth.php');

if (!isset($_SESSION['steamUser']))
{
	header('Location: login.php');
}
else
{
	if (!$_SESSION['steamUser']->permissions->canEditVTCSettings)
	{
		?>
			<h3 class="fg-red"><?php echo $lang['error']; ?></h3>
			<p class="fg-red"><?php echo $lang['permission_denied']; ?></p>
		<?php
	}
	else
	{
		?>
			

<script src="custom_js/VTCSettings_validate.js" type="text/javascript"></script>
<div class="center padding 10 full-height v-align-middle">
	<?php
		if (isset($_POST['submitVTCSettings']) && isset($_POST['Save']))
		{
			try{
				$regStr = "true";
				if (!isset($_POST['VTCRegistrationOpen']))
				{
					$settings->VTCRegistrationOpen = false;
					$regStr = "false";
				}
				else
				{
					$settings->VTCRegistrationOpen = true;
					$regStr = "true";
				}
				
				$settings->VTCName = $_POST['VTCName'];
				$settings->VTCSlogan = $_POST['VTCSlogan'];
				$settings->VTCUrl = $_POST['VTCUrl'];
				$settings->VTCId = $_POST['VTCId'];
				//$settings->VTCRegistrationOpen = $_POST['VTCRegistrationOpen'];
				$settings->dbHost = $_POST['VTCDBHost'];
				$settings->dbUser = $_POST['VTCDBUser'];
				$settings->dbPassword = $_POST['VTCDBPassword'];
				$settings->dbName = $_POST['VTCDBName'];
				$settings->steamAPIKey = $_POST['VTCSteamAPIKey'];
				
				$settingsFile = fopen("settings.php", "w");
				fwrite($settingsFile, "<?php \n");
				fwrite($settingsFile, "include_once('classes/Settings.php'); \n");
				fwrite($settingsFile, "\n");
				fwrite($settingsFile, "function getSettings() \n");
				fwrite($settingsFile, "{ \n");
				fwrite($settingsFile, "\t" . '$settings' . " = new Settings(); \n");
				fwrite($settingsFile, "\t" . '$settings->VTCName' . " = \"" . $settings->VTCName .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->VTCSlogan' . " = \"" . $settings->VTCSlogan .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->VTCUrl' . " = \"" . $settings->VTCUrl .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->VTCId' . " = \"" . $settings->VTCId .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->VTCRegistrationOpen' . " = " . $regStr .  "; \n");
				fwrite($settingsFile, "\t" . '$settings->dbHost' . " = \"" . $settings->dbHost .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->dbUser' . " = \"" . $settings->dbUser .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->dbPassword' . " = \"" . $settings->dbPassword .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->dbName' . " = \"" . $settings->dbName .  "\"; \n");
				fwrite($settingsFile, "\t" . '$settings->steamAPIKey' . " = \"" . $settings->steamAPIKey .  "\"; \n");
				fwrite($settingsFile, "\t" . 'return $settings' . "; \n");
				fwrite($settingsFile, "} \n");
				fwrite($settingsFile, "?>");
				fclose($settingsFile);
				
				header("location: " . $settings->VTCUrl . "/" . "editVTCSettings.php?error=0");
				}
			catch (Exception $e)
			{
				header("location: " . $settings->VTCurl . "/editVTCSettings.php?error=" . $e->getMessage());
			} 
		}
		if(isset($_GET["error"]))
		{
			if($_GET["error"] != "" && $_GET["error"] != "0")
			{
				?>
				<h4 class="fg-red h4"><b>An error has ocurred: <?php echo $_GET["error"]; ?></b></h4>
				<?php
			}
		}
		?>
	<form method="POST" action="#" id="FormVTCsettings" name="FormVTCsettings" data-role="validator" data-on-error-input="notifyOnErrorInput" data-show-error-hint="false">
		<input type="text" id="submitVTCSettings" name="submitVTCSettings" value="true" style="display: none">
		<br /><br /><div class="accordion large-heading" data-role="accordion">
			<div class="frame active">
				<div class="heading">VTC Management</div>
				<div class="content">
					<table>
						<tbody>
							<tr>
								<td><?php echo $lang["VTCName"];?> &nbsp;</td>
								<td>
									<div class="input-control text" >
										<input type="text" name="VTCName" id="VTCName" value="<?php echo $settings->VTCName;?>" data-validate-func="required" data-validate-hint="VTC name cannot be empty!">
										<span class="input-state-error mif-warning"></span>
										<span class="input-state-success mif-checkmark"></span>
									</div>
								</td>
							</tr>
							
							<tr>
								<td><?php echo $lang["VTCSlogan"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCSlogan" id="VTCSlogan" value="<?php echo $settings->VTCSlogan;?>" data-validate-func="required" data-validate-hint="VTC slogan cannot be empty!">
										<span class="input-state-error mif-warning"></span>
										<span class="input-state-success mif-checkmark"></span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo $lang["VTCUrl"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCUrl" id="VTCUrl" value="<?php echo $settings->VTCUrl;?>" data-validate-func="required" data-validate-hint="VTC url cannot be empty!">
										<span class="input-state-error mif-warning"></span>
										<span class="input-state-success mif-checkmark"></span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo $lang["VTCId"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCId" id="VTCId" maxlength="3" value="<?php echo $settings->VTCId;?>" data-validate-func="required" data-validate-hint="VTC ID cannot be empty!">
										<span class="input-state-error mif-warning"></span>
										<span class="input-state-success mif-checkmark"></span>
									</div>
								</td>
							</tr>
							<tr>
								<td style="padding: 20px 0px 20px 20px;"><?php echo $lang["VTCRegistrationOpen"];?> &nbsp;</td>
								<td>
							
									<label class="switch">
									<input type="checkbox" name="VTCRegistrationOpen" id="VTCRegistrationOpen" <?php if ($settings->VTCRegistrationOpen == true) { echo "checked";}?>/>
									<span class="check"/>
									</label>
								</td>
							</tr>
							<tr>
								<td><button class="button" onclick='$("#FormVTCsettings")[0].reset()'>Reset</button>&nbsp;</td>
								<td>&nbsp;<input type="submit" value="Save" name="Save"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="frame active">
				<div class="heading">Database Connection</div>
				<div class="content">
					<table>
						<tbody>
							<tr>
								<td><?php echo $lang["VTCDBHost"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCDBHost" id="VTCDBHost" value="<?php echo $settings->dbHost;?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo $lang["VTCDBUser"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCDBUser" id="VTCDBUser" value="<?php echo $settings->dbUser;?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo $lang["VTCDBPassword"];?> &nbsp;</td>
								<td>
									<div class="input-control password">
										<input type="password" name="VTCDBPassword" id="VTCDBPassword" value="<?php echo $settings->dbPassword;?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo $lang["VTCDBName"];?> &nbsp;</td>
								<td>
									<div class="input-control text">
										<input type="text" name="VTCDBName" id="VTCDBName" value="<?php echo $settings->dbName;?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><button class="button" onclick='$("#FormVTCsettings")[0].reset()'>Reset</button>&nbsp;</td>
								<td>&nbsp;<input type="submit" value="Save" name="Save"></td>
							</tr>
						</tbody>
					</table>
						
				</div>
			</div>
			<div class="frame active">
				<div class="heading">Delivery Reporting</div>
				<div class="content">
					No Settings Yet!
				</div>
			</div>
			<div class="frame active">
				<div class="heading">Other Settings</div>
				<div class="content">
			
					<table>
						<tbody>
							<tr>
								<td><a href="https://steamcommunity.com/dev/apikey"><?php echo $lang["VTCSteamAPIKey"];?></a> &nbsp;</td>
								<td>
									<div class="input-control password">
										<input type="password" name="VTCSteamAPIKey" id="VTCSteamAPIKey" value="<?php echo $settings->steamAPIKey;?>">
									</div>
								</td>
		
							</tr>
							<tr>
								<td><button class="button" onclick='$("#FormVTCsettings")[0].reset()'>Reset</button>&nbsp;</td>
								<td>&nbsp;<input type="submit" value="Save" name="Save"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<br />
	</form>
</div>
<?php
		
	}
}
include_once('footer.php');
?>