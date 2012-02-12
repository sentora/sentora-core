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
	static $edit;
	static $alreadyexistsmail;
	static $alreadyexistsforwarder;
	static $alreadyexistsdu;
	static $alreadyexistsalias;
	static $validemail;
	static $noaddress;

    static function getDistList() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_distlists WHERE dl_acc_fk=" . $currentuser['userid'] . " AND dl_deleted_ts IS NULL ORDER BY dl_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowdistlist = $sql->fetch()) {
                array_push($res, array('address'     => $rowdistlist['dl_address_vc'],
									   'id' 	     => $rowdistlist['dl_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }
	
    static function getDistListUsers() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $result = $zdbh->query("SELECT * FROM x_distlists WHERE dl_acc_fk=" . $currentuser['userid'] . " AND dl_id_pk=" . self::$edit . " AND dl_deleted_ts IS NULL")->Fetch();
        if ($result) {
	        $sql = "SELECT * FROM x_distlistusers WHERE du_distlist_fk=" . $result['dl_id_pk'] . " AND du_deleted_ts IS NULL";
	        $numrows = $zdbh->query($sql);
	        if ($numrows->fetchColumn() <> 0) {
	            $sql = $zdbh->prepare($sql);
	            $res = array();
	            $sql->execute();
	            while ($rowdistlist = $sql->fetch()) {
	                array_push($res, array('address' => $rowdistlist['du_address_vc'],
										   'distlist'=> $result['dl_address_vc'],
										   'id' 	 => $rowdistlist['du_id_pk']));
	            }
	            return $res;
	        } else {
	            return false;
	        }	
		}
		return false;
    }
	
    static function getAllDistListUser() {
        global $zdbh;
        $result = $zdbh->query("SELECT * FROM x_distlistusers WHERE du_deleted_ts IS NULL")->Fetch();
        if ($result) {
	    	return $result;
	    } 
    }
	
    static function getDistListAddress($dl_id_pk) {
        global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
        $result = $zdbh->query("SELECT * FROM x_distlists WHERE dl_acc_fk=" . $currentuser['userid'] . " AND dl_id_pk=" . $dl_id_pk . " AND dl_deleted_ts IS NULL")->Fetch();
        if ($result) {
			$distlistaddress = $result['dl_address_vc'];
	    	return $distlistaddress;
	    } 
    }
	
    static function getEdit() {
		if (!fs_director::CheckForEmptyValue(self::$edit)) {
			$retval = self::$edit;
            return $retval;
        } else {
            return false;
        }
    }

    static function getEditAddress() {
        global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (!fs_director::CheckForEmptyValue(self::$edit)) {
        	$result = $zdbh->query("SELECT * FROM x_distlists WHERE dl_acc_fk=" . $currentuser['userid'] . " AND dl_id_pk=" . self::$edit . " AND dl_deleted_ts IS NULL")->Fetch();
        	if ($result) {
				$retval = $result['dl_address_vc'];
            	return $retval;
			}
        } else {
            return false;
        }
    }

	static function getDistListUserAddress($du_id_pk){
		global $zdbh;
		global $controller;
	    $result = $zdbh->query("SELECT du_address_vc FROM x_distlistusers WHERE du_id_pk='" . $du_id_pk . "'")->Fetch();
			if ($result) {
				return $result['du_address_vc'];
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
                array_push($res, array('address' => $rowmailboxes['mb_address_vc'],
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
                array_push($res, array('domain' => $rowdomains['vh_name_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getDistListUsagepChart() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $distlistquota = $currentuser['distrobutionlistsquota'];
        $distlist = fs_director::GetQuotaUsages('distlists', $currentuser['userid']);
        $total = $distlistquota;
        $used = $distlist;
        $free = $total - $used;
        $line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=" . $free . "::" . $used . "&labels=Free: " . $free . "::Used: " . $used . "&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";
        return $line;
    }

	static function doAddDistList(){
		self::AddDistList();	
	}

	static function doDeleteDistList(){
		global $controller;
		$distlists = self::getDistList();
		if (!fs_director::CheckForEmptyValue($distlists)) {
			foreach ($distlists as $address){
				if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$address['id'].''))) {
					self::DeleteDistList($address['id']);
					self:$edit = $address['id'];
					self::$ok = true;
					return;
				}
				if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inEdit_'.$address['id'].''))) {
					self::$edit = $address['id'];
					return;
				}
			}
		}
	}
	
	static function doEditDistList(){
		global $zdbh;
		global $controller;		
		$sql = "SELECT * FROM x_distlistusers WHERE du_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowlist = $sql->fetch()) {
				if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDeleteUser_'.$rowlist['du_id_pk'].''))) {
					self::DeleteDistListUser($rowlist['du_id_pk']);
					$dlid = $controller->GetControllerRequest('FORM', 'inDLID');
					self::$edit = $dlid;
					self::$ok = true;
					return;
				}
			}
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inAdd'))) {
			$dlid    = $controller->GetControllerRequest('FORM', 'inDLID');
			self::AddDistListUser($dlid);
			self::$edit = $dlid;
			self::$ok = true;
			return;
		}
	}

	static function AddDistList(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors())) {
			$address = $controller->GetControllerRequest('FORM', 'inAddress');
			$domain  = $controller->GetControllerRequest('FORM', 'inDomain');
			$fulladdress = $address . "@" . $domain;
			$fulladdress = str_replace(' ', '', $fulladdress);
			$fulladdress = strtolower($fulladdress);
			// Include mail server specific file here.
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
			$sql = "INSERT INTO x_distlists (dl_acc_fk,
											  dl_address_vc,
											  dl_created_ts) VALUES (
											  " . $currentuser['userid'] . ",
											  '" . $fulladdress . "',
											  " . time() . ")";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			self::$ok = true;
		}
	}

	static function DeleteDistList($dl_id_pk){
		global $zdbh;
		global $controller;
		// Include mail server specific file here.
		include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		$sql = "UPDATE x_distlistusers SET du_deleted_ts=" . time() . " WHERE du_distlist_fk=" .  $dl_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		$sql = "UPDATE x_distlists SET dl_deleted_ts=" . time() . " WHERE dl_id_pk=" . $dl_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		self::$ok = true;
	}
	
	static function AddDistListUser($du_distlist_fk){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$address     = $controller->GetControllerRequest('FORM', 'inAddAddress');
		$domain      = $controller->GetControllerRequest('FORM', 'inAddDomain');
		$dladdress   = $controller->GetControllerRequest('FORM', 'inDLAD');
		$fulladdress = $address . "@" . $domain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrorsDistListUser())) {
			// Include mail server specific file here.
			include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
       		$sql = "INSERT INTO x_distlistusers (
											du_distlist_fk,
											du_address_vc,
											du_created_ts) VALUES (
											" . $du_distlist_fk . ",
											'" . $fulladdress . "',
											" . time() . ")";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			self::$ok = true;
		}
	}
	
	static function DeleteDistListUser($du_id_pk){
		global $zdbh;
		global $controller;
		$dladdress   = $controller->GetControllerRequest('FORM', 'inDLAD');
		// Include mail server specific file here.
		include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		$sql = "UPDATE x_distlistusers SET du_deleted_ts=" . time() . " WHERE du_id_pk=" . $du_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		self::$ok = true;
	}

	static function CheckCreateForErrors(){
		global $zdbh;
        global $controller;
		$address = $controller->GetControllerRequest('FORM', 'inAddress');
		$domain  = $controller->GetControllerRequest('FORM', 'inDomain');
		$fulladdress = $address . "@". $domain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		$destination = $controller->GetControllerRequest('FORM', 'inDestination');
		if (fs_director::CheckForEmptyValue($address)){
			self::$noaddress = true;
			return true;
		}
		if (!self::IsValidEmail($fulladdress)){
			self::$validemail = true;
			return true;
		}
        $sql = "SELECT * FROM x_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsmail = true;
			return true;
		}
        $sql = "SELECT * FROM x_forwarders WHERE fw_address_vc='" . $fulladdress . "' AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsforwarder = true;
			return true;
		}
        $sql = "SELECT * FROM x_forwarders WHERE fw_destination_vc='" . $fulladdress . "' AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsforwarder = true;
			return true;
		}
        $sql = "SELECT * FROM x_aliases WHERE al_address_vc='" . $fulladdress . "' AND al_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsalias = true;
			return true;
		}
		return false;
	}
	
	static function CheckCreateForErrorsDistListUser(){
		global $zdbh;
        global $controller;
		$address = $controller->GetControllerRequest('FORM', 'inAddAddress');
		$domain  = $controller->GetControllerRequest('FORM', 'inAddDomain');
		$dlid    = $controller->GetControllerRequest('FORM', 'inDLID');
		$fulladdress = $address . "@". $domain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue($address)){
			self::$noaddress = true;
			return true;
		}
		if (!self::IsValidEmail($fulladdress)){
			self::$validemail = true;
			return true;
		}
        $sql = "SELECT * FROM x_distlistusers WHERE du_distlist_fk=" . $dlid . "  AND du_address_vc='" . $fulladdress . "' AND du_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsdu = true;
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
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsmail)) {
            return ui_sysmessage::shout("A mailbox already exists with that alias address!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsforwarder)) {
            return ui_sysmessage::shout("A forwarder already exists with that address!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsalias)) {
            return ui_sysmessage::shout("An alias already exists with that address!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexistsdu)) {
            return ui_sysmessage::shout("That email aready exists on this distrubution list!", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$validemail)) {
            return ui_sysmessage::shout("Your email address is not valid.", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$noaddress)) {
            return ui_sysmessage::shout("Your email address cannot be blank.", "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout("Changes to your distrubution lists have been saved successfully!", "zannounceok");
        } else {
            return NULL;
        }
        return;
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
		
}

?>