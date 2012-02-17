<?php

include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;
try {
    $zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

echo "START Backup Config." . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('Backup Config')){
echo "Backup Config module ENABLED..." . fs_filehandler::NewLine();

// Schedule daily backups are enabled...
if (strtolower(ctrl_options::GetOption('schedule_bu')) == "true") {
    runtime_hook::Execute('OnBeforeScheduleBackup');
    echo "Backup Scheduling enabled - Backing up all enabled client files now..." . fs_filehandler::NewLine();
    // Get all accounts
    $bsql = "SELECT * FROM x_accounts WHERE ac_enabled_in=1 AND ac_deleted_ts IS NULL";
    $numrows = $zdbh->query($bsql);
    if ($numrows->fetchColumn() <> 0) {
        $bsql = $zdbh->prepare($bsql);
        $bsql->execute();
        while ($rowclients = $bsql->fetch()) {
            // User loop
            $username = $rowclients['ac_user_vc'];
            $userid = $rowclients['ac_id_pk'];
            $homedir = ctrl_options::GetOption('hosted_dir') . $username;
            $backupname = $username . "_" . date("M-d-Y_hms", time());
            $dbstamp = date("dmy_Gi", time());
            // We now see what the OS is before we work out what compression command to use..
            if (sys_versions::ShowOSPlatformVersion() == "Windows") {
                $result = exec(fs_director::SlashesToWin(ctrl_options::GetOption('zip_exe') . " a -tzip -y-r " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . $homedir . "/public_html"));
            } else {
                $result = exec(ctrl_options::GetOption('zip_exe') . " -r9 " . ctrl_options::GetOption('temp_dir') . $backupname . " " . $homedir . "/public_html/*");
                fs_director::SetDirectoryPermissions(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", 0777);
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
                            $result = exec(fs_director::SlashesToWin(ctrl_options::GetOption('zip_exe') . " u " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql"));
                        } else {
                            $result = exec(ctrl_options::GetOption('zip_exe') . " " . ctrl_options::GetOption('temp_dir') . $backupname . "  " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
                        }
                        unlink(ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
                    }
                }
            }
            // We have the backup now lets output it to disk or download
            if (file_exists(ctrl_options::GetOption('temp_dir') . $backupname . ".zip")) {
                // Copy Backup to user home directory...
                $backupdir = $homedir . "/backups/";
                if (!is_dir($backupdir)) {
                    mkdir($backupdir, 0777, TRUE);
                }
                copy(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", $backupdir . $backupname . ".zip");
                unlink(ctrl_options::GetOption('temp_dir') . $backupname . ".zip");
                fs_director::SetDirectoryPermissions($backupdir . $backupname . ".zip", 0777);
                echo $backupdir . $backupname . ".zip" . fs_filehandler::NewLine();
            }
        }
    }
    runtime_hook::Execute('OnAfterScheduleBackup');
    echo "Backup Schedule COMPLETE..." . fs_filehandler::NewLine();
}

// Purge backups are enabled....
if (strtolower(ctrl_options::GetOption('purge_bu')) == "true") {
    echo fs_filehandler::NewLine() . "Backup Purging enabled - Purging backups older than ".ctrl_options::GetOption('purge_date')." days..." . fs_filehandler::NewLine();
    runtime_hook::Execute('OnBeforePurgeBackup');
    clearstatcache();
    // Get all accounts
    $bsql = "SELECT * FROM x_accounts WHERE ac_enabled_in=1 AND ac_deleted_ts IS NULL";
    $numrows = $zdbh->query($bsql);
    if ($numrows->fetchColumn() <> 0) {
        $purge_date = ctrl_options::GetOption('purge_date');
        $bsql = $zdbh->prepare($bsql);
        $bsql->execute();
        echo "[FILE][PURGE_DATE][FILE_DATE][ACTION]" . fs_filehandler::NewLine();
        while ($rowclients = $bsql->fetch()) {
            $username = $rowclients['ac_user_vc'];
            $backupdir = ctrl_options::GetOption('hosted_dir') . $username . "/backups/";
            if ($handle = @opendir($backupdir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $filetime = @filemtime($backupdir . $file);
                        if ($filetime == NULL) {
                            $filetime = @filemtime(utf8_decode($backupdir . $file));
                        }
                        $filetime = floor((time() - $filetime) / 86400);
                        echo "" . $file . " - " . $purge_date . " - " . $filetime . "";
                        if ($purge_date < $filetime) {
                            //delete the file
                            echo " - Deleting file..." . fs_filehandler::NewLine();
                            unlink($backupdir . $file);
                        } else {
                            echo " - Skipping file..." . fs_filehandler::NewLine();
                        }
                    }
                }
            }
        }
    }
    echo "Backup Purging COMPLETE..." . fs_filehandler::NewLine();
    runtime_hook::Execute('OnAfterPurgeBackup');
}



} else {
echo "Backup Config module DISABLED...nothing to do." . fs_filehandler::NewLine();
}
echo "END Backup Config." . fs_filehandler::NewLine();
?>