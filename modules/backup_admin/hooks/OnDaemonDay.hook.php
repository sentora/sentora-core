<?php
include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;
try {	
	$zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
	
}
		
// Get backup settings...
$rowconfig = $zdbh->query("SELECT * FROM x_backup_settings WHERE bus_name_vc='schedule_bu'")->fetch();

// Schedule daily backups are enabled...
if (strtolower($rowconfig['bus_value_tx']) == "true"){
	runtime_hook::Execute('OnBeforeScheduleBackup');
	echo "\r\nBackup Scheduling enabled - Backing up all enabled client files now...\r\n";
	// Get all accounts
	$bsql = "SELECT * FROM x_accounts WHERE ac_enabled_in=1 AND ac_deleted_ts IS NULL";
    $numrows = $zdbh->query($bsql);
    if ($numrows->fetchColumn() <> 0) {
    	$bsql = $zdbh->prepare($bsql);
        $bsql->execute();
        while ($rowclients = $bsql->fetch()) {
			// User loop
			$username   = $rowclients['ac_user_vc'];
			$userid     = $rowclients['ac_id_pk'];
			$homedir    = ctrl_options::GetOption('hosted_dir') . $username;
			$backupname = $username . "_" . date("M-d-Y_hms", time());
			$dbstamp    = date("dmy_Gi", time());		
			// We now see what the OS is before we work out what compression command to use..
			if (sys_versions::ShowOSPlatformVersion() == "Windows") {
	    		$result = exec(fs_director::SlashesToWin(ctrl_options::GetOption('7z_exe') . " a -tzip -y-r " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . $homedir . "/public_html"));
			} else {
	    		$result = exec(ctrl_options::GetOption('7z_exe') . " -r9 " . ctrl_options::GetOption('temp_dir') . $backupname . " " . $homedir . "/public_html/*");
	    		@chmod(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", 0777);
			}
			// Now lets backup all MySQL datbases for the user and add them to the archive...
			$msql = "SELECT COUNT(*) FROM x_mysql WHERE my_acc_fk = '" . $userid . "'";
			if ($numrows = $zdbh->query($msql)) {
	 			if ($numrows->fetchColumn() <> 0) {		
					$msql = $zdbh->prepare("SELECT * FROM x_mysql WHERE my_acc_fk=" . $userid . "");
					$msql->execute();		
				    while ($row_mysql = $msql->fetch()) {
				        $bkcommand = ctrl_options::GetOption('mysqldump_exe') . " -h " . $host . " -u " . $user . " -p" . $pass . " --no-create-db " . $row_mysql['my_name_vc'] . " > " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql";
				        passthru($bkcommand);
				        // Add it to the ZIP archive...
				        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
				            $result = exec(fs_director::SlashesToWin(ctrl_options::GetOption('7z_exe') . " u " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql"));
				        } else {
				            $result = exec(ctrl_options::GetOption('7z_exe') . " " . ctrl_options::GetOption('temp_dir') . $backupname . "  " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
				        }
				        unlink(ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
				    }
				}
			}		
			// We have the backup now lets output it to disk or download
			if (file_exists(ctrl_options::GetOption('temp_dir') . $backupname . ".zip")){
				// Copy Backup to user home directory...
				$backupdir = $homedir . "/backups/";
				if (!is_dir($backupdir)){
					mkdir($backupdir, 0777, TRUE);
				}
				copy(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", $backupdir . $backupname . ".zip");
				unlink(ctrl_options::GetOption('temp_dir').$backupname. ".zip");
				echo $backupdir . $backupname . ".zip\r\n";
			}
        }
    }
	runtime_hook::Execute('OnAfterScheduleBackup');
	echo "Backup Schedule COMPLETE...\r\n";
}


// Get backup settings...
$rowconfig = $zdbh->query("SELECT * FROM x_backup_settings WHERE bus_name_vc='purge_bu'")->fetch();
$rowconfig_date = $zdbh->query("SELECT * FROM x_backup_settings WHERE bus_name_vc='purge_date'")->fetch();
// Purge backups are enabled....
if (strtolower($rowconfig['bus_value_tx']) == "true"){
	echo "\r\nBackup Purging enabled - Purging old backups now...\r\n";
	runtime_hook::Execute('OnBeforePurgeBackup');
	// Get all accounts
	$bsql = "SELECT * FROM x_accounts WHERE ac_enabled_in=1 AND ac_deleted_ts IS NULL";
    $numrows = $zdbh->query($bsql);
    if ($numrows->fetchColumn() <> 0) {
		$purge_date = $rowconfig_date['bus_value_tx'];
    	$bsql = $zdbh->prepare($bsql);
        $bsql->execute();
		echo "[FILE][PURGE_DATE][FILE_DATE][ACTION]\r\n";
        while ($rowclients = $bsql->fetch()) {
			$username   = $rowclients['ac_user_vc'];
			$backupdir  = ctrl_options::GetOption('hosted_dir') . $username . "/backups/"; 
			if ($handle = opendir($backupdir)) {
	   			while (false !== ($file = readdir($handle))){
	          		if ($file != "." && $file != ".."){
						$filetime = @filemtime($backupdir . $file);
						if($filetime == NULL){
    						$filetime = @filemtime(utf8_decode($backupdir . $file));
						} 
						$filetime = floor((time() - $filetime)/86400);
						echo "" . $file . " - " . $purge_date ." - " . $filetime . "";
						if ($purge_date < $filetime){
							//delete the file
							echo " - Deleting file...\r\n";
							unlink($backupdir . $file);
						} else {
							echo " - Skipping file...\r\n";
						}
    	      		}
	       		}
			}
		}
	}
	echo "Backup Purging COMPLETE...\r\n";
	runtime_hook::Execute('OnAfterPurgeBackup');
}

?>