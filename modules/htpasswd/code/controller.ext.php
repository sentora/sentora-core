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

    static $alreadyexists;
    static $blank;
    static $nomatch;
    static $nowrite;
    static $ok;

    /**
     * The 'worker' methods.
     */
    static function ListProtectedDirectories($uid) {
        global $zdbh;
        $sql = "SELECT * FROM x_htaccess WHERE ht_acc_fk=:uid AND ht_deleted_ts IS NULL";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            while ($rowhta = $sql->fetch()) {
                array_push($res, array('id' => $rowhta['ht_id_pk'],
                    'htuser' => $rowhta['ht_user_vc'],
                    'htdir' => $rowhta['ht_dir_vc'],
                    'userid' => $rowhta['ht_acc_fk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentHTA($id) {
        global $zdbh;
        $res = array();
        //$htpasswd = ctrl_options::GetSystemOption('zpanel_root') . "modules/htpasswd/assets/files/" . $id . ".htpasswd";
        $htpasswd = "modules/htpasswd/assets/files/" . $id . ".htpasswd";
        if (file_exists($htpasswd)) {
            $lines = file($htpasswd);
            foreach ($lines as $line_num => $line) {
                $data = explode(":", $line);
                array_push($res, array('htaccuser' => $data[0],
                    'htaccpass' => $data[1]));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListHTA($id) {
        global $zdbh;
        $sql = "SELECT * FROM x_htaccess WHERE ht_id_pk=:id AND ht_deleted_ts IS NULL";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':id', $id);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':id', $id);
            $res = array();
            $sql->execute();
            while ($rowhta = $sql->fetch()) {
                array_push($res, array('id' => $rowhta['ht_id_pk'],
                    'htuser' => $rowhta['ht_user_vc'],
                    'htdir' => $rowhta['ht_dir_vc'],
                    'userid' => $rowhta['ht_acc_fk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function DirectoryIsProtected($uid, $folder) {
        global $zdbh;
        //$rowpath = $zdbh->query("SELECT * FROM x_htaccess WHERE ht_acc_fk=" . $uid . " AND ht_dir_vc='" . $folder . "' AND ht_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_htaccess WHERE ht_acc_fk=:uid AND ht_dir_vc=:folder AND ht_deleted_ts IS NULL");
        $numrows->bindParam(':uid', $uid);
        $numrows->bindParam(':folder', $folder);
        $numrows->execute();
        $rowpath = $numrows->fetch();

        if ($rowpath) {
            if (file_exists(ctrl_options::GetSystemOption('hosted_dir') . $folder . "/.htaccess")) {
                header("location: ./?module=htpasswd&selected=Selected&show=Edit&other=" . $rowpath['ht_id_pk'] . "");
                exit;
            } else {
                if (file_exists(ctrl_options::GetSystemOption('zpanel_root') . "modules/htpasswd/assets/files/" . $rowpath['ht_id_pk'] . ".htpasswd")) {
                    unlink(ctrl_options::GetSystemOption('zpanel_root') . "modules/htpasswd/assets/files/" . $rowpath['ht_id_pk'] . ".htpasswd");
                }
                $sql = $zdbh->prepare("UPDATE x_htaccess SET ht_deleted_ts=:time WHERE ht_id_pk=:ht_id_pk");
                $sql->bindParam(':ht_id_pk', $rowpath['ht_id_pk']);
                $time = time();
                $sql->bindParam(':time', $time);
                $sql->execute();
                return false;
            }
        } else {
            return false;
        }
    }

    static function ExecuteDeleteHTA($id) {
        global $zdbh;
        runtime_hook::Execute('OnBeforeDeleteHTAccess');
        //$row = $zdbh->query("SELECT * FROM x_htaccess WHERE ht_id_pk=" . $id . "")->fetch();        

        $numrows = $zdbh->prepare("SELECT * FROM x_htaccess WHERE ht_id_pk=:id");
        $numrows->bindParam(':id', $id);
        $numrows->execute();
        $row = $numrows->fetch();

        $sql = $zdbh->prepare("UPDATE x_htaccess SET ht_deleted_ts=:time WHERE ht_id_pk=:id");
        $sql->bindParam(':id', $id);
        $time = time();
        $sql->bindParam(':time', $time);
        $sql->execute();
        $htpassword = ctrl_options::GetSystemOption('zpanel_root') . "modules/htpasswd/assets/files/" . $id . ".htpasswd";
        $htaccess = ctrl_options::GetSystemOption('hosted_dir') . $row['ht_dir_vc'] . "/.htaccess";
        if (file_exists($htpassword)) {
            unlink($htpassword);
        }
        if (file_exists($htaccess)) {
            unlink($htaccess);
        }
        runtime_hook::Execute('OnAfterDeleteHTAccess');
        return true;
    }

    static function ExecuteRemoveUserHTA($id, $username) {
        runtime_hook::Execute('OnBeforeRemoveUserHTAccess');
        $htpasswd_exe = ctrl_options::GetSystemOption('htpasswd_exe') . " -D " .
                ctrl_options::GetSystemOption('zpanel_root') .
                "/modules/htpasswd/assets/files/" .
                $id . ".htpasswd " . $username . "";
        system($htpasswd_exe);
        runtime_hook::Execute('OnAfterRemoveUserHTAccess');
        header("location: ./?module=htpasswd&selected=Selected&show=Edit&other=" . $id . "");
        exit;
    }

    static function ExecuteAddUserHTA($id, $inHTUsername, $inHTPassword, $inConfirmHTPassword) {
        if (fs_director::CheckForEmptyValue(self::CheckAddForErrors($inHTPassword, $inConfirmHTPassword))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeAdUserHTAccess');
        $htpasswd_exe = ctrl_options::GetSystemOption('htpasswd_exe') . " -b -m " .
                ctrl_options::GetSystemOption('zpanel_root') .
                "/modules/htpasswd/assets/files/" .
                $id . ".htpasswd " . strtolower(str_replace(' ', '', $inHTUsername)) . " " . $inHTPassword . "";
        system($htpasswd_exe);
        runtime_hook::Execute('OnAfterAddUserHTAccess');
        header("location: ./?module=htpasswd&selected=Selected&show=Edit&other=" . $id . "");
        exit;
    }

    static function ExecuteCreateHTA($userid, $inAuthName, $inHTUsername, $inHTPassword, $inConfirmHTPassword, $inPath) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($userid);
        // Check for spaces and remove if found...
        $username = strtolower(str_replace(' ', '', $inAuthName));
        // If errors are found, then exit before creating user...
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($userid, $inAuthName, $inHTUsername, $inHTPassword, $inConfirmHTPassword, $inPath))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreateHTAccess');
        $sql = $zdbh->prepare("INSERT INTO x_htaccess (
								ht_acc_fk, 
								ht_user_vc, 
								ht_dir_vc,
								ht_created_ts) VALUES (
								:userid, 
								:inHTUsername, 
								:inPath,
								:time)");
        $time = time();
        $sql->bindParam(':userid', $userid);
        $sql->bindParam(':inHTUsername', $inHTUsername);
        $sql->bindParam(':inPath', $inPath);
        $sql->bindParam(':time', $time);
        $sql->execute();

        //$row = $zdbh->query("SELECT * FROM x_htaccess WHERE ht_acc_fk =" . $userid . " AND ht_deleted_ts IS NULL ORDER BY ht_id_pk DESC LIMIT 1")->fetch();       
        $numrows = $zdbh->prepare("SELECT * FROM x_htaccess WHERE ht_acc_fk =:userid AND ht_deleted_ts IS NULL ORDER BY ht_id_pk DESC LIMIT 1");
        $numrows->bindParam(':userid', $userid);
        $numrows->execute();
        $row = $numrows->fetch();

        $htaccesfiledir = ctrl_options::GetSystemOption('zpanel_root') . "modules/htpasswd/assets/files/";
        if (!is_dir($htaccesfiledir)) {
            fs_director::CreateDirectory($htaccesfiledir);
        }
        $htaccessfile = ctrl_options::GetSystemOption('hosted_dir') . $inPath . "/.htaccess";
        $stringData = "AuthUserFile " . $htaccesfiledir . $row['ht_id_pk'] . ".htpasswd" . fs_filehandler::NewLine();
        $stringData .= "AuthType Basic" . fs_filehandler::NewLine();
        $stringData .= "AuthName \"" . $inAuthName . "\"" . fs_filehandler::NewLine();
        $stringData .= "Require valid-user" . fs_filehandler::NewLine();
        if (is_writable(ctrl_options::GetSystemOption('hosted_dir') . $inPath)) {
            fs_filehandler::UpdateFile($htaccessfile, 0777, $stringData);
            fs_director::SetFileSystemPermissions($htaccessfile, 0777);
        }
        if (file_exists($htaccessfile)) {
            fs_director::SetFileSystemPermissions($htaccesfiledir, 0777);
            $htpasswd_exe = ctrl_options::GetSystemOption('htpasswd_exe') . " -b -m -c " .
                    $htaccesfiledir .
                    $row['ht_id_pk'] . ".htpasswd " .
                    $inHTUsername . " " . $inHTPassword . "";

            system($htpasswd_exe);
        } else {
            $sql = $zdbh->prepare("DELETE  FROM x_htaccess WHERE ht_id_pk=:ht_id_pk");
            $sql->bindParam(':ht_id_pk', $row['ht_id_pk']);
            $sql->execute();
            self::$nowrite = true;
        }
        runtime_hook::Execute('OnAfterCreateHTAccess');
        self::$ok = true;
        return true;
    }

    static function CheckCreateForErrors($userid, $inAuthName, $inHTUsername, $inHTPassword, $inConfirmHTPassword, $inPath) {
        global $zdbh;
        // Check to make sure the user name is not blank before we go any further...
        if ($inAuthName == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure the password is not blank before we go any further...
        if ($inHTPassword == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure the confirm is not blank before we go any further...
        if ($inConfirmHTPassword == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure passwords match before we go any further...
        if ($inHTPassword != $inConfirmHTPassword) {
            self::$nomatch = true;
            return false;
        }
        // Check to make sure the directory is writable...
        $htaccessfile = ctrl_options::GetSystemOption('hosted_dir') . $inPath . "";
        if (!is_writable($htaccessfile)) {
            self::$nowrite = true;
            return false;
        }
        return true;
    }

    static function CheckAddForErrors($inHTPassword, $inConfirmHTPassword) {
        global $zdbh;
        // Check to make sure the password is not blank before we go any further...
        if ($inHTPassword == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure the confirm is not blank before we go any further...
        if ($inConfirmHTPassword == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure passwords match before we go any further...
        if ($inHTPassword != $inConfirmHTPassword) {
            self::$nomatch = true;
            return false;
        }
        return true;
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doSelectFolder() {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inFolder'])) {
            if (!self::DirectoryIsProtected($currentuser['userid'], $formvars['inFolder'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&selected=Selected&path=" . $formvars['inFolder'] . "");
                exit;
            }
        }
        return;
    }

    static function doCreateHTA() {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteCreateHTA($currentuser['userid'], $formvars['inAuthName'], $formvars['inHTUsername'], $formvars['inHTPassword'], $formvars['inConfirmHTPassword'], $currentuser['username'] . '/public_html/' . $formvars['inPath']))
            return true;
        return false;
    }

    static function doEditHTA() {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListProtectedDirectories($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&selected=Selected&show=Delete&other=" . $row['id'] . "");
                exit;
            }
            if (isset($formvars['inEdit_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&selected=Selected&show=Edit&other=" . $row['id'] . "");
                exit;
            }
        }
        return;
    }

    static function doConfirmDeleteHTA() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteHTA($formvars['inDelete']))
            return true;
        return false;
    }

    static function doRemoveUserHTA() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteRemoveUserHTA($formvars['inID'], $formvars['inRemove']))
            return true;
        return false;
    }

    static function doAddUserHTA() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteAddUserHTA($formvars['inID'], $formvars['inHTUsername'], $formvars['inHTPassword'], $formvars['inConfirmHTPassword']))
            return true;
        return false;
    }

    static function getHTA() {
        global $controller;
        return self::ListHTA($controller->GetControllerRequest('URL', 'other'));
    }

    static function getisSelected() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['selected'])) && ($urlvars['selected'] == "Selected"))
            return true;
        return false;
    }

    static function getisEdit() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit"))
            return true;
        return false;
    }

    static function getisDelete() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getProtectedDirectories() {
        global $controller;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListProtectedDirectories($currentuser['userid']);
    }

    static function getSelectedFolder() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['path'])))
            return $urlvars['path'];
    }

    static function getCurrentSelectedFolder() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListHTA($controller->GetControllerRequest('URL', 'other'));
            return str_replace($current[0]['htuser'] . '/public_html/', '', $current[0]['htdir']);
        } else {
            return "";
        }
    }

    static function getRootPath() {
        $currentuser = ctrl_users::GetUserDetail();
        return "";
    }

    static function getCurrentUserName() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser['username'];
    }

    static function getCurrentHTID() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListHTA($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function getCurrentHTA() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        $retval = self::ListCurrentHTA($controller->GetControllerRequest('URL', 'other'));
        if ($retval) {
            return self::ListCurrentHTA($controller->GetControllerRequest('URL', 'other'));
        } else {
            return false;
        }
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You need to specify a user name and password."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$nomatch)) {
            return ui_sysmessage::shout(ui_language::translate("Your passwords do not match!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$nowrite)) {
            return ui_sysmessage::shout(ui_language::translate("Cannot write to that directory! Check your permissions."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Your directory has been protected successfully!"), "zannounceok");
        }
        return;
    }

    static function getCSFR_Tag() {
        return runtime_csfr::Token();
    }

    static function getModuleDesc() {
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

    static function getModuleName() {
        $module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

}

?>