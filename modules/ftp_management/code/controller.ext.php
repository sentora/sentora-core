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
	static $blank;
	static $ok;
	static $edit;
	static $ftpid;

	static function getFTPAccounts(){
		global $controller;
			$display = self::DisplayFTPAccounts();
		return $display;
	}

	static function getFTPAction(){
		global $controller;
		if (!fs_director::CheckForEmptyValue(self::$edit)){
			$display = self::DisplayEditFTP();
		} else {
			$display = self::DisplayNewFTP();
		}
		return $display;
	}

	static function DisplayFTPAccounts(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();

		$sql = "SELECT COUNT(*) FROM x_ftpaccounts WHERE ft_acc_fk=" . $currentuser['userid'] . " AND ft_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
							
				$sql = $zdbh->prepare("SELECT * FROM x_ftpaccounts WHERE ft_acc_fk=" . $currentuser['userid'] . " AND ft_deleted_ts IS NULL");
				$sql->execute();
				$line  = "<h2>".ui_language::translate("Current FTP accounts")."</h2>"; 
    			$line .= "<form action=\"./?module=ftp_management&action=EditFTP\" method=\"post\">";
        		$line .= "<table class=\"zgrid\">"; 
            	$line .= "<tr>"; 
                $line .= "<th>".ui_language::translate("Account name")."</th>"; 
                $line .= "<th>".ui_language::translate("Home directory")."</th>"; 
                $line .= "<th>".ui_language::translate("Permission")."</th>"; 
                $line .= "<th></th>"; 
            	$line .= "</tr>"; 
           	 	while ($rowftpaccounts = $sql->fetch()) {
                	$line .= "<tr>"; 
                    $line .= "<td>".$rowftpaccounts['ft_user_vc']."</td>"; 
                    $line .= "<td>".$rowftpaccounts['ft_directory_vc']."</td>"; 
                    $line .= "<td>".$rowftpaccounts['ft_access_vc']."</td>";
					$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inReset_".$rowftpaccounts['ft_id_pk'] . "\" id=\"button\" value=\"inReset_".$rowftpaccounts['ft_id_pk'] . "\">".ui_language::translate("Reset Password")."</button><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inDelete_".$rowftpaccounts['ft_id_pk'] . "\" id=\"button\" value=\"inDelete_".$rowftpaccounts['ft_id_pk'] . "\">".ui_language::translate("Delete")."</button></td>";
                	$line .= "</tr>"; 
            	}
        		$line .= "</table>"; 
    			$line .= "</form>";

				} else {
    			$line = "<h2>".ui_language::translate("You do not have any FTP Accounts setup.")."</h2>";
    			$line .= ui_language::translate("Create an FTP account using the form below.");
				}
				return $line;
			}

	}
	
	static function DisplayNewFTP(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "<table class=\"none\" width=\"100%\" cellborder=\"0\" cellspacing=\"0\"><tr valign=\"top\"><td>";
		$line .= "<h2>".ui_language::translate("Create a new FTP Account")."</h2>";
		$line .= "<form action=\"./?module=ftp_management&action=CreateFTP\" method=\"post\">";
       	$line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Username").":</th>";
        $line .= "<td><input name=\"inUsername\" type=\"text\" id=\"inUsername\" size=\"30\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Password").":</th>";
        $line .= "<td><input name=\"inPassword\" type=\"password\" id=\"inPassword\" size=\"30\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Access type").":</th>";
        $line .= "<td><select name=\"inAccess\" size=\"1\">";
        $line .= "<option value=\"RO\" selected=\"selected\">".ui_language::translate("Read-only")."</option>";
        $line .= "<option value=\"WO\">".ui_language::translate("Write-only")."</option>";
        $line .= "<option value=\"RW\">".ui_language::translate("Full access")."</option>";
        $line .= "</select></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Home directory").":</th>";
        $line .= "<td><input name=\"inAutoHome\" type=\"checkbox\" id=\"inAutoHome\" value=\"1\" checked=\"checked\" /> ".ui_language::translate("Create a new home directory")."</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>&nbsp;</th>";
        $line .= "<td>".ui_language::translate("or use existing").": ";
        $line .= "<select name=\"inDestination\" id=\"inDestination\">";
        $line .= "<option value=\"\">/ (".ui_language::translate("Default").")</option>";
                        /*
                        $handle = @opendir(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc']);
                        $chkdir = GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . "/";
                        if (!$handle) {
                            # Log an error as the folder cannot be opened...

                        } else {
                            while ($file = readdir($handle)) {
                                if ($file != "." && $file != "..") {
                                    if (is_dir($chkdir . $file)) {
                                        $line .= "<option value=\"" . $file . "\">/" . $file . "</option>";
                                    }
                                }
                            }
                            closedir($handle);
                        }
                       */
       	$line .= "</select></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th colspan=\"2\" align=\"right\">";
        $line .= "<input type=\"hidden\" name=\"inAction\" value=\"NewFTPAccount\" />";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inSubmit\" id=\"inSubmit\" value=\"\">".ui_language::translate("Create")."</button></th>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		$line .= "</td>";
		$line .= "<td align=\"right\">".self::DisplayFTPUsagepChart()."</td>";
		$line .= "</tr></table>";
		
		return $line;
	}
	
	static function DisplayEditFTP(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		$rowftp = $zdbh->query("SELECT * FROM x_ftpaccounts WHERE ft_id_pk=" . self::$ftpid . " AND ft_deleted_ts IS NULL")->Fetch();
		$line  = "<table class=\"none\" width=\"100%\" cellborder=\"0\" cellspacing=\"0\"><tr valign=\"top\"><td>";		
		$line .= "<h2>".ui_language::translate("Reset FTP Password")."</h2>";
		$line .= "<form action=\"./?module=ftp_management&action=ResetPassword\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Username").":</th>";
        $line .= "<td>".$rowftp['ft_user_vc']."</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("New password").":</th>";
        $line .= "<td><input name=\"inPassword\" type=\"password\" id=\"inPassword\" size=\"30\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th colspan=\"2\" align=\"right\">";
        $line .= "<input type=\"hidden\" name=\"inAccount\" value=\"".$rowftp['ft_user_vc']."\" />";
        //$line .= "<input type=\"hidden\" name=\"inAction\" value=\"reset\" />";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inReset_".self::$ftpid . "\" id=\"button\" value=\"inReset_".self::$ftpid . "\">".ui_language::translate("Reset Password")."</button><button class=\"fg-button ui-state-default ui-corner-all\" name=\"inReset_CANCEL\" value=\"CANCEL\" onclick=\"window.location.href='".self::getModulePath()."'\">".ui_language::translate("Cancel")."</button></th>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		$line .= "</td>";
		$line .= "<td align=\"right\">".self::DisplayFTPUsagepChart()."</td>";
		$line .= "</tr></table>";
		
		return $line;	
	}

    static function DisplayFTPUsagepChart() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "";
		$ftpquota = $currentuser['ftpaccountsquota'];
		$ftp = fs_director::GetQuotaUsages('ftpaccounts', $currentuser['userid']);
		$total= $ftpquota;
		$used = $ftp;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";		
		return $line;
	}
	
	static function doCreateFTP(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
	
		if (fs_director::CheckForEmptyValue(self::CheckForErrors())){
			$username = $controller->GetControllerRequest('FORM', 'inUsername');
	    	$password = $controller->GetControllerRequest('FORM', 'inPassword');
		    $destination = $controller->GetControllerRequest('FORM', 'inDestination');
		    $access_type = $controller->GetControllerRequest('FORM', 'inAccess');
		
    		# Check to see if its a new home directory or use a current one...
	    	if ($controller->GetControllerRequest('FORM', 'inAutoHome') == 1) {
		        $homedirectoy_to_use = "/" . str_replace(".", "_", $username);
		        # Create the new home directory... (If it doesnt already exist.)		
		        if (!file_exists(ctrl_options::GetOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/")) {
		            @mkdir(ctrl_options::GetOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/", 777);
		            @chmod(ctrl_options::GetOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/", 0777);
		        }
				
		    } else {
		        $homedirectoy_to_use = "/" . $destination;
		    }
			
			$sql = $zdbh->prepare("INSERT INTO x_ftpaccounts (ft_acc_fk,
											ft_user_vc,
											ft_directory_vc,
											ft_access_vc,
											ft_created_ts) VALUES (
										" . $currentuser['userid'] . ",
										'" . $username . "',
										'" . $homedirectoy_to_use . "',
										'" . $access_type . "',
										" . time() . ")");
			$sql->execute();
			self::$ok = TRUE;
			return;
		}
		self::$error = TRUE;
		return;
	}
	
	static function doEditFTP(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_ftpaccounts WHERE ft_acc_fk=" . $currentuser['userid'] . " AND ft_deleted_ts IS NULL");
		$sql->execute();
		while ($rowftp = $sql->fetch()) {
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inReset_' . $rowftp['ft_id_pk'] . ''))){
				self::$edit=1;
				self::$ftpid = $rowftp['ft_id_pk'];
				return;
			}
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $rowftp['ft_id_pk'] . ''))){
				self::DeleteFTP($rowftp['ft_id_pk']);
				return;
			}
		}		

	}
	
	static function doResetPassword(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inReset_CANCEL'))){
    		$sql = "SELECT * FROM x_ftpaccounts WHERE ft_user_vc='" . $controller->GetControllerRequest('FORM', 'inAccount') . "' AND ft_acc_fk=" . $currentuser['userid'] . " AND ft_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
	 			if ($numrows->fetchColumn() <> 0) {
				
				self::$ok=TRUE;
				}
			}
		}	
	}
	
	static function DeleteFTP($ft_id_pk){
		global $zdbh;
		$sql = $zdbh->prepare("UPDATE x_ftpaccounts SET ft_deleted_ts=" . time() . " WHERE ft_id_pk=" . $ft_id_pk . "");
		$sql->execute();
		self::$ok=TRUE;
	}
	
	static function CheckForErrors() {
		global $zdbh;
		global $controller;
		$retval = FALSE;
		$currentuser = ctrl_users::GetUserDetail();
    	# Check to make sure the username and password is not blank before we go any further...
    	if ($controller->GetControllerRequest('FORM', 'inUsername') == '' || $controller->GetControllerRequest('FORM', 'inPassword') == '') {
			self::$blank = TRUE;
			$retval = TRUE;
    	}
	    # Check to make sure the cron is not a duplicate...
			$sql = "SELECT COUNT(*) FROM x_ftpaccounts WHERE ft_user_vc='" . $controller->GetControllerRequest('FORM', 'inUsername') . "' AND ft_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {	
					self::$alreadyexists = TRUE;
					$retval = TRUE;
				}
			}
		return $retval;
   	}
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout(ui_language::translate("You must enter a valid username and password to create your FTP account."), "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
			return ui_sysmessage::shout(ui_language::translate("An FTP account with that name already exists."), "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$error)){
			return ui_sysmessage::shout(ui_language::translate("There was an error updating your FTP accounts."), "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("FTP accounts updated successfully."), "zannounceok");
		}else{
			return ui_language::translate(ui_module::GetModuleDescription());
		}
        return;
    }

	static function getModuleName() {
		$module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

	static function getModulePath() {
		global $controller;
		$module_path = "?module=" . $controller->GetControllerRequest('URL', 'module') . "";
        return $module_path;
    }
	
}

?>