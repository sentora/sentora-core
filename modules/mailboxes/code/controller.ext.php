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
    static $validemail;
    static $noaddress;
    static $editmailbox;
    static $validdomain;
    static $update;
    static $delete;
    static $create;

    /**
     * The 'worker' methods.
     */
    static function ListMailboxes($uid)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=:userid AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        
        if(file_exists(ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/up.gif') && file_exists(ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/down.gif')) {
            $iconpath = '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/';
        }else{
            $iconpath = '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/';    
        }


        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $currentuser['userid']);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
                if ($rowmailboxes['mb_enabled_in'] == 1) {
                    $status = $iconpath . '/up.gif" alt="Up"/>';
                } else {
                    $status = $iconpath . '/down.gif" alt="Down"/>';
                }
                $res[] = array('address' => $rowmailboxes['mb_address_vc'],
                    'created' => date(ctrl_options::GetSystemOption('sentora_df'), $rowmailboxes['mb_created_ts']),
                    'status' => $status,
                    'id' => $rowmailboxes['mb_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentMailboxes($mid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mailboxes WHERE mb_id_pk=:mid AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':mid', $mid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':mid', $mid);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
                if ($rowmailboxes['mb_enabled_in'] == 1) {
                    $ischeck = "CHECKED";
                } else {
                    $ischeck = NULL;
                }
                $res[] = array('address' => $rowmailboxes['mb_address_vc'],
                    'created' => date(ctrl_options::GetSystemOption('sentora_df'), $rowmailboxes['mb_created_ts']),
                    'ischeck' => $ischeck,
                    'id' => $rowmailboxes['mb_id_pk']);
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListDomains($uid)
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=:userid AND vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $currentuser['userid']);
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

    static function ExecuteAddMailbox($uid, $address, $domain, $password)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($address, $domain, $password))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreateMailbox');
        $address = strtolower(str_replace(' ', '', $address));
        $fulladdress = strtolower(str_replace(' ', '', $address . "@" . $domain));
        self::$create = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile))
            include($MailServerFile);

        $sql = "INSERT INTO x_mailboxes (mb_acc_fk,
											 mb_address_vc,
											 mb_created_ts) VALUES (
											 :userid,
											 :fulladdress,
											 :time)";
        $time = time();
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':time', $time);
        $sql->bindParam(':userid', $currentuser['userid']);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->execute();
        runtime_hook::Execute('OnAfterCreateMailbox');
        self::$ok = true;
        return true;
    }

    static function ExecuteDeleteMailbox($mid)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeDeleteMailbox');
        self::$delete = true;
        //$rowmailbox = $zdbh->query("SELECT * FROM x_mailboxes WHERE mb_id_pk=" . $mid . "")->Fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mailboxes WHERE mb_id_pk=:mid");
        $numrows->bindParam(':mid', $mid);
        $numrows->execute();
        $rowmailbox = $numrows->fetch();
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile)) {
            include($MailServerFile);
        }
        $time = time();
        $sql = "UPDATE x_mailboxes SET mb_deleted_ts=:time WHERE mb_id_pk=:mid";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':time', $time);
        $sql->bindParam(':mid', $mid);
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteMailbox');
        self::$ok = true;
    }

    static function ExecuteUpdateMailbox($mid, $password, $enabled)
    {
        global $zdbh;
        global $controller;
        runtime_hook::Execute('OnBeforeUpdateMailbox');
        $numrows = $zdbh->prepare("SELECT * FROM x_mailboxes WHERE mb_id_pk=:mid");
        $numrows->bindParam(':mid', $mid);
        $numrows->execute();
        $rowmailbox = $numrows->fetch();
        if ($enabled <> 0) {
            self::ExecuteEnableMailbox($mid);
        } else {
            self::ExecuteDisableMailbox($mid);
        }
        self::$update = true;
        // Include mail server specific file here.
        $MailServerFile = 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/code/' . ctrl_options::GetSystemOption('mailserver_php');
        if (file_exists($MailServerFile)) {
            include($MailServerFile);
        }
        runtime_hook::Execute('OnAfterUpdateMailbox');
        self::$ok = true;
        return;
    }

    static function ExecuteEnableMailbox($mid)
    {
        global $zdbh;
        runtime_hook::Execute('OnBeforeEnableMailbox');
        $sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=1 WHERE mb_id_pk=:mid");
        $sql->bindParam(':mid', $mid);
        $sql->execute();
        $retval = true;
        runtime_hook::Execute('OnAfterEnableMailbox');
        return $retval;
    }

    static function ExecuteDisableMailbox($mid)
    {
        global $zdbh;
        runtime_hook::Execute('OnBeforeDisableMailbox');
        $sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=0 WHERE mb_id_pk=:mid");
        $sql->bindParam(':mid', $mid);
        $sql->execute();
        $retval = true;
        runtime_hook::Execute('OnAfterDisableMailbox');
        return $retval;
    }

    static function CheckCreateForErrors($address, $domain, $password)
    {
        global $zdbh;
        $fulladdress = strtolower(str_replace(' ', '', $address . '@' . $domain));
        if (fs_director::CheckForEmptyValue($address)) {
            self::$noaddress = true;
            return false;
        }
        if (fs_director::CheckForEmptyValue($password)) {
            self::$password = true;
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
        return preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email) == 1;
    }
    
    static function IsValidDomain($domain)
    {
         foreach(self::ListDomains() as $checkDomain){
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
    static function doAddMailbox()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteAddMailbox($currentuser['userid'], $formvars['inAddress'], $formvars['inDomain'], $formvars['inPassword']))
            self::$ok = true;
        return true;
    }

    static function doEditMailbox()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListMailboxes($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['id']])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . '&show=Delete&other=' . $row['id']);
                exit;
            }
            if (isset($formvars['inEdit_' . $row['id']])) {
                header('location: ./?module=' . $controller->GetCurrentModule() . '&show=Edit&other=' . $row['id']);
                exit;
            }
        }
        return true;
    }

    static function doUpdateMailbox()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        $enabled = (isset($formvars['inEnabled'])) ? fs_director::GetCheckboxValue($formvars['inEnabled']) : 0;
        if (self::ExecuteUpdateMailbox($formvars['inSave'], $formvars['inPassword'], $enabled))
            self::$ok = true;
        return true;
    }

    static function doConfirmDeleteMailbox()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteDeleteMailbox($formvars['inDelete']);
    }

    static function getMailboxList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListMailboxes($currentuser['userid']);
    }

    static function getDomainList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDomains($currentuser['userid']);
    }

    static function getCurrentMailboxList()
    {
        global $controller;
        return self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
    }

    static function GetMailOption($name)
    {
        global $zdbh;
        $numrows = $zdbh->prepare("SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = :name");
        $numrows->bindParam(':name', $name);
        $numrows->execute();
        $result = $numrows->fetch();
        return ($result) ? $result['mbs_value_tx'] : false;
    }

    static function getisCreateMailbox()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return !isset($urlvars['show']);
    }

    static function getisDeleteMailbox()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Delete");
    }

    static function getisEditMailbox()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Edit");
    }

    static function getEditCurrentMailboxName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return '';
        }
    }

    static function getEditCurrentMailboxID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['mailboxquota'] < 0) or //-1 = unlimited
                ($currentuser['mailboxquota'] > ctrl_users::GetQuotaUsages('mailboxes', $currentuser['userid']));
    }

    static function getEmailUsagepChart()
    {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$maximum = $currentuser['mailboxquota'];
		if ($maximum < 0) { //-1 = unlimited
            if (file_exists(ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png')) {
				return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			} else {
				return '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
			}
        } else {
            $used = ctrl_users::GetQuotaUsages('mailboxes', $currentuser['userid']);
            $free = max($maximum - $used, 0);
            return '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&labels=Free: ' . $free . '::Used: ' . $used
                    . '&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
    }

    static function getResult()
    {
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
        if (!fs_director::CheckForEmptyValue(self::$validdomain)) {
            return ui_sysmessage::shout(ui_language::translate("The selected domain was not valid."), "zannounceerror");
        }   
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your mailboxes have been saved successfully!"), "zannounceok");
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
