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

    static $ok;
    static $password;
    static $alreadyexists;
    static $alreadyexistssame;
    static $validemail;
    static $validmailbox;
    static $noaddress;
    static $delete;
    static $create;

    /**
     * The 'worker' methods.
     */
    static function ListForwarders($uid)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_forwarders WHERE fw_acc_fk=:userid AND fw_deleted_ts IS NULL ORDER BY fw_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $currentuser['userid']);
            $res = array();
            $sql->execute();
            while ($rowforwarders = $sql->fetch()) {
                if ($rowforwarders['fw_keepmessage_in'] == 1) {
                    $status = '<a href="#" title="' . ui_language::translate("A copy of the original message will be left in the source mailbox address when it is fowarded to the destination address") . '">'
                            . '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/up.gif"></a>';
                } else {
                    $status = '<a href="#" title="' . ui_language::translate("The original message will only be available in the destination address") . '">'
                            . '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/down.gif"></a>';
                }
                $res[] = array('address' => $rowforwarders['fw_address_vc'],
                    'destination' => $rowforwarders['fw_destination_vc'],
                    'status' => $status,
                    'id' => $rowforwarders['fw_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentForwarder($fid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_forwarders WHERE fw_id_pk=:fid AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fid', $fid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':fid', $fid);
            $res = array();
            $sql->execute();
            while ($rowforwarders = $sql->fetch()) {
                $res[] = array('address' => $rowforwarders['fw_address_vc'],
                    'destination' => $rowforwarders['fw_destination_vc'],
                    'id' => $rowforwarders['fw_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getMailboxList()
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=:userid AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $currentuser['userid']);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
                //$result = $zdbh->query("SELECT fw_address_vc FROM x_forwarders WHERE fw_address_vc='" . $rowmailboxes['mb_address_vc'] . "' AND fw_deleted_ts IS NULL")->Fetch();
                $numrows = $zdbh->prepare("SELECT fw_address_vc FROM x_forwarders WHERE fw_address_vc=:mb_address_vc AND fw_deleted_ts IS NULL");
                $numrows->bindParam(':mb_address_vc', $rowmailboxes['mb_address_vc']);
                $numrows->execute();
                $result = $numrows->fetch();
                if (!$result) {
                    $res[] = array('address' => $rowmailboxes['mb_address_vc'],
                        'id' => $rowmailboxes['mb_id_pk']);
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteCreateForwarder($uid, $address, $dname, $ddomain, $keepmessage)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($address, $dname, $ddomain, $keepmessage))) {
            return false;
        }
        $destination = strtolower(str_replace(' ', '', $dname . '@' . $ddomain));
        runtime_hook::Execute('OnBeforeCreateForwarder');
        self::$create = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);
        $sql = "INSERT INTO x_forwarders (fw_acc_fk,
											  fw_address_vc,
											  fw_destination_vc,
											  fw_keepmessage_in,
											  fw_created_ts) VALUES (
											  :userid,
											  :address,
											  :destination,
											  :keepmessage,
											  :time)";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':userid', $currentuser['userid']);
        $sql->bindParam(':address', $address);
        $sql->bindParam(':destination', $destination);
        $sql->bindParam(':keepmessage', $keepmessage);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterCreateForwarder');
        self::$ok = true;
        return true;
    }

    static function ExecuteDeleteForwarder($fw_id_pk)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeDeleteForwarer');
        //$rowforwarder = $zdbh->query("SELECT * FROM x_forwarders WHERE fw_id_pk=" . $fw_id_pk . "")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_forwarders WHERE fw_id_pk=:fw_id_pk");
        $numrows->bindParam(':fw_id_pk', $fw_id_pk);
        $numrows->execute();
        $rowforwarder = $numrows->fetch();
        self::$delete = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);
        $sql = "UPDATE x_forwarders SET fw_deleted_ts=:time WHERE fw_id_pk=:fw_id_pk";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':fw_id_pk', $fw_id_pk);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteForwarder');
        self::$ok = true;
    }

    static function CheckCreateForErrors($address, $dname, $ddomain, $keepmessage)
    {
        global $controller;
        $address = $controller->GetControllerRequest('FORM', 'inAddress');
        $destination = strtolower(str_replace(' ', '', $dname . '@' . $ddomain));
        if (fs_director::CheckForEmptyValue($address)) {
            self::$noaddress = true;
            return false;
        }
        if (!self::IsValidEmail($destination)) {
            self::$validemail = true;
            return false;
        }
         if (!self::IsValidMailbox($address)) {
            self::$validmailbox = true;
            return false;
        }  
        
        if ($address == $destination) {
            self::$alreadyexistssame = true;
            return false;
        }
        return true;
    }

    static function IsValidEmail($email)
    {
        return preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email) == 1;
    }
    
    static function IsValidMailbox($address)
    {
         foreach(self::getMailboxList() as $checkMailbox)
         {
            if($checkMailbox == $address)
            {
                return true;
            }
        }
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doCreateForwarder()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        $keepmessage = (isset($formvars['inKeepMessage'])) ? fs_director::GetCheckboxValue($formvars['inKeepMessage']) : 0;
        if (self::ExecuteCreateForwarder($currentuser['userid'], $formvars['inAddress'], $formvars['inDestinationName'], $formvars['inDestinationDomain'], $keepmessage))
            self::$ok = true;
        return true;
    }

    static function doDeleteForwarder()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListForwarders($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['id']);
                exit;
            }
        }
        return true;
    }

    static function doConfirmDeleteForwarder()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteDeleteForwarder($formvars['inDelete']);
    }

    static function getForwarderList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListForwarders($currentuser['userid']);
    }

    static function getisCreateForwarder()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return !isset($urlvars['show']);
    }

    static function getisDeleteForwarder()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Delete");
    }

    static function getEditCurrentForwarderName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentForwarder($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return '';
        }
    }

    static function getEditCurrentForwarderID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentForwarder($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return '';
        }
    }

    static function GetMailOption($name)
    {
        global $zdbh;
        //$result = $zdbh->query("SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = '$name'")->Fetch();
        $numrows = $zdbh->prepare("SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = :name");
        $numrows->bindParam(':name', $name);
        $numrows->execute();
        $result = $numrows->fetch();
        return ($result) ? $result['mbs_value_tx'] : false;
    }

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['forwardersquota'] < 0) or //-1 = unlimited
                ($currentuser['forwardersquota'] > ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']));
    }

    static function getForwardUsagepChart()
    {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$maximum = $currentuser['forwardersquota'];
		if ($maximum < 0) { //-1 = unlimited
            if (file_exists(ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png')) {
				return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			} else {
				return '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			}
        } else {
            $used = ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']);
            $free = max($maximum - $used, 0);
            return '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&labels=Free: ' . $free . '::Used: ' . $used
                    . '&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistssame)) {
            return ui_sysmessage::shout(ui_language::translate("You cannot forward a mailbox to itself!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate('A mailbox, alias, forwarder or distribution list already exists with that name.'), "zannounceerror");
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
        if (!fs_director::CheckForEmptyValue(self::$validmailbox)) {
            return ui_sysmessage::shout(ui_language::translate("The mailbox chosen is invalid."), "zannounceerror");
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
