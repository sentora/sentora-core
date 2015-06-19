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
    static $edit;
    static $alreadyexists;
    static $validemail;
    static $noaddress;
    static $validdomain;
    static $delete;
    static $create;
    static $deleteuser;
    static $createuser;

    /**
     * The 'worker' methods.
     */
    static function ListDist($uid)
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_distlists WHERE dl_acc_fk=:userid AND dl_deleted_ts IS NULL ORDER BY dl_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':userid', $currentuser['userid']);
            $sql->execute();
            while ($rowdistlist = $sql->fetch()) {
                $numrows2 = $zdbh->prepare("SELECT COUNT(*) FROM x_distlistusers WHERE du_distlist_fk=:dl_id_pk AND du_deleted_ts IS NULL");
                $numrows2->bindParam(':dl_id_pk', $rowdistlist['dl_id_pk']);
                $numrows2->execute();
                $numrowmb = $numrows2->fetch();
                $res[] = array('address' => $rowdistlist['dl_address_vc'],
                    'totalmb' => $numrowmb[0],
                    'id' => $rowdistlist['dl_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentDist($id)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_distlists WHERE dl_id_pk=:id AND dl_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':id', $id);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':id', $id);
            $sql->execute();
            while ($rowdistlist = $sql->fetch()) {
                $res[] = array('address' => $rowdistlist['dl_address_vc'],
                    'id' => $rowdistlist['dl_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListDistUsers($id)
    {
        global $zdbh;
        $numrows = $zdbh->prepare("SELECT * FROM x_distlists WHERE dl_id_pk=:id AND dl_deleted_ts IS NULL");
        $numrows->bindParam(':id', $id);
        $numrows->execute();
        $result = $numrows->fetch();
        if ($result) {
            $numrows = $zdbh->prepare("SELECT * FROM x_distlistusers WHERE du_distlist_fk=:dl_id_pk AND du_deleted_ts IS NULL");
            $numrows->bindParam(':dl_id_pk', $result['dl_id_pk']);
            $numrows->execute();
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_distlistusers WHERE du_distlist_fk=:dl_id_pk AND du_deleted_ts IS NULL");
                $res = array();
                $sql->bindParam(':dl_id_pk', $result['dl_id_pk']);
                $sql->execute();
                while ($rowdistlist = $sql->fetch()) {
                    $res[] = array('address' => $rowdistlist['du_address_vc'],
                        'distlist' => $result['dl_address_vc'],
                        'id' => $rowdistlist['du_id_pk']);
                }
                return $res;
            } else {
                return false;
            }
        }
        return false;
    }

    static function ListMailbox($uid)
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=:userid AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':userid', $currentuser['userid']);
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
                $res[] = array('address' => $rowmailboxes['mb_address_vc'],
                    'id' => $rowmailboxes['mb_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getDomainList()
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowdomains = $sql->fetch()) {
                $res[] = array('domain' => $rowdomains['vh_name_vc']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteAddDistList($uid, $inAddress, $inDomain)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $fulladdress = strtolower(str_replace(' ', '', $inAddress . '@' . $inDomain));
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($inAddress, $inDomain))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeAddDistList');
        self::$create = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);

        $sqlStatment = "INSERT INTO x_distlists (dl_acc_fk,
										  dl_address_vc,
										  dl_created_ts) VALUES (
										  :userid,
										  :fulladdress,
										  :time)";
        $sql = $zdbh->prepare($sqlStatment);
        $sql->bindParam(':userid', $currentuser['userid']);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterAddDistList');
        self::$ok = true;
        return true;
    }

    static function ExecuteDeleteDistList($dl_id_pk)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeDeleteDistList');
        self::$delete = true;
        $numrows = $zdbh->prepare("SELECT * FROM x_distlists WHERE dl_id_pk=:dl_id_pk AND dl_deleted_ts IS NULL");
        $numrows->bindParam(':dl_id_pk', $dl_id_pk);
        $numrows->execute();
        $rowdl = $numrows->fetch();

        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);

        $sql = "UPDATE x_distlistusers SET du_deleted_ts=:time WHERE du_distlist_fk=:dl_id_pk";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':dl_id_pk', $dl_id_pk);
        $sql->bindParam(':time', time());
        $sql->execute();
        $sql = "UPDATE x_distlists SET dl_deleted_ts=:time WHERE dl_id_pk=:dl_id_pk";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':dl_id_pk', $dl_id_pk);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteDistList');
        self::$ok = true;
    }

    static function ExecuteAddDistListUser($du_distlist_fk, $address, $domain, $dladdress)
    {
        global $zdbh;
        global $controller;
        $fulladdress = strtolower(str_replace(' ', '', $address . '@' . $domain));
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrorsDistListUser())) {
            return false;
        }

        $numrows = $zdbh->prepare("SELECT * FROM x_distlists WHERE dl_id_pk=:du_distlist_fk AND dl_deleted_ts IS NULL");
        $numrows->bindParam(':du_distlist_fk', $du_distlist_fk);
        $numrows->execute();
        $rowdl = $numrows->fetch(); //WARNING : $rowdl is used in mail server specific file

        runtime_hook::Execute('OnBeforeAddDistListUser');
        self::$createuser = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);

        $sql = "INSERT INTO x_distlistusers (
										du_distlist_fk,
										du_address_vc,
										du_created_ts) VALUES (
										:du_distlist_fk,
										:fulladdress,
										:time)";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':du_distlist_fk', $du_distlist_fk);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->bindParam(':time', time());
        $sql->execute();
        runtime_hook::Execute('OnAfterAddDistListUser');
        self::$ok = true;
        return true;
    }

    static function ExecuteDeleteDistListUser($du_id_pk)
    {
        global $zdbh;
        global $controller;
        $numrows = $zdbh->prepare("SELECT * FROM x_distlistusers WHERE du_id_pk=:du_id_pk AND du_deleted_ts IS NULL");
        $numrows->bindParam(':du_id_pk', $du_id_pk);
        $numrows->execute();
        $rowdlu = $numrows->fetch(); //WARNING : $rowdlu is used in mail server specific file

        $numrows = $zdbh->prepare("SELECT * FROM x_distlists WHERE dl_id_pk=:du_distlist_fk AND dl_deleted_ts IS NULL");
        $numrows->bindParam(':du_distlist_fk', $rowdlu['du_distlist_fk']);
        $numrows->execute();
        $rowdl = $numrows->fetch();
        $dladdress = $rowdl['dl_address_vc']; //WARNING : $dladdress is used in mail server specific file

        runtime_hook::Execute('OnBeforeDeleteDistListUser');
        self::$deleteuser = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);

        $sql = "UPDATE x_distlistusers SET du_deleted_ts=:time WHERE du_id_pk=:du_id_pk";
        $sql = $zdbh->prepare($sql);
        $time = time();
        $sql->bindParam(':time', $time);
        $sql->bindParam(':du_id_pk', $du_id_pk);
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteDistListUser');
        self::$ok = true;
        return true;
    }

    static function CheckCreateForErrors($inAddress, $inDomain)
    {
        global $zdbh;
        $fulladdress = strtolower(str_replace(' ', '', $inAddress . '@' . $inDomain));
        if (fs_director::CheckForEmptyValue($inAddress)) {
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
        $result = $numrows->fetch();
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

    static function CheckCreateForErrorsDistListUser()
    {
        global $zdbh;
        global $controller;
        $address = $controller->GetControllerRequest('FORM', 'inAddAddress');
        $domain = $controller->GetControllerRequest('FORM', 'inAddDomain');
        $dlid = $controller->GetControllerRequest('FORM', 'inDLID');
        $fulladdress = strtolower(str_replace(' ', '', $address . '@' . $domain));
        if (fs_director::CheckForEmptyValue($address)) {
            self::$noaddress = true;
            return false;
        }
        if (!self::IsValidEmail($fulladdress)) {
            self::$validemail = true;
            return false;
        }

        $sql = "SELECT * FROM x_distlistusers WHERE du_distlist_fk=:dlid  AND du_address_vc=:fulladdress AND du_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':dlid', $dlid);
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            self::$alreadyexistsdu = true;
            return false;
        }
        return true;
    }

    static function IsValidEmail($email)
    {
        return preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email) == 1;
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

    /**
     * Webinterface sudo methods.
     */
    static function doEditDistList()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListDist($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['id']);
                exit;
            }
            if (isset($formvars['inEdit_' . $row['id'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $row['id']);
                exit;
            }
        }
        return;
    }

    static function doConfirmDeleteDistList()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteDeleteDistList($formvars['inDelete']);
    }

    static function doUpdateDistList()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inAdd'])) {
            if (self::ExecuteAddDistListUser($formvars['inDLID'], $formvars['inAddAddress'], $formvars['inAddDomain'], $formvars['inDLAD'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $formvars['inDLID'] . "&status=ok");
                exit;
            }
        }
        foreach (self::ListDistUsers($formvars['inDLID']) as $row) {
            if (isset($formvars['inDeleteUser_' . $row['id'] . ''])) {
                if (self::ExecuteDeleteDistListUser($formvars['inDeleteUser_' . $row['id'] . ''])) {
                    header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $formvars['inDLID'] . "&status=ok");
                    exit;
                }
            }
        }
        return;
    }

    static function doAddDistList()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteAddDistList($currentuser['userid'], $formvars['inAddress'], $formvars['inDomain']);
    }

    static function getDistList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDist($currentuser['userid']);
    }

    static function getDistListUsers()
    {
        global $controller;
        return self::ListDistUsers($controller->GetControllerRequest('URL', 'other'));
    }

    static function getCurrentDistListID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDist($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return '';
        }
    }

    static function getCurrentDistList()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDist($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return '';
        }
    }

    static function getisEditDistList()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit"))
            return true;
        return false;
    }

    static function getisDeleteDistList()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisCreateDistList()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['distlistsquota'] < 0) or //-1 = unlimited
                ($currentuser['distlistsquota'] > ctrl_users::GetQuotaUsages('distlists', $currentuser['userid']));
    }

    static function getDistListUsagepChart()
    {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$maximum = $currentuser['distlistsquota'];
		if ($maximum < 0) { //-1 = unlimited
            if (file_exists(ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png')) {
				return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			} else {
				return '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			}
        } else {
            $used = ctrl_users::GetQuotaUsages('distlists', $currentuser['userid']);
            $free = max($maximum - $used, 0);
            return '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&labels=Free: ' . $free . '::Used: ' . $used
                    . '&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
    }

    static function getResultURL()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (isset($urlvars['status']) && $urlvars['status'] == 'ok') {
            return ui_sysmessage::shout(ui_language::translate("Changes to your distribution lists have been saved successfully!"), "zannounceok");
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A mailbox, alias, forwarder or distribution list already exists with that name."), "zannounceerror");
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
            return ui_sysmessage::shout(ui_language::translate("Changes to your distrubution lists have been saved successfully!"), "zannounceok");
        } else {
            return NULL;
        }
        return;
    }

}
