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

	static $error;
	static $alreadyexists;
	static $badname;
	static $blank;
	static $ok;
	static $edit;
	static $package_to_edit;

	static function getPackageAction(){
		global $controller;
		if (!fs_director::CheckForEmptyValue(self::$edit)){
			$display = self::DisplayEditPackage();
		} else {
			$display = self::DisplayNewPackage();
		}
		return $display;
	}
	
	
    static function getCurrentPackages() {
		$display = self::DisplayCurrentPackages();
		return $display;		
    }



	#Begin Display Methods
    static function DisplayCurrentPackages() {
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$totalpackages = 1;
		$line  = "";
		$line .= "<h2>Current packages</h2>";
		$numrows = $zdbh->query("SELECT COUNT(*) FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL")->fetchColumn(); 
	    $totalpackages = count($numrows); 
		if ($totalpackages <> 0) {
	    	$line .= "<form action=\"./?module=packages&action=EditPackage\" method=\"post\">";
	        $line .= "<table class=\"zgrid\">";
	        $line .= "<tr>";
	        $line .= "<th scope=\"row\">Package name</th>";
	        $line .= "<th>Created</th>";
	        $line .= "<th>No. of clients</th>";
	        $line .= "<th>&nbsp;</th>";
	        $line .= "</tr>";
			$sql = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL");   
			$sql->execute();
			while ($rowpackages = $sql->fetch()) {
				$numrows = $zdbh->query("SELECT COUNT(*) FROM x_accounts WHERE ac_package_fk=" . $rowpackages['pk_id_pk'] . "")->fetchColumn(); 
	            $totalclients = count($numrows);   
	            $line .= "<tr>";
	            $line .= "<td scope=\"row\">" . $rowpackages['pk_name_vc'] . "</td>";
	            $line .= "<td>" . date(ctrl_options::GetOption('zpanel_df'), $rowpackages['pk_created_ts']) . "</td>";
	            $line .= "<td>" . $totalclients . "</td>";
	            $line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inEdit_" . $rowpackages['pk_id_pk'] . "\" value=\"1\">Edit</button>";
	        	if ($rowpackages['pk_id_pk'] != 1) {
	            	$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDelete_" . $rowpackages['pk_id_pk'] . "\" value=\"1\">Delete</button>";
    	     	}
        	    $line .= "</td>";
	            $line .= "</tr>";
	     	}
	        $line .= "</table>";
	        $line .= "<input type=\"hidden\" name=\"inReturn\" value=\"\" /><input type=\"hidden\" name=\"inAction\" value=\"delete\" />";
	    	$line .= "</form>";
		} else {
		$line .= "You have no packages at this time";
		}
		
		return $line;	
    }


    static function DisplayNewPackage() {
		global $zdbh;
        global $controller;
		$line  = "";
		$line .= "<h2>Create a new package</h2>";
    	$line .= "<form action=\"./?module=packages&action=CreatePackage\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>Package name:</th>";
        $line .= "<td><input type=\"text\" name=\"inPackageName\" id=\"inPackageName\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Enable PHP:</th>";
        $line .= "<td><input type=\"checkbox\" name=\"inEnablePHP\" id=\"inEnablePHP\" value=\"1\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Enable CGI:</th>";
        $line .= "<td><input type=\"checkbox\" name=\"inEnableCGI\" id=\"inEnableCGI\" value=\"1\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Domains:</th>";
        $line .= "<td><input name=\"inNoDomains\" type=\"text\" id=\"inNoDomains\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Sub-domains:</th>";
        $line .= "<td><input name=\"inNoSubDomains\" type=\"text\" id=\"inNoSubDomains\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Parked domains:</th>";
        $line .= "<td><input name=\"inNoParkedDomains\" type=\"text\" id=\"inNoParkedDomains\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Mailboxes:</th>";
        $line .= "<td><input name=\"inNoMailboxes\" type=\"text\" id=\"inNoMailboxes\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Forwarders:</th>";
        $line .= "<td><input name=\"inNoFowarders\" type=\"text\" id=\"inNoFowarders\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Dist Lists:</th>";
        $line .= "<td><input name=\"inNoDistLists\" type=\"text\" id=\"inNoDistLists\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. FTP accounts:</th>";
        $line .= "<td><input name=\"inNoFTPAccounts\" type=\"text\" id=\"inNoFTPAccounts\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. MySQL databases:</th>";
        $line .= "<td><input name=\"inNoMySQL\" type=\"text\" id=\"inNoMySQL\" value=\"0\" size=\"5\" maxlength=\"3\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Disk space quota:</th>";
        $line .= "<td><input name=\"inDiskQuota\" type=\"text\" id=\"inDiskQuota\" value=\"0\" size=\"10\" maxlength=\"10\" /> MB (1000MB = 1GB)</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Monthly bandwidth quota:</th>";
        $line .= "<td><input name=\"inBandQuota\" type=\"text\" id=\"inBandQuota\" value=\"0\" size=\"10\" maxlength=\"10\" /> MB (1000MB = 1GB)</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th><input type=\"hidden\" name=\"inAction\" value=\"new\" /></th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSubmit\" value=\"Save\">Save</button></td>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		return $line;
    }	


    static function DisplayEditPackage() {
		global $zdbh;	
					$sql = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_id_pk=" . self::$package_to_edit . " AND pk_deleted_ts IS NULL");
					$sql->execute();
					$packageinfo = $sql->fetch();
					$sql = $zdbh->prepare("SELECT * FROM x_quotas WHERE qt_package_fk=" . self::$package_to_edit . "");
					$sql->execute();
					$quotainfo = $sql->fetch();
		$line  = "";
		$line .= "<h2>Edit package: ".$packageinfo['pk_name_vc']."</h2>";
    	$line .= "<form action=\"./?module=packages&action=SavePackage\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>Package name:</th>";
        $line .= "<td><input type=\"text\" name=\"inPackageName\" id=\"inPackageName\" value=\"".$packageinfo['pk_name_vc']."\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Enable PHP:</th>";
        $line .= "<td><input type=\"checkbox\" name=\"inEnablePHP\" id=\"inEnablePHP\" value=\"1\"";
		if (fs_director::GetCheckboxValue($packageinfo['pk_enablephp_in']) == 1){
			$line .= " CHECKED";
		}
		$line .= "/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Enable CGI:</th>";
        $line .= "<td><input type=\"checkbox\" name=\"inEnableCGI\" id=\"inEnableCGI\" value=\"1\"";
		if (fs_director::GetCheckboxValue($packageinfo['pk_enablecgi_in']) == 1){
			$line .= " CHECKED";
		}
		$line .= "/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Domains:</th>";
        $line .= "<td><input name=\"inNoDomains\" type=\"text\" id=\"inNoDomains\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_domains_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Sub-domains:</th>";
        $line .= "<td><input name=\"inNoSubDomains\" type=\"text\" id=\"inNoSubDomains\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_subdomains_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Parked domains:</th>";
        $line .= "<td><input name=\"inNoParkedDomains\" type=\"text\" id=\"inNoParkedDomains\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_parkeddomains_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Mailboxes:</th>";
        $line .= "<td><input name=\"inNoMailboxes\" type=\"text\" id=\"inNoMailboxes\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_mailboxes_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Forwarders:</th>";
        $line .= "<td><input name=\"inNoFowarders\" type=\"text\" id=\"inNoFowarders\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_fowarders_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. Dist Lists:</th>";
        $line .= "<td><input name=\"inNoDistLists\" type=\"text\" id=\"inNoDistLists\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_distlists_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. FTP accounts:</th>";
        $line .= "<td><input name=\"inNoFTPAccounts\" type=\"text\" id=\"inNoFTPAccounts\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_ftpaccounts_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>No. MySQL databases:</th>";
        $line .= "<td><input name=\"inNoMySQL\" type=\"text\" id=\"inNoMySQL\" size=\"5\" maxlength=\"3\" value=\"".$quotainfo['qt_mysql_in']."\"/></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Disk space quota:</th>";
        $line .= "<td><input name=\"inDiskQuota\" type=\"text\" id=\"inDiskQuota\" size=\"10\" maxlength=\"10\" value=\"".$quotainfo['qt_diskspace_bi'] / 1024000 ."\"/> MB (1000MB = 1GB)</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>Monthly bandwidth quota:</th>";
        $line .= "<td><input name=\"inBandQuota\" type=\"text\" id=\"inBandQuota\" size=\"10\" maxlength=\"10\" value=\"".$quotainfo['qt_bandwidth_bi'] / 1024000 ."\"/> MB (1000MB = 1GB)</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th><input type=\"hidden\" name=\"inPackageID\" value=\"".self::$package_to_edit."\" /></th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSubmit\" value=\"Save\">Save</button></td>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		return $line;
		
    }
	
	
	
	static function doCreatePackage(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$acc_fk = $currentuser['userid'];
		$packagename = $controller->GetControllerRequest('FORM', 'inPackageName');
		$packagename = str_replace(' ', '', $packagename);
   		# Check to make sure the packagename is not blank or exists for reseller before we go any further...
   		if (!fs_director::CheckForEmptyValue($packagename)) {
			$sql = "SELECT COUNT(*) FROM x_packages WHERE UPPER(pk_name_vc)='" . strtoupper($packagename) . "' AND pk_reseller_fk=" . $acc_fk . " AND pk_deleted_ts IS NULL";
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
	# If the user submitted a 'new' request then we will simply add the package to the database...
    $sql = $zdbh->prepare("INSERT INTO x_packages (pk_reseller_fk,
									pk_name_vc,
									pk_enablephp_in,
									pk_enablecgi_in,
									pk_created_ts) VALUES (
									" . $acc_fk . ",
									'" . $packagename . "',
									" . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'inEnablePHP')) . ",
									" . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'inEnableCGI')) . ",
									" . time() . ");");
    $sql->execute();
    # Now lets pull back the package ID so we can use it in the other tables we are about to manipulate.
	$package = $zdbh->query("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $acc_fk . " ORDER BY pk_id_pk DESC")->Fetch();
    $sql = $zdbh->prepare("INSERT INTO x_quotas (qt_package_fk,
									qt_domains_in,
									qt_subdomains_in,
									qt_parkeddomains_in,
									qt_mailboxes_in,
									qt_fowarders_in,
									qt_distlists_in,
									qt_ftpaccounts_in,
									qt_mysql_in,
									qt_diskspace_bi,
									qt_bandwidth_bi) VALUES (
									" . $package['pk_id_pk'] . ",
									" . $controller->GetControllerRequest('FORM', 'inNoDomains') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoSubDomains') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoParkedDomains') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoMailboxes') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoFowarders') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoDistLists') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoFTPAccounts') . ",
									" . $controller->GetControllerRequest('FORM', 'inNoMySQL') . ",
									" . ($controller->GetControllerRequest('FORM', 'inDiskQuota') * 1024000) . ",
									" . ($controller->GetControllerRequest('FORM', 'inBandQuota') * 1024000) . ")");
	$sql->execute();
	/* TODO Sort out new permissions and BW_MOD
    $sql = $zdbh->prepare("INSERT INTO x_permissions (pr_package_fk) VALUES (" . $packageid['pk_id_pk'] . ");");
	$sql->execute();
	# Insert default mod_bw quota limits for package
    $throttledefaults = = $zdbh->query("SELECT * FROM x_throttle WHERE tr_id_pk=1")->Fetch();
	$sql = "UPDATE x_quotas SET qt_bwenabled_in = '".$throttledefaults['tr_bwenabled_in']."',
								qt_dlenabled_in = '".$throttledefaults['tr_dlenabled_in']."',
								qt_totalbw_fk   = '".$throttledefaults['tr_totalbw_fk']."',
								qt_minbw_fk     = '".$throttledefaults['tr_minbw_fk']."',
								qt_maxcon_fk    = '".$throttledefaults['tr_maxcon_fk']."',
								qt_filesize_fk  = '".$throttledefaults['tr_filespeed_fk']."',
								qt_filespeed_fk = '".$throttledefaults['tr_filespeed_fk']."',
								qt_filetype_vc  = '".$throttledefaults['tr_filetype_vc']."',
								qt_modified_in  = '1'
								WHERE qt_package_fk  = '".$packageid['pk_id_pk']."'";
	*/						  
	self::$ok = 1;	
	}




	static function doEditPackage(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();		
		$sql = "SELECT COUNT(*) FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
				if ($numrows->fetchColumn() <> 0) {
					$sql = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $currentuser['userid'] . " AND pk_deleted_ts IS NULL");
					$sql->execute();
					while ($rowpackages = $sql->fetch()) {
						#Check if we are deleting a package
						if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$rowpackages['pk_id_pk'].''))){
							self::DeletePackage($rowpackages['pk_id_pk']);
						}
						#Check if we are editing a package
						if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inEdit_'.$rowpackages['pk_id_pk'].''))){
							self::$edit=1;
							self::$package_to_edit=$rowpackages['pk_id_pk'];
						}			
					}			
				}
			}
	}

	static function doSavePackage(){
		global $zdbh;
        global $controller;
		$sql = $zdbh->prepare("UPDATE x_packages SET pk_name_vc='" . $controller->GetControllerRequest('FORM', 'inPackageName') . "',
								pk_enablephp_in =" . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'inEnablePHP')) . ",
								pk_enablecgi_in =" . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'inEnableCGI')) . " 
								WHERE pk_id_pk  =" . $controller->GetControllerRequest('FORM', 'inPackageID') . "");
		$sql->execute();
		$sql = $zdbh->prepare("UPDATE x_quotas SET qt_domains_in = " . $controller->GetControllerRequest('FORM', 'inNoDomains') . ", 
								qt_parkeddomains_in =" . $controller->GetControllerRequest('FORM', 'inNoParkedDomains') . ",
								qt_ftpaccounts_in   =" . $controller->GetControllerRequest('FORM', 'inNoFTPAccounts') . ",
								qt_subdomains_in    =" . $controller->GetControllerRequest('FORM', 'inNoSubDomains') . ",
								qt_mailboxes_in     =" . $controller->GetControllerRequest('FORM', 'inNoMailboxes') . ",
								qt_fowarders_in     =" . $controller->GetControllerRequest('FORM', 'inNoFowarders') . ",
								qt_distlists_in     =" . $controller->GetControllerRequest('FORM', 'inNoDistLists') . ",
								qt_diskspace_bi     =" . ($controller->GetControllerRequest('FORM', 'inDiskQuota') * 1024000) . ",
								qt_bandwidth_bi     =" . ($controller->GetControllerRequest('FORM', 'inBandQuota') * 1024000) . " ,
								qt_mysql_in         =" . $controller->GetControllerRequest('FORM', 'inNoMySQL') . "
								WHERE qt_package_fk =" . $controller->GetControllerRequest('FORM', 'inPackageID') . "");						
		$sql->execute();
		self::$ok = 1;
	}
	
	static function DeletePackage($pk_id_pk){
		global $zdbh;
		$sql = $zdbh->prepare("UPDATE x_packages SET pk_deleted_ts = '" . time() . "' WHERE pk_id_pk = '".$pk_id_pk."'");
		$sql->execute();
		self::$ok = 1;
	}
	
	
	
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout(ui_language::translate("You need to specify a package name to create your package."));
		}
		if (!fs_director::CheckForEmptyValue(self::$badname)){
		return ui_sysmessage::shout(ui_language::translate("Your package name is not valid. Please enter a valid package name."));
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout(ui_language::translate("A package with that name already appears to exsist."));
		}	
		if (!fs_director::CheckForEmptyValue(self::$error)){
		return ui_sysmessage::shout(ui_language::translate("There was an error updating your package"));
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("Changes to your packages have been saved successfully!"));
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
