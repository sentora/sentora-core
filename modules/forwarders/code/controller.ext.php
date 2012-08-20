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

    static $ok;
    static $password;
    static $alreadyexists;
    static $alreadyexistssame;
    static $validemail;
    static $noaddress;
    static $delete;
    static $create;

    /**
     * The 'worker' methods.
     */
    static function ListForwarders($uid) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_forwarders WHERE fw_acc_fk=" . $currentuser['userid'] . " AND fw_deleted_ts IS NULL ORDER BY fw_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowforwarders = $sql->fetch()) {
                if ($rowforwarders['fw_keepmessage_in'] == 1) {
                    $status = "<a href=\"#\" title=\"" . ui_language::translate("A copy of the original message will be left in the source mailbox address when it is fowarded to the destination address") . "\"><img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\"></a>";
                } else {
                    $status = "<a href=\"#\" title=\"" . ui_language::translate("The original message will only be available in the destination address") . "\"><img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\"></a>";
                }
                array_push($res, array('address' => $rowforwarders['fw_address_vc'],
                    'destination' => $rowforwarders['fw_destination_vc'],
                    'status' => $status,
                    'id' => $rowforwarders['fw_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentForwarder($fid) {
        global $zdbh;
        global $controller;
        $sql = "SELECT * FROM x_forwarders WHERE fw_id_pk=" . $fid . " AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowforwarders = $sql->fetch()) {
                array_push($res, array('address' => $rowforwarders['fw_address_vc'],
                    'destination' => $rowforwarders['fw_destination_vc'],
                    'id' => $rowforwarders['fw_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getMailboxList() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=" . $currentuser['userid'] . " AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
                $result = $zdbh->query("SELECT fw_address_vc FROM x_forwarders WHERE fw_address_vc='" . $rowmailboxes['mb_address_vc'] . "' AND fw_deleted_ts IS NULL")->Fetch();
                if (!$result) {
                    array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
                        'id' => $rowmailboxes['mb_id_pk']));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteCreateForwarder($uid, $address, $dname, $ddomain, $keepmessage) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($address, $dname, $ddomain, $keepmessage))) {
            return false;
        }
        $destination = strtolower(str_replace(' ', '', $dname . "@" . $ddomain));
        runtime_hook::Execute('OnBeforeCreateForwarder');
        self::$create = true;
        // Include mail server specific file here.
        if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "")) {
            include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "");
        }
        $sql = "INSERT INTO x_forwarders (fw_acc_fk,
											  fw_address_vc,
											  fw_destination_vc,
											  fw_keepmessage_in,
											  fw_created_ts) VALUES (
											  " . $currentuser['userid'] . ",
											  '" . $address . "',
											  '" . $destination . "',
											  '" . $keepmessage . "',
											  " . time() . ")";
        $sql = $zdbh->prepare($sql);
        $sql->execute();
        runtime_hook::Execute('OnAfterCreateForwarder');
        self::$ok = true;
        return true;
    }

    static function ExecuteDeleteForwarder($fw_id_pk) {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeDeleteForwarer');
        $rowforwarder = $zdbh->query("SELECT * FROM x_forwarders WHERE fw_id_pk=" . $fw_id_pk . "")->fetch();
        self::$delete = true;
        // Include mail server specific file here.
        if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "")) {
            include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "");
        }
        $sql = "UPDATE x_forwarders SET fw_deleted_ts=" . time() . " WHERE fw_id_pk=" . $fw_id_pk . "";
        $sql = $zdbh->prepare($sql);
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteForwarder');
        self::$ok = true;
    }

    static function CheckCreateForErrors($address, $dname, $ddomain, $keepmessage) {
        global $zdbh;
        global $controller;
        $address = $controller->GetControllerRequest('FORM', 'inAddress');
        $destination = strtolower(str_replace(' ', '', $dname . "@" . $ddomain));
        if (fs_director::CheckForEmptyValue($address)) {
            self::$noaddress = true;
            return false;
        }
        if (!self::IsValidEmail($destination)) {
            self::$validemail = true;
            return false;
        }
        if ($address == $destination) {
            self::$alreadyexistssame = true;
            return false;
        }
        return true;
    }

    static function IsValidEmail($email) {
        if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email)) {
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
    static function doCreateForwarder() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inKeepMessage'])) {
            $keepmessage = fs_director::GetCheckboxValue($formvars['inKeepMessage']);
        } else {
            $keepmessage = 0;
        }
        if (self::ExecuteCreateForwarder($currentuser['userid'], $formvars['inAddress'], $formvars['inDestinationName'], $formvars['inDestinationDomain'], $keepmessage))
            self::$ok = true;
        return true;
        return false;
    }

    static function doDeleteForwarder() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListForwarders($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['id'] . "");
                exit;
            }
        }
        return;
    }

    static function doConfirmDeleteForwarder() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteForwarder($formvars['inDelete']))
            return true;
        return false;
    }

    static function getForwarderList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListForwarders($currentuser['userid']);
    }

    static function getisCreateForwarder() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getisDeleteForwarder() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getEditCurrentForwarderName() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentForwarder($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return "";
        }
    }

    static function getEditCurrentForwarderID() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentForwarder($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function GetMailOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['mbs_value_tx'];
        } else {
            return false;
        }
    }

    static function getQuotaLimit() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $mailboxes = 0;
        $sql = "SELECT mb_id_pk FROM x_mailboxes WHERE mb_acc_fk=" . $currentuser['userid'] . " AND mb_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
            $mailboxes = $sql->rowCount();
        }
        $quota = $currentuser['mailboxquota'];
        if ($quota > $mailboxes) {
            return true;
        } else {
            return false;
        }
    }

    static function getForwardUsagepChart() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $forwardersquota = $currentuser['forwardersquota'];
        $forwarders = ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']);
        $total = $forwardersquota;
        $used = $forwarders;
        $free = $total - $used;
        $line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=" . $free . "::" . $used . "&labels=Free: " . $free . "::Used: " . $used . "&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";
        return $line;
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

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistssame)) {
            return ui_sysmessage::shout(ui_language::translate("You cannot forward a mailbox to itself!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A mailbox, alias, forwarder or distrubution list already exists with that name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$validemail)) {
            return ui_sysmessage::shout(ui_language::translate("Your email address is not valid."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$password)) {
            return ui_sysmessage::shout(ui_language::translate("Your password cannot be blank."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$noaddress)) {
            return ui_sysmessage::shout(ui_language::translate("Your email address cannot be blank."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your forwarders have been saved successfully!"), "zannounceok");
        } else {
            return NULL;
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}

?>