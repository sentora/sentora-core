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
	
    static function getDomainList() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
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
		if ($quota > $mailboxes){
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

	static function getEditMailbox() {
        global $zdbh;
        global $controller;
		if (!fs_director::CheckForEmptyValue(self::$editmailbox)){
        	$currentuser = ctrl_users::GetUserDetail();
        	$sql = "SELECT * FROM x_mailboxes WHERE mb_id_pk=" . self::$editmailbox . " AND mb_deleted_ts IS NULL";
        	$numrows = $zdbh->query($sql);
	        if ($numrows->fetchColumn() <> 0) {
	            $sql = $zdbh->prepare($sql);
	            $res = array();
	            $sql->execute();
	            while ($rowmailboxes = $sql->fetch()) {
					if ($rowmailboxes['mb_enabled_in'] == 1){
						$ischeck = "checked=\"checked\" ";
					} else {
						$ischeck = NULL;
					}
	                array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
										   'ischeck' => $ischeck,
										   'id' 	 => $rowmailboxes['mb_id_pk']));
	            }
    	        return $res;
			}
		} else {
		return false;
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

	static function doAddMailbox(){
		self::AddMailbox();	
	}

	static function doEditMailbox(){
		global $zdbh;
        global $controller;
		$mailboxes = self::getMailboxList();
		foreach ($mailboxes as $mailbox){
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$mailbox['id'].''))) {
				self::DeleteMailbox($mailbox['id']);
				self::$ok = true;
				return;
			}
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inSave_'.$mailbox['id'].''))) {
				self::SaveMailbox($mailbox['id']);
				self::$ok = true;
				return;
			}
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inEdit_'.$mailbox['id'].''))) {
				self::$editmailbox = $mailbox['id'];
				return;
			}
		}	
	}

	static function AddMailbox(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors())) {
			$fulladdress = str_replace(' ', '', $controller->GetControllerRequest('FORM', 'inAddress') . "@" . $controller->GetControllerRequest('FORM', 'inDomain'));
			$fulladdress = strtolower($fulladdress);
			$password = $controller->GetControllerRequest('FORM', 'inPassword');
			$password = md5($password);
			self::$create=true;
			// Include mail server specific file here.
			if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "")){
				include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "");
			}
			$sql = "INSERT INTO x_mailboxes (mb_acc_fk,
											 mb_address_vc,
											 mb_created_ts) VALUES (
											 " . $currentuser['userid'] . ",
											 '" . $fulladdress . "',
											 " . time() . ")";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			self::$ok = true;
		}
	}

	static function DeleteMailbox($mb_id_pk){
		global $zdbh;
		global $controller;
		self::$delete=true;
		// Include mail server specific file here.
		if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "")){
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "");
		}
		$sql = "UPDATE x_mailboxes SET mb_deleted_ts=" . time() . " WHERE mb_id_pk=" . $mb_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
	}
	
	static function SaveMailbox($mb_id_pk){
		global $zdbh;
		global $controller;
		$password = $controller->GetControllerRequest('FORM', 'inPassword');
		self::$update=true;
		// Include mail server specific file here.
		if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "")){
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "");
		}
		if ($controller->GetControllerRequest('FORM', 'inEnabled') == 1){
			$sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=1 WHERE mb_id_pk='" . $mb_id_pk . "'");
			$sql->execute();
		} else {
			$sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_enabled_in=0 WHERE mb_id_pk='" . $mb_id_pk . "'");
			$sql->execute();				
		}
		self::$ok = true;
		return;
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

    static function GetMailOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT mbs_value_tx FROM x_mail_settings WHERE mbs_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['mbs_value_tx'];
        } else {
            return false;
        }
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
		
}

?>