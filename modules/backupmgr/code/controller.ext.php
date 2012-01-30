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

    static $hasupdated;

    static function ExecuteBackup($userid, $download=0) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($userid);
        $username = $currentuser['username'];
        include('cnf/db.php');
        // Lets grab and archive the user's web data....
        $homedir = ctrl_options::GetOption('hosted_dir') . $username;
        $backupname = $username . "_" . date("dmy_Gi", time());
        $dbstamp = date("dmy_Gi", time());
        // We now see what the OS is before we work out what compression command to use..
        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
            $resault = exec(fs_director::SlashesToWin(ctrl_options::GetOption('7z_exe') . " a -tzip -y-r " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . $homedir . ""));
        } else {
            $resault = exec(ctrl_options::GetOption('7z_exe') . " -r9 " . ctrl_options::GetOption('temp_dir') . $backupname . " " . $homedir . "/*");
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
                        $resault = exec(fs_director::SlashesToWin(ctrl_options::GetOption('7z_exe') . " u " . ctrl_options::GetOption('temp_dir') . $backupname . ".zip " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql"));
                    } else {
                        $resault = exec(ctrl_options::GetOption('7z_exe') . " " . ctrl_options::GetOption('temp_dir') . $backupname . "  " . ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
                    }
                    unlink(ctrl_options::GetOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
                }
            }
        }
        // Copy Backup to user home directory...
        $backupdir = ctrl_options::GetOption('hosted_dir') . $username . "/backups/";
        if (!file_exists($backupdir)) {
            fs_director::CreateDirectory($backupdir);
        }
        copy(ctrl_options::GetOption('temp_dir') . $backupname . ".zip ", $backupdir . $backupname . ".zip");
        unlink(ctrl_options::GetOption('temp_dir') . $backupname . ".zip ");
        // If Client has checked to download file
        if ($download <> 0) {
            if (sys_versions::ShowOSPlatformVersion() == "Windows") {
                # Now we send the output (Windows)...
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename=' . $backupname . '.zip');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($backupdir . $backupname . '.zip ') . '');
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
    }

    static function readfile_chunked($filename) {
        $chunksize = 1 * (1024 * 1024);
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

    static function ListBackUps($userid) {
        $currentuser = ctrl_users::GetUserDetail($userid);
        $userid = $currentuser['userid'];
        $username = $currentuser['username'];
        $backupdir = ctrl_options::GetOption('hosted_dir') . $username . "/backups/";
        $res = array();
        if (!file_exists(ctrl_options::GetOption('hosted_dir') . $username . "/backups/")) {
            fs_director::CreateDirectory(ctrl_options::GetOption('hosted_dir') . $username . "/backups/");
        }
        if ($handle = opendir($backupdir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, -4) == ".zip") {
                    //echo $file;
                    array_push($res, array('backupfile' => $file));
                }
            }
        }
        closedir($handle);
        return $res;
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$hasupdated)) {
            return ui_sysmessage::shout("Backup completed successfully!");
        }
        return;
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

    static function getUserID() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $userid = $currentuser['userid'];
        return $userid;
    }

    static function getModuleDesc() {
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
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

    static function GetBackUpList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListBackUps($currentuser['userid']);
    }

    static function doBackup() {
        global $zdbh;
        global $controller;
        $userid = $controller->GetControllerRequest('FORM', 'inBackUp');
        $download = $controller->GetControllerRequest('FORM', 'inDownLoad');
        self::ExecuteBackup($userid, $download);
    }

}

?>
