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
	static $validemail;
	static $noaddress;
	static $editmailbox;
	static $update;
	static $delete;
	static $create;

    /**
     * The 'worker' methods.
     */
	 
    static function ListMailboxes($uid) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk=" . $currentuser['userid'] . " AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
				if ($rowmailboxes['mb_enabled_in'] == 1){
					$status = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
				} else {
					$status = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
				}
                array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
									   'created' => date(ctrl_options::GetOption('zpanel_df'), $rowmailboxes['mb_created_ts']),
									   'status'  => $status,
									   'id' 	 => $rowmailboxes['mb_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentMailboxes($mid) {
        global $zdbh;
        global $controller;
        $sql = "SELECT * FROM x_mailboxes WHERE mb_id_pk=" . $mid . " AND mb_deleted_ts IS NULL ORDER BY mb_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmailboxes = $sql->fetch()) {
				if ($rowmailboxes['mb_enabled_in'] == 1){
					$ischeck = "CHECKED";
				} else {
					$ischeck = NULL;
				}
                array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
									   'created' => date(ctrl_options::GetOption('zpanel_df'), $rowmailboxes['mb_created_ts']),
									   'ischeck'  => $ischeck,
									   'id' 	 => $rowmailboxes['mb_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }
	
    static function ListDomains($uid) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowdomains = $sql->fetch()) {
                array_push($res, array('domain' => ui_language::translate($rowdomains['vh_name_vc'])));
            }
            return $res;
        } else {
            return false;
        }
    }
	
	static function ExecuteAddMailbox($uid, $address, $domain, $password){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail($uid);
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors())) {
			runtime_hook::Execute('OnBeforeCreateMailbox');
			$address = strtolower(str_replace(' ', '', $address));
			$fulladdress = strtolower(str_replace(' ', '', $address . "@" . $domain));
			self::$create=true;
			// Include mail server specific file here.
			if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "")){
				include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
			}
			$sql = "INSERT INTO x_mailboxes (mb_acc_fk,
											 mb_address_vc,
											 mb_created_ts) VALUES (
											 " . $currentuser['userid'] . ",
											 '" . $fulladdress . "',
											 " . time() . ")";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			runtime_hook::Execute('OnAfterCreateMailbox');
			self::$ok = true;
		}
	}

	static function ExecuteDeleteMailbox($mid){
		global $zdbh;
		global $controller;
		runtime_hook::Execute('OnBeforeDeleteMailbox');
		self::$delete=true;
		$rowmailbox = $zdbh->query("SELECT * FROM x_mailboxes WHERE mb_id_pk=" . $mid . "")->Fetch();
		// Include mail server specific file here.
		if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "")){
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		}
		$sql = "UPDATE x_mailboxes SET mb_deleted_ts=" . time() . " WHERE mb_id_pk=" . $mid . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		runtime_hook::Execute('OnAfterDeleteMailbox');
		self::$ok = true;
	}
	
	static function ExecuteUpdateMailbox($mid, $password, $enabled){
		global $zdbh;
		global $controller;
		runtime_hook::Execute('OnBeforeUpdateMailbox');
		$rowmailbox = $zdbh->query("SELECT * FROM x_mailboxes WHERE mb_id_pk=" . $mid . "")->fetch();
		if ($enabled <> 0){
			self::ExecuteEnableMailbox($mid);
		} else {
			self::ExecuteDisableMailbox($mid);			
		}
		self::$update=true;
		// Include mail server specific file here.
		if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "")){
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		}
		runtime_hook::Execute('OnAfterUpdateMailbox');
		self::$ok = true;
		return;
	}
	
	static function ExecuteEnableMailbox($mid){
		global $zdbh;
		runtime_hook::Execute('OnBeforeEnableMailbox');
		$retval = false;
		$sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=1 WHERE mb_id_pk='" . $mid . "'");
		$sql->execute();
		$retval = true;
		runtime_hook::Execute('OnAfterEnableMailbox');
		return $retval;
	}

	static function ExecuteDisableMailbox($mid){
		global $zdbh;
		runtime_hook::Execute('OnBeforeDisableMailbox');
		$retval = false;
		$sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=0 WHERE mb_id_pk='" . $mid . "'");
		$sql->execute();
		$retval = true;
		runtime_hook::Execute('OnAfterDisableMailbox');
		return $retval;	
	}

	static function CheckCreateForErrors(){
		global $zdbh;
        global $controller;
		$address  = $controller->GetControllerRequest('FORM', 'inAddress');
		$domain   = $controller->GetControllerRequest('FORM', 'inDomain');
		$password = $controller->GetControllerRequest('FORM', 'inPassword');
		$fulladdress = str_replace(' ', '', $address . "@" . $domain);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue($address)){
			self::$noaddress = true;
			return true;
		}
		if (fs_director::CheckForEmptyValue($password)){
			self::$password = true;
			return true;
		}
		if (!self::IsValidEmail($fulladdress)){
			self::$validemail = true;
			return true;
		}
        $sql = "SELECT * FROM x_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexists = true;
			return true;
		}
		return false;
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

    static function doAddMailbox() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteAddMailbox($currentuser['userid'], $formvars['inAddress'], $formvars['inDomain'], $formvars['inPassword']))
			self::$ok = true;
            return true;
        return false;
    }

    static function doEditMailbox() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListMailboxes($currentuser['userid']) as $row) {
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

    static function doUpdateMailbox() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
		if (isset($formvars['inEnabled'])){
			$enabled = fs_director::GetCheckboxValue($formvars['inEnabled']);
		} else {
			$enabled = 0;
		}
        if (self::ExecuteUpdateMailbox($formvars['inSave'], $formvars['inPassword'], $enabled))
			self::$ok = true;
            return true;
        return false;
    }

    static function doConfirmDeleteMailbox() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteMailbox($formvars['inDelete']))
            return true;
        return false;
    }

    static function getMailboxList() {
		global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListMailboxes($currentuser['userid']);
    }

    static function getDomainList() {
		global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDomains($currentuser['userid']);
    }
	
    static function getCurrentMailboxList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
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

    static function getisCreateMailbox() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getisDeleteMailbox() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisEditMailbox() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit")){
			return true;
		} else {
        	return false;
		}
    }

    static function getEditCurrentMailboxName() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return "";
        }
    }

    static function getEditCurrentMailboxID() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentMailboxes($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
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
	
    static function getQuotaLimit() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['mailboxquota'] > fs_director::GetQuotaUsages('mailboxes', $currentuser['userid'])) {
            return true;
        } else {
            return false;
        }
    }

    static function getEmailUsagepChart() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $mailboxquota = $currentuser['mailboxquota'];
        $mailboxes = fs_director::GetQuotaUsages('mailboxes', $currentuser['userid']);
        $total = $mailboxquota;
        $used = $mailboxes;
        $free = $total - $used;
        $line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=" . $free . "::" . $used . "&labels=Free: " . $free . "::Used: " . $used . "&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";
        return $line;
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout("The email address you entered already exists!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$validemail)) {
            return ui_sysmessage::shout("Your email address is not valid.", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$password)) {
            return ui_sysmessage::shout("Your password cannot be blank.", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$noaddress)) {
            return ui_sysmessage::shout("Your email address cannot be blank.", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout("Changes to your mailboxes have been saved successfully!", "zannounceok");
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