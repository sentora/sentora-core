<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
class module_controller {

	static $deleteok;
	static $backupok;
	static $filenotexist;

    static function ListBackUps($userid) {
        $currentuser = ctrl_users::GetUserDetail($userid);
		$userid = $currentuser['userid'];
		$username = $currentuser['username'];
		$res = array();
		$dirFiles = array();
		$backupdir = ctrl_options::GetOption('hosted_dir') . $username . "/backups/"; 
		if ($handle = opendir($backupdir)) {
   			while (false !== ($file = readdir($handle))){
          		if ($file != "." && $file != ".." && stristr($file,"_") && substr($file, -4) == ".zip"){
					$dirFiles[] = $file;
          		}
       		}
		}
		closedir($handle);
		if (!fs_director::CheckForEmptyValue($dirFiles)){
			sort($dirFiles);
				foreach ($dirFiles as $file) {
          			$filesize = fs_director::ShowHumanFileSize(filesize($backupdir . $file));
					$splitfile = explode("_", $file);
					$filedate = $splitfile[1];
					//$filecreated = date("M-d-Y", mktime(0, 0, 0, substr($filedate, 2, -2), substr($filedate, 0, -4), substr($filedate, -2)));
					$filecreated = $splitfile[1];
					array_push($res, array('backupfile' => substr($file, 0, -4),
										   'created'    => $filecreated,
										   'filesize'   => $filesize));
				}
		}							   
        return $res;
    }
	
	static function ExecuteBackup($userid, $download=0){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail($userid);
		$username = $currentuser['username'];
		include('cnf/db.php');
		runtime_hook::Execute('OnBeforeCreateBackup');
		// Lets grab and archive the user's web data....
		$homedir =  ctrl_options::GetOption('hosted_dir') . $username;
		$backupname = $username . "_" . date("M-d-Y_hms", time());
		$dbstamp = date("dmy_Gi", time());		
		// We now see what the OS is before we work out what compression command to use..
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
    		$resault = exec(fs_director::SlashesToWin(ctrl_options::GetOption('zip_exe') . " a -tzip -y-r " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . $homedir . "/public_html"));
		} else {
    		$resault = exec(ctrl_options::GetOption('zip_exe') . " -r9 " . ctrl_options::GetOption('temp_dir') . $backupname . " " . $homedir . "/public_html/*");
    		@chmod(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", 0777);
		}		
		// Now lets backup all MySQL datbases for the user and add them to the archive...
		$sql = "SELECT COUNT(*) FROM x_mysql WHERE my_acc_fk = '" . $userid . "'";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() <> 0) {		
				$sql = $zdbh->prepare("SELECT * FROM x_mysql WHERE my_acc_fk=" . $userid . "");
				$sql->execute();		
			    while ($row_mysql = $sql->fetch()) {
			        $bkcommand = ctrl_options::GetOption('mysqldump_exe') . " -h " . $host . " -u " . $user . " -p" . $pass . " --no-create-db " . $row_mysql['my_name_vc'] . " > " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql";
			        passthru($bkcommand);
			        // Add it to the ZIP archive...
			        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
			            $resault = exec(fs_director::SlashesToWin(ctrl_options::GetOption('zip_exe') . " u " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql"));
			        } else {
			            $resault = exec(ctrl_options::GetOption('zip_exe') . " " . ctrl_options::GetOption('temp_dir') . $backupname . "  " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
			        }
			        unlink(ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
			    }
			}
		}
		// We have the backup now lets output it to disk or download
		if (file_exists(ctrl_options::GetOption('temp_dir') . $backupname . ".zip")){
		
			// If Disk based backups are allowed in backup config
			if (strtolower(ctrl_options::GetOption('disk_bu')) == "true"){
				// Copy Backup to user home directory...
				$backupdir = $homedir . "/backups/";
				if (!is_dir($backupdir)){
					mkdir($backupdir, 0777, TRUE);
				}
				copy(ctrl_options::GetOption('temp_dir') . $backupname . ".zip", $backupdir . $backupname . ".zip");
			} else {
				$backupdir = ctrl_options::GetOption('temp_dir');
			}	
			
			// If Client has checked to download file
			if ($download <> 0){
				if (sys_versions::ShowOSPlatformVersion() == "Windows") {
					# Now we send the output (Windows)...
					header('Pragma: public'); 
					header('Expires: 0');        
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');   
					header('Cache-Control: private',false);   
					header('Content-Type: application/zip');   
					header('Content-Disposition: attachment; filename='.$backupname.'.zip');   
					header('Content-Transfer-Encoding: binary');   
					header('Content-Length: '.filesize($backupdir . $backupname . '.zip ').''); 
					readfile($backupdir . $backupname . ".zip ");   
				} else {
					# Now we send the output (POSIX)...
					$file = $backupdir . $backupname . ".zip"; 
					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Cache-Control: private', false);
					header('Content-Description: File Transfer');
					header('Content-Transfer-Encoding: binary');
					header('Content-Type: application/force-download'); 
					header('Content-Length: ' . filesize($file)); 
					header('Content-Disposition: attachment; filename=' . $backupname . '.zip');  
					self::readfile_chunked($file);
				}		
			}
			unlink(ctrl_options::GetOption('temp_dir').$backupname. ".zip");
		} else {
			self::$filenotexist=true;
		}
		runtime_hook::Execute('OnAfterCreateBackup');
	}
	
	static function readfile_chunked($filename) { 
		$chunksize = 1*(1024*1024);
	 	$buffer = ''; 
	 	$handle = fopen($filename, 'rb'); 
	 	if ($handle === false) { 
	   		return false; 
	 	} 
	 	while (!feof($handle)) { 
	   		$buffer = fread($handle, $chunksize); 
	   		print $buffer; 
	 	} 
	 	return fclose($handle); 
	}
	
	static function ExecuteDeleteBackup($username, $file) {
		runtime_hook::Execute('OnBeforeDeleteBackup');
		$backup_file_to_delete = ctrl_options::GetOption('hosted_dir') . $username ."/backups/". $file .".zip";
		unlink($backup_file_to_delete);
		runtime_hook::Execute('OnAfterDeleteBackup');
	} 

	static function ExecuteCreateBackupDirectory($username){
		$backupdir = ctrl_options::GetOption('hosted_dir') . $username . "/backups/";
		if (!is_dir($backupdir)){
                    fs_director::CreateDirectory($backupdir);
		}
	
	}
	
	static function doBackup(){
		global $zdbh;
		global $controller;
		$userid = $controller->GetControllerRequest('FORM', 'inBackUp');
		$download = $controller->GetControllerRequest('FORM', 'inDownLoad');
		self::ExecuteBackup($userid, $download);
		self::$backupok = true;
		
	}

	static function doDeleteBackup(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$userid = $currentuser['userid'];
		$username = $currentuser['username'];
		$files = self::ListBackUps($userid);
		//print_r($_POST);
		foreach ($files as $file){
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '')) ||
				!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '_x'))||
				!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '_y'))){
				self::ExecuteDeleteBackup($username, $file['backupfile']);
				self::$deleteok = true;
			}
		}
	}

    static function GetBackUpList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListBackUps($currentuser['userid']);     
    }

    static function GetFileLocation() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();;
		$filelocation = $currentuser['username'] . "/backups/";
        return $filelocation;
    }

	static function getUserID() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
		$userid = $currentuser['userid'];
		return $userid;
    }

    static function GetDiskAllowed() {
        global $controller;
        if (strtolower(ctrl_options::GetOption('disk_bu')) == "true")
        	return true;
		return false;
    }
	
	static function getCreateBackupDirectory() {
		$currentuser = ctrl_users::GetUserDetail();
		if (self::ExecuteCreateBackupDirectory($currentuser['username']))
        	return true;
		return false;
    }

    static function GetBUOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT bus_value_tx FROM x_backup_settings WHERE bus_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['bus_value_tx'];
        } else {
            return false;
        }
    }
		
	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

	static function getModuleDesc() {
		$message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

    static function getModulePath() {
        global $controller;
        $module_path = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/";
        return $module_path;
    }

	static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$filenotexist)){
            return ui_sysmessage::shout("There was an error saving your backup!", "zannounceerror");
		}
        if (!fs_director::CheckForEmptyValue(self::$deleteok)){
            return ui_sysmessage::shout("Backup deleted successfully!", "zannounceok");
		}
        if (!fs_director::CheckForEmptyValue(self::$backupok)){
            return ui_sysmessage::shout("Backup completed successfully!", "zannounceok");
		}
        return;
    }
	
}

?>