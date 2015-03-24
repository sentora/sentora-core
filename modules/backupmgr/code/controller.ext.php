<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
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
class module_controller extends ctrl_module
{

    static $deleteok;
    static $backupok;
    static $filenotexist;

    static function ListBackUps($userid)
    {
        $currentuser = ctrl_users::GetUserDetail($userid);
        $userid = $currentuser['userid'];
        $username = $currentuser['username'];
        $res = array();
        $dirFiles = array();
        $backupdir = ctrl_options::GetSystemOption('hosted_dir') . $username . "/backups/";
        if ($handle = opendir($backupdir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && stristr($file, "_") && substr($file, -4) == ".zip") {
                    $dirFiles[] = $file;
                }
            }
        }
        closedir($handle);
        if (!fs_director::CheckForEmptyValue($dirFiles)) {
            sort($dirFiles);
            foreach ($dirFiles as $file) {
                $filesize = fs_director::ShowHumanFileSize(filesize($backupdir . $file));
                $filedate = date("F d Y H:i:s", filemtime($backupdir . $file));
                array_push($res, array('backupfile' => substr($file, 0, -4),
                    'created' => $filedate,
                    'filesize' => $filesize));
            }
        }
        self::array_sort_by_column($res, 'created');
        return $res;
    }

    static function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
    {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }

    static function CheckHasData($userid)
    {
        $currentuser = ctrl_users::GetUserDetail($userid);
        $datafolder = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/public_html/";
        $dirFiles = array();
        if ($handle = opendir($datafolder)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $dirFiles[] = $file;
                }
            }
        }
        closedir($handle);
        if (!fs_director::CheckForEmptyValue($dirFiles)) {
            return true;
        }
        return false;
    }

    static function ExecuteBackup($userid, $download = 0)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($userid);
        runtime_hook::Execute('OnBeforeCreateBackup');

        runtime_hook::Execute('OnAfterCreateBackup');
    }

    static function readfile_chunked($filename)
    {
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

    static function ExecuteDeleteBackup($username, $file)
    {
        runtime_hook::Execute('OnBeforeDeleteBackup');
        $backup_file_to_delete = ctrl_options::GetSystemOption('hosted_dir') . $username . "/backups/" . $file . ".zip";
        unlink($backup_file_to_delete);
        runtime_hook::Execute('OnAfterDeleteBackup');
    }

    static function ExecuteCreateBackupDirectory($username)
    {
        $backupdir = ctrl_options::GetSystemOption('hosted_dir') . $username . "/backups/";
        if (!is_dir($backupdir)) {
            fs_director::CreateDirectory($backupdir);
        }
    }

    static function CheckPurgeDate()
    {
        if (strtolower(ctrl_options::GetSystemOption('purge_bu')) == "true") {
            return ctrl_options::GetSystemOption('purge_date');
        } else {
            return false;
        }
    }

    static function doBackup()
    {
        global $zdbh;
        global $controller;
        $userid = $controller->GetControllerRequest('FORM', 'inBackUp');
        $download = $controller->GetControllerRequest('FORM', 'inDownLoad');
        self::ExecuteBackup($userid, $download);
        self::$backupok = true;
    }

    static function doDeleteBackup()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $userid = $currentuser['userid'];
        $username = $currentuser['username'];
        $files = self::ListBackUps($userid);
        //print_r($_POST);
        foreach ($files as $file) {
            if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '')) ||
                    !fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '_x')) ||
                    !fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $file['backupfile'] . '_y'))) {
                self::ExecuteDeleteBackup($username, $file['backupfile']);
                self::$deleteok = true;
            }
        }
    }

    static function GetHasData()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::CheckHasData($currentuser['userid']);
    }

    static function GetBackUpList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListBackUps($currentuser['userid']);
    }

    static function GetFileLocation()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $filelocation = $currentuser['username'] . "/backups/";
        return $filelocation;
    }

    static function getUserID()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $userid = $currentuser['userid'];
        return $userid;
    }

    static function GetDiskAllowed()
    {
        global $controller;
        if (strtolower(ctrl_options::GetSystemOption('disk_bu')) == "true")
            return true;
        return false;
    }

    static function GetPurgeDate()
    {
        return self::CheckPurgeDate();
    }

    static function getCreateBackupDirectory()
    {
        $currentuser = ctrl_users::GetUserDetail();
        if (self::ExecuteCreateBackupDirectory($currentuser['username']))
            return true;
        return false;
    }

    static function GetBUOption($name)
    {
        global $zdbh;
        // $result = $zdbh->query("SELECT bus_value_tx FROM x_backup_settings WHERE bus_name_vc = '$name'")->Fetch();
        $sql = $zdbh->prepare("SELECT bus_value_tx FROM x_backup_settings WHERE bus_name_vc = :name");
        $sql->bindParam(':name', $name);
        $sql->execute();
        $result = $sql->fetch();
        if ($result) {
            return $result['bus_value_tx'];
        } else {
            return false;
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$filenotexist)) {
            return ui_sysmessage::shout("There was an error saving your backup!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$deleteok)) {
            return ui_sysmessage::shout("Backup deleted successfully!", "zannounceok");
        }
        if (!fs_director::CheckForEmptyValue(self::$backupok)) {
            return ui_sysmessage::shout("Backup completed successfully!", "zannounceok");
        }
        return;
    }

}
