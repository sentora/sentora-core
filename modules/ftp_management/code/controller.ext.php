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

    static $error;
    static $alreadyexists;
    static $blank;
    static $badname;
    static $invalidPath;
    static $ok;
    static $delete;
    static $reset;
    static $create;

    /**
     * The 'worker' methods.
     */
    static function ListClients($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_ftpaccounts WHERE ft_acc_fk=:userid AND ft_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':userid', $uid);
            $sql->execute();
            while ($rowclients = $sql->fetch()) {
                $res[] = array('id' => $rowclients['ft_id_pk'],
                    'directory' => runtime_xss::xssClean($rowclients['ft_directory_vc']),
                    'access' => runtime_xss::xssClean($rowclients['ft_access_vc']),
                    'password' => runtime_xss::xssClean($rowclients['ft_password_vc']),
                    'username' => runtime_xss::xssClean($rowclients['ft_user_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentClient($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_ftpaccounts WHERE ft_id_pk=:userid AND ft_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $uid);
            $res = array();
            $sql->execute();
            while ($rowclients = $sql->fetch()) {
                $res[] = array('id' => $rowclients['ft_id_pk'],
                    'directory' => runtime_xss::xssClean($rowclients['ft_directory_vc']),
                    'access' => runtime_xss::xssClean($rowclients['ft_access_vc']),
                    'password' => runtime_xss::xssClean($rowclients['ft_password_vc']),
                    'username' => runtime_xss::xssClean($rowclients['ft_user_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListMasterDirs($uid)
    {
        $currentuser = ctrl_users::GetUserDetail($uid);
        $res = array();
        $handle = @opendir(ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "");
        $chkdir = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/";
        if (!$handle) {
            // Log an error as the folder cannot be opened...
        } else {
            while ($file = @readdir($handle)) {
                if ($file != '.' && $file != '..' && $file != '_errorpages') {
                    if (is_dir($chkdir . $file)) {
                        $res[] = array('domains' => runtime_xss::xssClean($file));
                    }
                }
            }
            closedir($handle);
        }
        return $res;
    }

    static function ListDomainDirs($uid)
    {
        $currentuser = ctrl_users::GetUserDetail($uid);
        $res = array();
        $handle = @opendir(ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/public_html");
        $chkdir = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/public_html/";
        if (!$handle) {
            // Log an error as the folder cannot be opened...
        } else {
            while ($file = @readdir($handle)) {
                if ($file != "." && $file != ".." && $file != "_errorpages") {
                    if (is_dir($chkdir . $file)) {
                        $res[] = array('domains' => runtime_xss::xssClean($file));
                    }
                }
            }
            closedir($handle);
        }
        return $res;
    }

    static function ExecuteResetPassword($ft_id_pk, $password)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeResetFTPPassword');
        $rowftpsql = "SELECT * FROM x_ftpaccounts WHERE ft_id_pk=:ftIdPk";
        $rowftpfind = $zdbh->prepare($rowftpsql);
        $rowftpfind->bindParam(':ftIdPk', $ft_id_pk);
        $rowftpfind->execute();
        $rowftp = $rowftpfind->fetch();

        $sql = $zdbh->prepare("UPDATE x_ftpaccounts SET ft_password_vc=:password WHERE ft_id_pk=:ftpid");
        $sql->bindParam(':password', $password);
        $sql->bindParam(':ftpid', $ft_id_pk);
        $sql->execute();

        self::$reset = true;
        // Include FTP server specific file here.
        $FtpModuleFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('ftp_php');
        if (file_exists($FtpModuleFile)) {
            include($FtpModuleFile);
        }
        $retval = TRUE;
        runtime_hook::Execute('OnAfterResetFTPPassword');
        return $retval;
    }

    static function ExecuteCreateFTP($uid, $username, $password, $destination, $domainDestination, $access_type, $home)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        runtime_hook::Execute('OnBeforeCreateFTPAccount');
        if (fs_director::CheckForEmptyValue(self::CheckForErrors($username, $password))) {
            // Check to see if its a new home directory or use a current one...
            if ($home == 1) {
                $homedirectory_to_use = '/' . str_replace('.', '_', $username);
                $full_path = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . $homedirectory_to_use . '/';
                // Create the new home directory... (If it doesnt already exist.)
                if (!file_exists($full_path)) {
                    @mkdir($full_path, 777);
                    @chmod($full_path, 0777);
                }
            } else if ($home == 3) {
                $homedirectory_to_use = '/' . $domainDestination;
            } else {
                $homedirectory_to_use = '/' . $destination;
            }

            // Check if Path is inside user home directory.
            $full_homeDir  = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . $homedirectory_to_use . '/';
            $baseDir       = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'];
            $realPath      = realpath($full_homeDir);

            if( 0 !== strpos($realPath, $baseDir))
            {
                self::$invalidPath = true;
                return false;
            }

            $sql = $zdbh->prepare("INSERT INTO x_ftpaccounts (ft_acc_fk, ft_user_vc, ft_directory_vc, ft_access_vc, ft_password_vc, ft_created_ts) VALUES (:userid, :username, :homedir, :accesstype, :password, :time)");
            $sql->bindParam(':userid', $currentuser['userid']);
            $sql->bindParam(':username', $username);
            $sql->bindParam(':homedir', $homedirectory_to_use);
            $sql->bindParam(':accesstype', $access_type);
            $sql->bindParam(':password', $password);
            $sql->bindParam(':time', time());
            $sql->execute();
            self::$create = true;
            // Include FTP server specific file here.
            $FtpModuleFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('ftp_php');
            if (file_exists($FtpModuleFile)) {
                include($FtpModuleFile);
            }
            runtime_hook::Execute('OnAfterCreateFTPAccount');
            return true;
        }
        return false;
    }

    static function CheckForErrors($username, $password)
    {
        global $zdbh;
        $retval = FALSE;
        // Check to make sure the username and password is not blank before we go any further...
        if ($username == '' || $password == '') {
            self::$blank = TRUE;
            $retval = TRUE;
        }
        // Check for invalid username
        if (!self::IsValidUserName($username)) {
            self::$badname = true;
            $retval = TRUE;
        }
        // Check to make sure the cron is not a duplicate...
        $sql = "SELECT COUNT(*) FROM x_ftpaccounts WHERE ft_user_vc=:userid AND ft_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $username);

        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                self::$alreadyexists = TRUE;
                $retval = TRUE;
            }
        }
        return $retval;
    }

    static function IsValidUserName($username)
    {
        return preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $username) || preg_match('/-$/', $username) == 1;
    }

    static function ExecuteDeleteFTP($ft_id_pk)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeDeleteFTPAccount');
        $rowftpsql = "SELECT * FROM x_ftpaccounts WHERE ft_id_pk=:ftIdPk";
        $rowftpfind = $zdbh->prepare($rowftpsql);
        $rowftpfind->bindParam(':ftIdPk', $ft_id_pk);
        $rowftpfind->execute();
        $rowftp = $rowftpfind->fetch();

        $sql = $zdbh->prepare("UPDATE x_ftpaccounts SET ft_deleted_ts=:time WHERE ft_id_pk=:ftpid");
        $sql->bindParam(':ftpid', $ft_id_pk);
        $sql->bindParam(':time', $ft_id_pk);
        $sql->execute();
        self::$delete = true;
        // Include FTP server specific file here.
        $FtpModuleFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('ftp_php');
        if (file_exists($FtpModuleFile)) {
            include($FtpModuleFile);
        }
        $retval = TRUE;
        runtime_hook::Execute('OnAfterDeleteFTPAccount');
        return $retval;
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doCreateFTP()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteCreateFTP($currentuser['userid'], $formvars['inFTPUsername'], $formvars['inPassword'], $formvars['inDestination'], $formvars['inDomainDestination'], $formvars['inAccess'], $formvars['inAutoHome'])) {
            self::$ok = true;
            return true;
        } else {
            return false;
        }
    }

    static function doDeleteFTP()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteFTP($formvars['inDelete']))
            self::$ok = true;
        return true;
    }

    static function doResetPassword()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteResetPassword($formvars['inReset'], $formvars['inPassword']))
            self::$ok = true;
        return true;
    }

    static function getClientList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $clientlist = self::ListClients($currentuser['userid']);
        return (!fs_director::CheckForEmptyValue($clientlist)) ? $clientlist : false;
    }

    static function getDomainDirsList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $domaindirectories = self::ListDomainDirs($currentuser['userid']);
        return (!fs_director::CheckForEmptyValue($domaindirectories)) ? $domaindirectories : false;
    }

    static function getMasterDirsList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $domaindirectories = self::ListMasterDirs($currentuser['userid']);
        return (!fs_director::CheckForEmptyValue($domaindirectories)) ? $domaindirectories : false;
    }

    static function doEditFTP()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListClients($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['id']);
                exit;
            }
            if (isset($formvars['inReset_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $row['id']);
                exit;
            }
        }
        return;
    }

    static function getisCreateFTP()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return !isset($urlvars['show']);
    }

    static function getisDeleteFTP()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Delete");
    }

    static function getisEditFTP()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Edit");
    }

    static function getEditCurrentName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentClient($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['username'];
        } else {
            return "";
        }
    }

    static function getEditCurrentID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentClient($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['ftpaccountsquota'] < 0 ) or //-1 = unlimited
                ($currentuser['ftpaccountsquota'] > ctrl_users::GetQuotaUsages('ftpaccounts', $currentuser['userid']));
    }

    static function getFTPUsagepChart()
    {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$maximum = $currentuser['ftpaccountsquota'];
		if ($maximum < 0) { //-1 = unlimited
            if (file_exists(ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png')) {
				return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			} else {
				return '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			}
        } else {
            $used = ctrl_users::GetQuotaUsages('ftpaccounts', $currentuser['userid']);
            $free = max($maximum - $used, 0);
            return '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&labels=Free: ' . $free . '::Used: ' . $used
                    . '&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You must enter a valid username and password to create your FTP account."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("An FTP account with that name already exists."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$error)) {
            return ui_sysmessage::shout(ui_language::translate("There was an error updating your FTP accounts."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badname)) {
            return ui_sysmessage::shout(ui_language::translate("Your ftp account name is not valid. Please enter a valid ftp account name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$invalidPath)) {
            return ui_sysmessage::shout(ui_language::translate("Invalid Folder."), "zannounceok");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("FTP accounts updated successfully."), "zannounceok");
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
