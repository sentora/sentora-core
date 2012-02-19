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
	static $alreadyexists;
	static $validemail;
	static $noaddress;
	static $delete;
	static $create;
	static $deleteuser;
	static $createuser;
	
    /**
     * The 'worker' methods.
     */
	 
    static function ListDist($uid) {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $sql = "SELECT * FROM x_distlists WHERE dl_acc_fk=" . $currentuser['userid'] . " AND dl_deleted_ts IS NULL ORDER BY dl_address_vc ASC";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowdistlist = $sql->fetch()) {
				$numrowmb = $zdbh->query("SELECT COUNT(*) FROM x_distlistusers WHERE du_distlist_fk=" . $rowdistlist['dl_id_pk'] . " AND du_deleted_ts IS NULL")->fetch();
                array_push($res, array('address' => $rowdistlist['dl_address_vc'],
									   'totalmb' => $numrowmb[0],
									   'id' 	 => $rowdistlist['dl_id_pk']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentDist($id) {
        global $zdbh;
        $sql = "SELECT * FROM x_distlists WHERE dl_id_pk=" . $id . " AND dl_deleted_ts IS NULL";
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
	
    static function ListDistUsers($id) {
        global $zdbh;
        global $controller;
        $result = $zdbh->query("SELECT * FROM x_distlists WHERE dl_id_pk=" . $id . " AND dl_deleted_ts IS NULL")->Fetch();
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
	
    static function ListMailbox($uid) {
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
	
	static function ExecuteAddDistList($uid, $inAddress, $inDomain){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail($uid);
		$fulladdress = $inAddress . "@" . $inDomain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($inAddress, $inDomain))) {
			return false;
		}
		runtime_hook::Execute('OnBeforeAddDistList');
		self::$create = true;
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
		runtime_hook::Execute('OnAfterAddDistList');
		self::$ok = true;
		return true;
	}

	static function ExecuteDeleteDistList($dl_id_pk){
		global $zdbh;
		global $controller;
		runtime_hook::Execute('OnBeforeDeleteDistList');
		self::$delete = true;
		$rowdl = $zdbh->query("SELECT * FROM x_distlists WHERE dl_id_pk=" . $dl_id_pk . " AND dl_deleted_ts IS NULL")->fetch();
		// Include mail server specific file here.
		include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		$sql = "UPDATE x_distlistusers SET du_deleted_ts=" . time() . " WHERE du_distlist_fk=" .  $dl_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		$sql = "UPDATE x_distlists SET dl_deleted_ts=" . time() . " WHERE dl_id_pk=" . $dl_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		runtime_hook::Execute('OnAfterDeleteDistList');
		self::$ok = true;
	}
	
	static function ExecuteAddDistListUser($du_distlist_fk, $address, $domain, $dladdress){
		global $zdbh;
        global $controller;
		$fulladdress = $address . "@" . $domain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrorsDistListUser())) {
			return false;
		}
		$rowdl = $zdbh->query("SELECT * FROM x_distlists WHERE dl_id_pk=" . $du_distlist_fk . " AND dl_deleted_ts IS NULL")->fetch();
		runtime_hook::Execute('OnBeforeAddDistListUser');
		self::$createuser = true;
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
		runtime_hook::Execute('OnAfterAddDistListUser');
		self::$ok = true;
		return true;
	}
	
	static function ExecuteDeleteDistListUser($du_id_pk){
		global $zdbh;
		global $controller;
		$rowdlu = $zdbh->query("SELECT * FROM x_distlistusers WHERE du_id_pk=" . $du_id_pk . " AND du_deleted_ts IS NULL")->fetch();
		$rowdl = $zdbh->query("SELECT * FROM x_distlists WHERE dl_id_pk=" . $rowdlu['du_distlist_fk'] . " AND dl_deleted_ts IS NULL")->fetch();
		$dladdress   = $rowdl['dl_address_vc'];
		runtime_hook::Execute('OnBeforeDeleteDistListUser');
		// Include mail server specific file here.
		self::$deleteuser = true;
		include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetOption('mailserver_php') . "");
		$sql = "UPDATE x_distlistusers SET du_deleted_ts=" . time() . " WHERE du_id_pk=" . $du_id_pk . "";
		$sql = $zdbh->prepare($sql);
		$sql->execute();
		runtime_hook::Execute('OnAfterDeleteDistListUser');
		self::$ok = true;
		return true;
	}

	static function CheckCreateForErrors($inAddress, $inDomain){
		global $zdbh;
        global $controller;
		$fulladdress = $inAddress . "@". $inDomain;
		$fulladdress = str_replace(' ', '', $fulladdress);
		$fulladdress = strtolower($fulladdress);
		if (fs_director::CheckForEmptyValue($inAddress)){
			self::$noaddress = true;
			return false;
		}
		if (!self::IsValidEmail($fulladdress)){
			self::$validemail = true;
			return false;
		}
        $sql = "SELECT * FROM x_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexists = true;
			return false;
		}
        $sql = "SELECT * FROM x_forwarders WHERE fw_address_vc='" . $fulladdress . "' AND fw_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexists = true;
			return false;
		}
        $sql = "SELECT * FROM x_distlists WHERE dl_address_vc='" . $fulladdress . "' AND dl_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexists = true;
			return false;
		}
        $sql = "SELECT * FROM x_aliases WHERE al_address_vc='" . $fulladdress . "' AND al_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexists = true;
			return false;
		}
		return true;
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
			return false;
		}
		if (!self::IsValidEmail($fulladdress)){
			self::$validemail = true;
			return false;
		}
        $sql = "SELECT * FROM x_distlistusers WHERE du_distlist_fk=" . $dlid . "  AND du_address_vc='" . $fulladdress . "' AND du_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
			self::$alreadyexistsdu = true;
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

    /**
     * Webinterface sudo methods.
     */

    static function doEditDistList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListDist($currentuser['userid']) as $row) {
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

    static function doConfirmDeleteDistList() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteDistList($formvars['inDelete']))
            return true;
        return false;
    }

    static function doUpdateDistList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
       	if (isset($formvars['inAdd'])) {
        	if(self::ExecuteAddDistListUser($formvars['inDLID'], $formvars['inAddAddress'], $formvars['inAddDomain'], $formvars['inDLAD'])){
				header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $formvars['inDLID'] . "&status=ok");
            	exit;
			}
        }
        foreach (self::ListDistUsers($formvars['inDLID']) as $row) {
            if (isset($formvars['inDeleteUser_' . $row['id'] . ''])) {
				if (self::ExecuteDeleteDistListUser($formvars['inDeleteUser_' . $row['id'] . ''])){
					header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $formvars['inDLID'] . "&status=ok");
            		exit;
				}
            }
        }
        return;
    }

    static function doAddDistList() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteAddDistList($currentuser['userid'], $formvars['inAddress'], $formvars['inDomain']))
            return true;
        return false;
    }

    static function getDistList() {
		global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDist($currentuser['userid']);
    }

    static function getDistListUsers() {
		global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDistUsers($controller->GetControllerRequest('URL', 'other'));
    }

    static function getCurrentDistListID() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDist($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['id'];
        } else {
            return "";
        }
    }

    static function getCurrentDistList() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDist($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['address'];
        } else {
            return "";
        }
    }

    static function getisEditDistList() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit"))
            return true;
        return false;
    }

    static function getisDeleteDistList() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisCreateDistList() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
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

    static function getQuotaLimit() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['distrobutionlistsquota'] > fs_director::GetQuotaUsages('distlists', $currentuser['userid'])) {
            return true;
        } else {
            return false;
        }
    }

    static function getResultURL() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (isset($urlvars['status']) && $urlvars['status'] == 'ok'){
			return ui_sysmessage::shout("Changes to your distrubution lists have been saved successfully!", "zannounceok");
		}
    }
	
    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout("A mailbox, alias, forwarder or distrubution list already exists with that name.", "zannounceerror");
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