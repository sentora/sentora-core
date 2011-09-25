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
	static $nosub;
	static $alreadyexists;
	static $badname;
	static $blank;
	static $ok;

	static function getDomains(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
			$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
			
			$line = "<h2>Current domains</h2>";
			$line .= "<form action=\"./?module=domains&action=DeleteDomain\" method=\"post\">";
    		$line .= "<table class=\"zgrid\">";
        	$line .= "<tr>";
	        $line .= "<th>Domain name</th>";
	        $line .= "<th>Home directory</th>";
	        $line .= "<th>Status</th>";
	        $line .= "<th></th>";
	        $line .= "</tr>";
			
					$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1");
		 			$sql->execute();
				
						while ($rowdomains = $sql->fetch()) {
				        	$line .= "<tr>";
					        $line .= "<td>".$rowdomains['vh_name_vc']."</td>";
					        $line .= "<td>/".$rowdomains['vh_directory_vc']."</td>";
					        $line .= "<td>";
				
					        if ($rowdomains['vh_active_in'] == 1) {
					            $line .= "<font color=\"green\">Live</font>";
					        } else {
					            $line .= "<font color=\"orange\">Pending</font> <a href=\"#\" title=\"Your domain will become active at the next scheduled update.  This can take up to one hour.\"><img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/help_small.png\"></a>";
					        }
							
					        $line .= "</td>";
					        $line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDelete_".$rowdomains['vh_id_pk']."\" value=\"1\">Delete</button></td>";
					        $line .= "</tr>";
						}
	
		$line .= "</table>";
		$line .= "<input type=\"hidden\" name=\"inAction\" value=\"delete\"/>";
		$line .= "</form>";
				} else {
		$line = ui_sysmessage::shout("You currently do not have any domains configured.");		
				}
			}
		
		return $line;	
	}

	static function getCreateDomainForm(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
		$line  = "<h2>Create a new domain</h2>";
		$line .= "<form action=\"./?module=domains&action=CreateDomain\" method=\"post\" name=\"CreateDomain\">";
   	 	$line .= "<table class=\"zform\">";
		$line .= "<tr>";
		$line .= "<th>Domain name:</th>";
	 	$line .= "<td><input name=\"inDomain\" type=\"text\" id=\"inDomain\" size=\"30\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Home directory:</th>";
		$line .= "<td><input name=\"inAutoHome\" type=\"checkbox\" id=\"inAutoHome\" value=\"1\" CHECKED />";
		$line .= "&nbsp;Create a new home directory</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>&nbsp;</th>";
		$line .= "<td>or use existing:";
		$line .= "<select name=\"inDestination\" id=\"inDestination\">";
		$line .= "<option value=\"\">/ (root)</option>";
                        
		$handle = @opendir(self::GetVHOption('hosted_dir') . $currentuser['username']);
        $chkdir = self::GetVHOption('hosted_dir') . $currentuser['username'] . "/";
        if (!$handle) {
        # Log an error as the folder cannot be opened...
        } else {
        	while ($file = readdir($handle)) {
            	if ($file != "." && $file != "..") {
                	if (is_dir($chkdir . $file)) {
                    	$line .= "<option value=\"" . $file . "\">/" . $file . "</option>\n";
                    }
                }
            }
         	closedir($handle);
         }
						                        
		$line .= "</select></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>";
		$line .= "</th>";
		$line .= "<td align=\"right\">";
		$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSubmit\" value=\"1\">Create</button>";
		$line .= "</td>";
		$line .= "</tr>";
	    $line .= "</table>";
		$line .= "</form>";	
		
		return $line;
			
	}
	
	static function doCreateDomain(){
	global $controller;
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inSubmit'))){
		self::AddVhost();
		}
	return;
	}
	
	static function doDeleteDomain(){
	global $controller;
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inAction'))){
		self::DeleteVhost();
		}
	return;
	}
	

	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout("Your Domain can not be empty. Please enter a valid Domain Name and try again.");
		}
		if (!fs_director::CheckForEmptyValue(self::$badname)){
		return ui_sysmessage::shout("Your Domain name is not valid. Please enter a valid Domain Name: i.e. \"domain.com\"");
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout("The domain already appears to exsist on this server.");
		}	
		if (!fs_director::CheckForEmptyValue(self::$nosub)){
		return ui_sysmessage::shout("You cannot add a Sub-Domain here. Please use the Subdomain manager to add Sub-Domains.");
		}
		if (!fs_director::CheckForEmptyValue(self::$error)){
		return ui_sysmessage::shout("Please remove 'www'. The 'www' will automatically work with all Domains / Subdomains.");
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout("Changes to your domain web hosting has been saved successfully.");
		}else{
			return ui_module::GetModuleDescription();
		}
        return;
    }
		
	
############### METHODS TO BE MOVED TO INDIVIDUAL CLASSES ###############
	
	public function AddVhost(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		# Declare the domain name as a string...
	    $domain = $controller->GetControllerRequest('FORM', 'inDomain');
    	$destination = $controller->GetControllerRequest('FORM', 'inDestination');
		$returnurl = $controller->GetControllerRequest('URL', 'module');
	  	# Check for spaces and remove if found...
	    $domain = str_replace(' ', '', $domain);
	    # Check to make sure the domain is not blank before we go any further...
	    if ($domain == '') {
			self::$blank=1;
			return;
	    }
	    # Check for invalid characters in the domain...
	    if (!self::IsValidDomainName($domain)) {
			self::$badname=1;
			return;
	    }
	    # Check to make sure the domain is in the correct format before we go any further...
	    $wwwclean = stristr($domain, 'www.');
	    if ($wwwclean == true) {
			self::$error=1;
			return;
	    }
	    # Check to see if the domain already exists in ZPanel somewhere and redirect if it does....
		$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_name_vc='" . $domain . "' AND vh_deleted_ts IS NULL AND vh_type_in='1'";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() > 0) {	
				self::$alreadyexists=1;
				return;
			}
		}	
    	# Check to make sure user not adding a subdomain and blocks stealing of subdomains....
		$SharedDomains = array(); //DELETE THIS
	    if (substr_count($domain, ".") > 1) {
	        $part = explode('.', $domain);
	        foreach ($part as $check) {
	            if (!in_array($check, $SharedDomains)) {
	                if (strlen($check) > 3) {
	                    $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_name_vc LIKE '%" . $check . "%' AND vh_type_in !=2 AND vh_deleted_ts IS NULL");
		 				$sql->execute();
	                    while ($rowcheckdomains = $sql->fetch()) {
	                        $subpart = explode('.', $rowcheckdomains['vh_name_vc']);
	                        foreach ($subpart as $subcheck) {
	                            if (strlen($subcheck) > 3) {
	                                if ($subcheck == $check) {
	                                    if (substr($domain, -7) == substr($rowcheckdomains['vh_name_vc'], -7)) {
											self::$nosub=1;
	                                    }
	                                }
	                            }
	                        }
	                    }			
	                }
	            }
	        }
	    }		
    	# Check to see if its a new home directory or use a current one...
	    if ($controller->GetControllerRequest('FORM', 'inAutoHome') == 1) {
	        $homedirectoy_to_use = "/" . str_replace(".", "_", $domain);
			$vhost_path = self::GetVHOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/";
	        # Create the new home directory... (If it doesnt already exist.)
	        fs_filehandler::CreateDirectory($vhost_path);
	    } else {
	        $homedirectoy_to_use = "/" . $destination;
			$vhost_path = self::GetVHOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/";
	    }		
	    # Only run if the Server platform is Windows.
	    if (sys_versions::ShowOSPlatformVersion() == "Windows"){
	        if (self::GetVHOption('disable_hostsen') == 'false') {
	            # Lets add the hostname to the HOSTS file so that the server can view the domain immediately...
	            @exec(ctrl_options::GetOption('root_drive') . "ZPanel/bin/zpanel/tools/setroute.exe " . $domain . "");
	            @exec(ctrl_options::GetOption('root_drive') . "ZPanel/bin/zpanel/tools/setroute.exe www." . $domain . "");
	        }
	    }		
	    # Work out what handlers to add and then lets do it...
	    $handlers = "";
		$packageinfo['pk_enablephp_in'] = 1; //NEED TO CHANGE THIS
	    if ($packageinfo['pk_enablephp_in'] == 1) {
	        $handlers .= self::GetVHOption('php_handler') . fs_filehandler::NewLine();
	    }
		$packageinfo['pk_enablecgi_in'] = 1; //NEED TO CHANGE THIS
	    if ($packageinfo['pk_enablecgi_in'] == 1) {
	        $handlers .= "ScriptAlias /cgi-bin/ \"".$homedirectoy_to_use."/_cgi-bin/\"" . fs_filehandler::NewLine();
			$handlers .= "<location /cgi-bin>" . fs_filehandler::NewLine();
			$handlers .= self::GetVHOption('cgi_handler') . fs_filehandler::NewLine();
			$handlers .= "Options ExecCGI -Indexes" . fs_filehandler::NewLine();
			$handlers .="</location>" . fs_filehandler::NewLine();
		
	        fs_filehandler::CreateDirectory($vhost_path . "/_cgi-bin/");
	    }
	    # Now we get all error pages and prepare them for the vhost container...
		fs_filehandler::CreateDirectory($vhost_path . "/_errorpages/");
		$errorpages = self::GetVHOption('static_dir')."/errorpages/";
		if (is_dir($errorpages)) {
	    	if ($handle = opendir($errorpages)) {
	        	while (($file = readdir($handle)) !== false) {
					if ($file != "." && $file != "..") {
	            		fs_filehandler::CopyFile($errorpages . $file, $vhost_path . '/_errorpages/' . $file);
						//WRITE CODE TO ADD ERROR PAGES TO VHOST FILE HERE
	        		}
	        	}
	        closedir($handle);
	    	}
		}
	    # Lets copy the default welcome page across...
	    if ((!file_exists($vhost_path . "/index.html")) && (!file_exists($vhost_path . "/index.php")) && (!file_exists($vhost_path . "/index.htm"))) {
	        fs_filehandler::CopyFileSafe(self::GetVHOption('static_dir') . "pages/welcome.html", $vhost_path . "/index.html");
	    }
		#PHP Flags
	    if (sys_versions::ShowOSPlatformVersion() <> "Windows"){
			$flag_seperator = ":";
		} else {
			$flag_seperator = ";";
		}
	    $flags  = "php_admin_value open_basedir \"" . $vhost_path . "";
		$flags .= $flag_seperator;
		$flags .= self::GetVHOption('temp_dir') . "\"" . fs_filehandler::NewLine();
		$flags .= "php_admin_value upload_tmp_dir \"" . self::GetVHOption('upload_temp_dir') . "\"" . fs_filehandler::NewLine();
		#Error logs
		$alogs  = "ErrorLog \""  . self::GetVHOption('logfile_dir') . $currentuser['username'] . "/" . $domain . "-error.log\"" . fs_filehandler::NewLine();
		$alogs .= "CustomLog \"" . self::GetVHOption('logfile_dir') . $currentuser['username'] . "/" . $domain . "-access.log\" common" . fs_filehandler::NewLine();
		$alogs .= "CustomLog \"" . self::GetVHOption('logfile_dir') . $currentuser['username'] . "/" . $domain . "-bandwidth.log\" common" . fs_filehandler::NewLine();
		#Server Admin
		if (!fs_director::CheckForEmptyValue($currentuser['email'])){
	        $serveradmin = $currentuser['email'] . fs_filehandler::NewLine();
	    } else {
	        $serveradmin = "webmaster@" . $domain . fs_filehandler::NewLine();
	    }
	    # If all has gone well we need to now create the domain in the database...
	    $sql = $zdbh->prepare("INSERT INTO x_vhosts (vh_acc_fk,
									vh_name_vc,
									vh_directory_vc,
									vh_type_in,
									vh_created_ts) VALUES (
									" . $currentuser['userid'] . ",
									'" . $domain . "',
									'" . $homedirectoy_to_use . "',
									1,
									" . time() . ")"); //CLEANER FUNCTION ON $domain and $homedirectoy_to_use
	    $sql->execute();
		fs_filehandler::ResetFile("/test/handlers.txt");
		fs_filehandler::CreateFile("/test/handlers.txt", "0777", $handlers . $flags . $alogs . $serveradmin); #DEBUG CHECK		
			self::$ok=1;
			return;
		
	}
	
	
	
	
	public function DeleteVhost(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		# User has choosen to delete the task...
			$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
					$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1");
		 			$sql->execute();
					while ($rowdomains = $sql->fetch()) {
		        		if ($controller->GetControllerRequest('FORM', 'inDelete_' . $rowdomains['vh_id_pk'])) {
		            		# Log the action in the database...
	
			            	# Call the API
			            	//zapi_vhost_remove(GetSystemOption('apache_vhost'), $rowdomains['vh_name_vc']);
	
				            # Remove the domain from the MySQL database now..
				            $updatesql = $zdbh->prepare("UPDATE x_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_id_pk=" . $rowdomains['vh_id_pk'] . "");
							$updatesql->execute();
				        }						
					}
				}
			}				
		self::$ok=1;
	}
	
	
	############### Script Functions ###############
	static function IsValidDomainName($a) {
    # DESCRIPTION: Check for invalid characters in domain creation.
	if (stristr($a, '.')){
    	$part = explode(".", $a);
    	foreach ($part as $check) {
        	if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $check) || preg_match('/-$/', $check)) {
            	return false;
        	}
    	}
	} else {
		return false;
	}
    return true;
	}

	static function IsValidEmail($email) {
    # DESCRIPTION: Check for invalid characters in email creation.
    if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email) ) {
    	return false;
    }
    return true;
	}

	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }


	static function getModuleIcon() {
		global $controller;
		$module_icon = "/etc/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
	

    static function GetVHOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT vhs_value_tx FROM x_vhosts_settings WHERE vhs_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['vhs_value_tx'];
        } else {
            return false;
        }
    }
	
		
}

?>
