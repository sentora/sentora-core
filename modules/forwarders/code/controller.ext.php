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
	static $alreadyexistssame;
	static $alreadyexistsforwarder;
	static $alreadyexistsalias;
	static $validemail;
	static $noaddress;

    static function getForwardList() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_forwarders WHERE fw_acc_fk=" . $currentuser['userid'] . " AND fw_deleted_ts IS NULL ORDER BY fw_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowforwarders = $sql->fetch()) {
				if ($rowforwarders['fw_keepmessage_in'] == 1){
					$status = "<a href=\"#\" title=\"".ui_language::translate("A copy of the original message will be left in the source mailbox address when it is fowarded to the destination address")."\"><img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\"></a>";
				} else {
					$status = "<a href=\"#\" title=\"".ui_language::translate("The original message will only be available in the destination address")."\"><img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\"></a>";
				}
                array_push($res, array('address'     => $rowforwarders['fw_address_vc'],
									   'destination' => $rowforwarders['fw_destination_vc'],
									   'status'      => $status,
									   'id' 	     => $rowforwarders['fw_id_pk']));
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
										   'id' 	 => $rowmailboxes['mb_id_pk']));
				}
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

    static function getForwardUsagepChart() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $forwardersquota = $currentuser['forwardersquota'];
        $forwarders = fs_director::GetQuotaUsages('forwarders', $currentuser['userid']);
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

	static function doAddForwarder(){
		self::AddForwarder();	
	}

	static function doDeleteForwarder(){
		global $controller;
		$fowarders = self::getForwardList();
		foreach ($fowarders as $fowarder){
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$fowarder['id'].''))) {
				self::DeleteForwarder($fowarder['id']);
				self::$ok = true;
				return;
			}
		}
	}

	static function AddForwarder(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors())) {
			$address = $controller->GetControllerRequest('FORM', 'inAddress');
			$destination = str_replace(' ', '', $controller->GetControllerRequest('FORM', 'inDestinationName') . "@" . $controller->GetControllerRequest('FORM', 'inDestinationDomain'));
			$destination = strtolower($destination);
			$keepmessage = fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'inKeepMessage'));
			// Include mail server specific file here.
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "");
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
			self::$ok = true;
		}
	}

	static function DeleteForwarder($fw_id_pk){
		global $zdbh;
		global $controller;
		// Include mail server specific file here.
		include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . self::GetMailOption('mailserver_php') . "");
		$sql = "UPDATE x_forwarders SET fw_deleted_ts=" . time() . " WHERE fw_id_pk=" . $fw_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		self::$ok = true;
	}
	
	static function CheckCreateForErrors(){
		global $zdbh;
        global $controller;
		$address = $controller->GetControllerRequest('FORM', 'inAddress');
		$destination = str_replace(' ', '', $controller->GetControllerRequest('FORM', 'inDestinationName') . "@" . $controller->GetControllerRequest('FORM', 'inDestinationDomain'));
		$destination = strtolower($destination);
		if (fs_director::CheckForEmptyValue($address)){
			self::$noaddress = true;
			return true;
		}
		if (!self::IsValidEmail($destination)){
			self::$validemail = true;
			return true;
		}
        if ($address == $destination) {
			self::$alreadyexistssame = true;
			return true;
		}
        $sql = "SELECT * FROM x_forwarders WHERE fw_address_vc='" . $address . "' AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsforwarder = true;
			return true;
		}
        $sql = "SELECT * FROM x_forwarders WHERE fw_address_vc='" . $destination . "' AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsforwarder = true;
			return true;
		}
        $sql = "SELECT * FROM x_aliases WHERE al_address_vc='" . $destination . "' AND al_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsalias = true;
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
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistssame)) {
            return ui_sysmessage::shout("You cannot forward a mailbox to itself!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsforwarder)) {
            return ui_sysmessage::shout("A forwarder already exists with that address!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsalias)) {
            return ui_sysmessage::shout("An alias already exists with that destination address!", "zannounceerror");
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
            return ui_sysmessage::shout("Changes to your forwarders have been saved successfully!", "zannounceok");
        } else {
            return NULL;
        }
        return;
    }
		
}

?>