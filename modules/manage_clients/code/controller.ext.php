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

	static $complete;
	static $error;
	static $alreadyexists;
	static $badname;
	static $blank;
	static $ok;
	static $edit;
	static $clientid;
	static $clientpkgid;

	static function getCurrentClients(){
		global $controller;
			$display = self::DisplayCurrentClient();
		return $display;
	}
	
	
	
	
	static function getClientAction(){
		global $controller;
		if (!fs_director::CheckForEmptyValue(self::$edit)){
			$display = self::DisplayEditClient();
		} else {
			$display = self::DisplayNewClient();
		}
		return $display;
	}
	
	
	

	static function DisplayCurrentClient(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line = "";
		$sql  = "SELECT * FROM x_accounts JOIN (x_bandwidth, x_quotas, x_packages) ON (x_accounts.ac_id_pk=x_bandwidth.bd_acc_fk AND x_accounts.ac_package_fk=x_quotas.qt_package_fk AND x_accounts.ac_package_fk=x_packages.pk_id_pk) WHERE ac_reseller_fk=" . $currentuser['userid'] . " AND ac_deleted_ts IS NULL";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() <> 0) {
				$line .= "<form action=\"./?module=manage_clients&action=EditClient\" method=\"post\">";
    			$line .= "<table class=\"zgrid\">";
 				$line .= "<tr>";
				$line .= "<th>Username</th>";
				$line .= "<th>Package</th>";
				$line .= "<th>Current Disk</th>";
				$line .= "<th>Current Bandwidth</th>";
				$line .= "<th></th>";
				$line .= "</tr>";
				$sql  = $zdbh->prepare($sql);
				$sql->execute();
				while ($rowclients = $sql->fetch()) {
					$line .= "<tr>";
                    $line .= "<td>" . $rowclients['ac_user_vc'] . "</td>";
					$package = $zdbh->query("SELECT pk_name_vc FROM x_packages WHERE pk_id_pk=" . $rowclients['ac_package_fk'] . "")->Fetch();
                    $line .= "<td>" . $rowclients['pk_name_vc'] . "</td>";
                    /* NOTE the disk space and bandwith values below need converting to MB / GB */
                    $line .= "<td>" . $rowclients['bd_diskamount_bi'] . "/" . $rowclients['qt_diskspace_bi'] . "</td>";
                    $line .= "<td>" . $rowclients['bd_transamount_bi'] . "/" . $rowclients['qt_bandwidth_bi'] . "</td>";
                    $line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inEdit_" . $rowclients['ac_id_pk'] . "\" value=\"" . $rowclients['ac_id_pk'] . "\">Edit</button>";
                    if ($rowclients['ac_user_vc'] != 'zadmin') {
                    	$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDelete_" . $rowclients['ac_id_pk'] . "\" value=\"" . $rowclients['ac_id_pk'] . "\">Delete</button>";
         			}
					$line .= "<input type=\"hidden\" name=\"inEdit\" value=\"edit\">";
					$line .= "<input type=\"hidden\" name=\"edit\" value=\"" . $rowclients['ac_id_pk'] . "\">";
                    $line .= "</td>";
                	$line .= "</tr>";
				}
				$line .= " </table>";
				$line .= "</form>";
			} else {
			$line .= "You have no client accounts at this time";
			}
		}
		return $line;
	}
	
	
	
	
	static function DisplayNewClient(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "";
		$line .= "<h2>Create new client account</h2>";
		$line .= "<form action=\"./?module=manage_clients&action=CreateClient\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>Username:</th>";
        $line .= "<td><input type=\"text\" name=\"inUserName\" id=\"inUserName\" maxlength=\"10\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Password:</th>";
        $line .= "<td><input type=\"text\" name=\"inPassword\" id=\"inPassword\" value=\"" . /*GenerateRandomPassword(9, 4)*/$currentuser['userid'] . "\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Package:</th>";
        $line .= "<td><select name=\"inPackage\" id=\"inPackage\">";
        $line .= "<option value=\"\" selected=\"selected\">-- Select a package --</option>";
		$sql  = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL");
		$sql->execute();
    	while ($rowpackages = $sql->fetch()) {
        	$line .= "<option value=\"" . $rowpackages['pk_id_pk'] . "\">" . $rowpackages['pk_name_vc'] . "</option>";
    	}
        $line .= "</select></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Full Name:</th>";
        $line .= "<td><input type=\"text\" name=\"inFullName\" id=\"inFullName\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Email Address:</th>";
        $line .= "<td><input type=\"text\" name=\"inEmailAddress\" id=\"inEmailAddress\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Postal Address</th>";
        $line .= "<td><textarea name=\"inAddress\" id=\"inAddress\" cols=\"45\" rows=\"5\"></textarea></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Postal Code</th>";
        $line .= "<td><input name=\"inPostCode\" type=\"text\" id=\"inPostCode\" value=\"\" size=\"20\" maxlength=\"10\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Phone Number</th>";
        $line .= "<td><input name=\"inPhone\" type=\"text\" id=\"inPhone\" value=\"\" size=\"20\" maxlength=\"50\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Send welcome email?</th>";
        $line .= "<td><input name=\"inSWE\" type=\"checkbox\" id=\"inSWE\" value=\"1\" checked=\"checked\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th><input type=\"hidden\" name=\"inReturn\" value=\"\" /><input type=\"hidden\" name=\"inAction\" value=\"new\" /></th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSubmit\" value=\"Save\">Save</button></td>";
        //$line .= "<input type=\"submit\" name=\"inSubmit\" id=\"inSubmit\" value=\"Save\" /></th>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		return $line;
	}
	
	
	
	
	static function DisplayEditClient(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$rowclient = $zdbh->query("SELECT * FROM x_accounts WHERE ac_id_pk=" . self::$clientid . " AND ac_deleted_ts IS NULL AND ac_reseller_fk=" . $currentuser['userid'] . "")->Fetch();
		$line  = "";
		$line .= "<h2>Edit existing client</h2>";
    	$line .= "<form action=\"./?module=manage_clients&action=SaveClient\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>Username:</th>";
        $line .= "<td><input name=\"inUserName\" type=\"text\" disabled=\"disabled\" maxlength=\"10\" id=\"inUserName\" value=\"" . $rowclient['ac_user_vc'] . "\" readonly=\"readonly\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Package:</th>";
        $line .= "<td>";
		$sql  = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL");
		$sql->execute();
        if ($currentuser['username'] != 'zadmin'){
        	$line .= "<select name=\"inPackage\" id=\"inPackage\">";  
			while ($rowpackages = $sql->fetch()) {
            	$line .= "<option value=\"" . $rowpackages['pk_id_pk'] . "\""; 
				if ($rowpackages['pk_id_pk'] == $rowclient['ac_package_fk']) {
                    $line .= " selected ";
                } 
				$line .= ">" . $rowpackages['pk_name_vc'] . "</option>";
        	}
            $line .= "</select>";
    	} else {
			$line .= "<input type=\"text\" disabled=\"disabled\" maxlength=\"10\" value=\"" . $rowpackages['pk_name_vc'] . "\" readonly=\"readonly\" />";
			$line .= "<input type=\"hidden\" name=\"inPackage\" id=\"inPackage\" value=\"" . $rowpackages['pk_id_pk'] . "\" />";
    	}
		$rowpersonal = $zdbh->query("SELECT * FROM x_profiles WHERE ud_user_fk=" . self::$clientid . "")->Fetch();
        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Full name:</th>";
        $line .= "<td><input type=\"text\" name=\"inFullName\" id=\"inFullName\" value=\"" . $rowpersonal['ud_fullname_vc'] . "\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Email Address:</th>";
        $line .= "<td><input type=\"text\" name=\"inEmailAddress\" id=\"inEmailAddress\" value=\"" . $rowpersonal['ud_email_vc'] . "\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Postal Address:</th>";
        $line .= "<td><textarea name=\"inAddress\" id=\"inAddress\" cols=\"45\" rows=\"5\">" . $rowpersonal['ud_address_tx'] . "</textarea></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Postal Code:</th>";
        $line .= "<td><input name=\"inPostCode\" type=\"text\" id=\"inPostCode\" size=\"20\" maxlength=\"10\" value=\"" . $rowpersonal['ud_postcode_vc'] . "\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Phone Number:</th>";
        $line .= "<td><input name=\"inPhone\" type=\"text\" id=\"inPhone\" size=\"20\" maxlength=\"50\" value=\"" . $rowpersonal['ud_phone_vc'] . "\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Reset password:</th>";
        $line .= "<td><input name=\"inNewPassword\" type=\"password\" id=\"inNewPassword\" size=\"20\" maxlength=\"50\" /> ";
        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>";
        $line .= "<input type=\"hidden\" name=\"inClientID\" value=\"" . $rowclient['ac_id_pk'] . "\" />";
        $line .= "<input type=\"hidden\" name=\"inClientName\" value=\"" . $rowclient['ac_user_vc'] . "\" />";
        $line .= "</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSubmit\" value=\"Save\">Save</button></td>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		return $line;
	}
	
	
	
			
	static function doEditClient(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk=" . $currentuser['userid'] . " AND ac_deleted_ts IS NULL");
		$sql->execute();
		while ($rowclients = $sql->fetch()) {
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inEdit_' . $rowclients['ac_id_pk'] . ''))){
				self::$edit=1;
				self::$clientid = $rowclients['ac_id_pk'];
				return;
			}
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $rowclients['ac_id_pk'] . ''))){
				self::DeleteClient($rowclients['ac_id_pk']);
				return;
			}
		}		

	}
	
	
	
	
	static function doSaveClient(){
		global $zdbh;
		global $controller;
	    $sql = $zdbh->prepare("UPDATE x_accounts SET 
										ac_package_fk= " . $controller->GetControllerRequest('FORM', 'inPackage')  . " 
										WHERE ac_id_pk=" . $controller->GetControllerRequest('FORM', 'inClientID') . "");
		$sql->execute();
  
	    $sql = $zdbh->prepare("UPDATE x_profiles SET 
										ud_fullname_vc= '" . $controller->GetControllerRequest('FORM', 'inFullName')     . "',
		     							ud_email_vc=    '" . $controller->GetControllerRequest('FORM', 'inEmailAddress') . "',
										ud_address_tx=  '" . $controller->GetControllerRequest('FORM', 'inAddress')      . "',
										ud_postcode_vc= '" . $controller->GetControllerRequest('FORM', 'inPostCode')     . "',
										ud_phone_vc=    '" . $controller->GetControllerRequest('FORM', 'inPhone')        . "'
										WHERE ud_user_fk=" . $controller->GetControllerRequest('FORM', 'inClientID')     . "");
	    $sql->execute();
	    # See if a password reset has been initiated! - Added in ZPanel 5.1.0
	    if ($controller->GetControllerRequest('FORM', 'inNewPassword') <> "") {
	        $resetforuser = $controller->GetControllerRequest('FORM', 'ac_user_vc');
	        //zapi_mysqluser_setpass($resetforuser, Cleaner("i", $_POST['inNewPassword']), $zdb);
	    }
		self::$ok=1;	
	}
	
	
	
	
	static function doCreateClient(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$acc_fk = $currentuser['userid'];
    	$username = $controller->GetControllerRequest('FORM', 'inUserName');
	    $packageid = $controller->GetControllerRequest('FORM', 'inPackage');
		$password = $controller->GetControllerRequest('FORM', 'inPassword');
	    # Check for spaces and remove if found...
	    $username = str_replace(' ', '', $username);
	    # Check to make sure the username is not blank or exists before we go any further...
		if (!fs_director::CheckForEmptyValue($username)) {
		$sql = "SELECT COUNT(*) FROM x_accounts WHERE UPPER(ac_user_vc)='" . strtoupper($username) . "' AND ac_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
					self::$alreadyexists=1;
					return;				
				}
			}
		} else {
			self::$blank=1;
			return;		
		}
	    # Check to make sure the packagename is not blank and exists before we go any further...
		if (!fs_director::CheckForEmptyValue($packageid)) {
		$sql = "SELECT COUNT(*) FROM x_packages WHERE pk_id_pk='" . $packageid . "' AND pk_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() == 0) {
					self::$error=1;
					return;				
				}
			}
		} else {
			self::$error=1;
			return;		
		}
	    # If the user submitted a 'new' request then we will simply add the client to the database...
	    $sql = $zdbh->prepare("INSERT INTO x_accounts (
										ac_user_vc,
										ac_pass_vc,
										ac_package_fk,
										ac_reseller_fk,
										ac_created_ts) VALUES (
										'" . $username . "',
										'" . md5($password) . "',
										'" . $packageid . "',
										" . $acc_fk . ",
										" . time() . ")");
	    $sql->execute();
		# Now lets pull back the client ID so that we can add their personal address details etc...
	    $client = $zdbh->query("SELECT * FROM x_accounts WHERE ac_reseller_fk=" . $acc_fk . " ORDER BY ac_id_pk DESC")->Fetch();
	    $sql = $zdbh->prepare("INSERT INTO x_profiles (ud_user_fk,
										ud_fullname_vc,
										ud_email_vc,
										ud_address_tx,
										ud_postcode_vc,
										ud_phone_vc,
										ud_created_ts) VALUES (
										 " . $client['ac_id_pk'] . ",
										'" . $controller->GetControllerRequest('FORM', 'inFullName') . "',
										'" . $controller->GetControllerRequest('FORM', 'inEmailAddress') . "',
										'" . $controller->GetControllerRequest('FORM', 'inAddress') . "',
										'" . $controller->GetControllerRequest('FORM', 'inPostCode') . "',
										'" . $controller->GetControllerRequest('FORM', 'inPhone') . "',
										 " . time() . ")");
		$sql->execute();
		# Now we add an entry into the bandwidth table, for the user for the upcoming month.
    	$sql = $zdbh->prepare("INSERT INTO x_bandwidth (bd_acc_fk, bd_month_in, bd_transamount_bi, bd_diskamount_bi) VALUES (" . $client['ac_id_pk'] . "," . date("Ym", time()) . ", 0, 0)");
		$sql->execute();
		
		# Create the MySQL account for the user...
		# Now we create the user's home directory if it doesnt already exsist...
		# Create the domain logs folder read for Apache...
		# Create a default FTP account if set in the system options...
		# Send the user account details via. email (if requested)...
	}
	
	
	
	
	static function DeleteClient($ac_id_pk){
		global $zdbh;
		global $controller;
		
		# Delete all cron jobs
		# Delete all mailboxes
		# Delete all forwarders
		# Delete all distrubution lists
		# Delete all VHOSTs (parked, sub and tld)
		# Delete all MySQL databases that the user has
		# Delete the MySQL user account for the user
		# Delete all FTP accounts that the user has.
		# Delete the user's home directory!
		# Delete the user's ZPanel login account
		$sql = $zdbh->prepare("UPDATE x_accounts SET ac_deleted_ts=" . time() . " WHERE ac_id_pk=" . $ac_id_pk . "");
		$sql->execute();
		# We reload the FTP server here as there will be the requirement to do so...
		self::$ok=1;
	}
	
	
	
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout("You need to specify a username to create a new client.");
		}
		if (!fs_director::CheckForEmptyValue(self::$badname)){
		return ui_sysmessage::shout("Your client name is not valid. Please enter a valid client name.");
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout("A client with that name already appears to exsist on this server.");
		}	
		if (!fs_director::CheckForEmptyValue(self::$error)){
		return ui_sysmessage::shout("You must select a package for your new client");
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout("Changes to your client(s) have been saved successfully!");
		}else{
			return ui_module::GetModuleDescription();
		}
        return;
    }
	
	
	
	
	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
    	return $module_name;
    }
	
	
	
	
	static function getModuleIcon() {
		global $controller;
		$module_icon = "/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
	
}

?>
