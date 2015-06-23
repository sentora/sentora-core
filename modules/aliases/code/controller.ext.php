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
    static $alreadyexists;
    static $validemail;
    static $validdomain;
    static $noaddress;
    static $delete;
    static $create;

    /**
     * The 'worker' methods.
     */
    static function ListAliases($uid)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_aliases WHERE al_acc_fk=:userid AND al_deleted_ts IS NULL ORDER BY al_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql2 = $zdbh->prepare($sql);
            $res = array();
            $sql2->bindParam(':userid', $currentuser['userid']);
            $sql2->execute();
            while ($rowaliases = $sql2->fetch()) {
                array_push($res, array('address' => $rowaliases['al_address_vc'],
                    'destination' => $rowaliases['al_destination_vc'],
                    'id' => $rowaliases['al_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentAlias($aid)
    {
        global $zdbh;
        global $controller;
        $sql = "SELECT * FROM x_aliases WHERE al_id_pk=:aid AND al_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':aid', $aid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':aid', $aid);
            $sql->execute();
            while ($rowaliases = $sql->fetch()) {
                array_push($res, array('address' => $rowaliases['al_address_vc'],
                    'destination' => $rowaliases['al_destination_vc'],
                    'id' => $rowaliases['al_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getMailboxList($uid = null)
    {
        global $zdbh;
        global $controller;
        if (($uid == '') || (empty($uid)) || ($uid == null)) {
            $uid = ctrl_auth::CurrentUserID();
        }
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=:userid AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sqlRun = $zdbh->prepare($sql);
            $sqlRun->bindParam(':userid', $currentuser['userid']);
            $res = array();
            $sqlRun->execute();
            while ($rowmailboxes = $sqlRun->fetch()) {
                array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
                    'id' => $rowmailboxes['mb_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getDomainList($uid = null)
    {
        global $zdbh;
        global $controller;
        if (($uid == '') || (empty($uid)) || ($uid == null)) {
            $uid = ctrl_auth::CurrentUserID();
        }
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=:userid AND vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sqlRun = $zdbh->prepare($sql);
            $sqlRun->bindParam(':userid', $currentuser['userid']);
            $res = array();
            $sqlRun->execute();
            while ($rowdomains = $sqlRun->fetch()) {
                array_push($res, array('domain' => $rowdomains['vh_name_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteCreateAlias($uid, $address, $domain, $destination)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($address, $domain, $destination))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreateAlias');
        $address = str_replace('*', '', $address);
        $fulladdress = $address . "@" . $domain;
        $destination = strtolower(str_replace(' ', '', $destination));
        self::$create = true;
        // Include mail server specific file here.
        include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "");

        $bindArray = array(
            ':userid' => $currentuser['userid'],
            ':fulladdress' => $fulladdress,
            ':destination' => $destination,
            ':time' => time()
        );
        $sql = "INSERT INTO x_aliases (al_acc_fk,
											  al_address_vc,
											  al_destination_vc,
											  al_created_ts) VALUES (
											  :userid,
											  :fulladdress,
											  :destination,
											  :time)";
        if ($zdbh->bindQuery($sql, $bindArray)) {
            runtime_hook::Execute('OnAfterCreateAlias');
            self::$ok = true;
            return true;
        } else {
            return false;
        }
    }

    static function ExecuteDeleteAlias($al_id_pk)
    {
        global $zdbh;
        global $controller;
        self::$delete = true;
        runtime_hook::Execute('OnBeforeDeleteAlias');
        //$rowalias = $zdbh->query("SELECT * FROM x_aliases WHERE al_id_pk=" . $al_id_pk . "")->Fetch();
        $bindArray = array(
            ':id' => $al_id_pk,
        );
        $sqlStatment = $zdbh->bindQuery("SELECT * FROM x_aliases WHERE al_id_pk=:id", $bindArray);
        $rowalias = $zdbh->returnRow();

        // Include mail server specific file here.
        if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "")) {
            include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('mailserver_php') . "");
        }
        $sqlStatmentUpdate = "UPDATE x_aliases SET al_deleted_ts=:time WHERE al_id_pk=:id";
        $sql = $zdbh->prepare($sqlStatmentUpdate);
        $sql->bindParam(':id', $al_id_pk);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteAlias');
        self::$ok = true;
    }

    static function CheckCreateForErrors($address, $domain, $destination)
    {
        global $zdbh;
        global $controller;
        $fulladdress = $address . "@" . $domain;
        $destination = strtolower(str_replace(' ', '', $destination));
        if (fs_director::CheckForEmptyValue($address)) {
            self::$noaddress = true;
            return false;
        }
        if (!self::IsValidEmail($fulladdress)) {
            self::$validemail = true;
            return false;
        }
        if(!self::IsValidDomain($domain)){
            self::$validdomain = true;
            return false;        
        }
        $sql = "SELECT * FROM x_mailboxes WHERE mb_address_vc=:fulladdress AND mb_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexists = true;
            return false;
        }
        $sql = "SELECT * FROM x_forwarders WHERE fw_address_vc=:fulladdress AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexists = true;
            return false;
        }
        $sql = "SELECT * FROM x_forwarders WHERE fw_destination_vc=:fulladdress AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexists = true;
            return false;
        }
        $sql = "SELECT * FROM x_distlists WHERE dl_address_vc=:fulladdress AND dl_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexists = true;
            return false;
        }
        $sql = "SELECT * FROM x_aliases WHERE al_address_vc=:fulladdress AND al_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexists = true;
            return false;
        }
        return true;
    }

    static function IsValidEmail($email)
    {
        if (!preg_match('/^([\*]|[a-z0-9]+([_\\.-][a-z0-9]+)*)@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email)) {
            return false;
        }
        return true;
    }
    
    static function IsValidDomain($domain)
    {
         foreach(self::getDomainList() as $checkDomain){
            if($checkDomain == $domain){
                return true;
            }
        }
        return false;
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doCreateAlias()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteCreateAlias($currentuser['userid'], $formvars['inAddress'], $formvars['inDomain'], $formvars['inDestination']))
            self::$ok = true;
        return true;
    }

    static function doDeleteAlias()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListAliases($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['id'] . "");
                exit;
            }
            if (isset($formvars['inEdit_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $row['id'] . "");
                exit;
            }
        }
        return;
    }

    static function doConfirmDeleteAlias()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteAlias($formvars['inDelete']))
            return true;
        return false;
    }

    static function getEditCurrentAliasName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentAlias($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return "";
        }
    }

    static function getAliasList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListAliases($currentuser['userid']);
    }

    static function getCurrentAliasList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
    }

    static function getisCreateAlias()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getisDeleteAlias()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getEditCurrentAliasID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentAlias($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function GetMailOption($name)
    {
        global $zdbh;
        $sql = 'SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = :name';
        $bindArray = array(':name' => $name);
        $sqlStatment = $zdbh->bindQuery($sql, $bindArray);
        $result = $zdbh->returnRow();

        if ($result) {
            return $result['mbs_value_tx'];
        } else {
            return false;
        }
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

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['forwardersquota'] < 0 ) or //-1 = unlimited
                ($currentuser['forwardersquota'] > ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']));
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A mailbox, alias, forwarder or distrubution list already exists with that name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$validemail)) {
            return ui_sysmessage::shout(ui_language::translate("Your email address is not valid."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$noaddress)) {
            return ui_sysmessage::shout(ui_language::translate("Your email address cannot be blank."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$validdomain)) {
            return ui_sysmessage::shout(ui_language::translate("The selected domain was not valid."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your aliases have been saved successfully!"), "zannounceok");
        } else {
            return NULL;
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
