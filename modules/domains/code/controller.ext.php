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
	static $writeerror;
	static $nosub;
	static $alreadyexists;
	static $badname;
	static $blank;
	static $ok;

	static function getCurrentDomains(){
		$display = self::DisplayCurrentDomains();
		return $display;
	}
	
	static function getCreateDomain(){
		$display = self::DisplayCreateDomain();
		return $display;
	}
	
	static function DisplayCurrentDomains(){
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
	        $line .= "<th></th>";
	        $line .= "</tr>";
			
					$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1");
		 			$sql->execute();
				
						while ($rowdomains = $sql->fetch()) {
				        	$line .= "<tr>";
					        $line .= "<td>".$rowdomains['vh_name_vc']."</td>";
					        $line .= "<td>".$rowdomains['vh_directory_vc']."</td>";
					        $line .= "<td>";
				
					        if ($rowdomains['vh_active_in'] == 1) {
					            $line .= "<font color=\"green\">Live</font></td><td>";
					        } else {
					            $line .= "<font color=\"orange\">Pending</font></td><td><a href=\"#\" title=\"Your domain will become active at the next scheduled update.  This can take up to one hour.\"><img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/help_small.png\"></a>";
					        }
							
					        $line .= "</td>";
					        $line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDelete_".$rowdomains['vh_id_pk']."\" id=\"inDelete_".$rowdomains['vh_id_pk']."\" value=\"inDelete_".$rowdomains['vh_id_pk']."\">Delete</button></td>";
					        $line .= "</tr>";
						}
	
		$line .= "</table>";
		$line .= "</form>";
				} else {
		$line = "<h2>Current domains</h2>";
		$line .= "You currently do not have any domains configured. Create a domain using the form below.";		
				}
			}
		
		return $line;	
	}

	static function DisplayCreateDomain(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
		$line  = "<table class=\"none\" width=\"100%\" cellborder=\"0\" cellspacing=\"0\"><tr valign=\"top\"><td>";	
		$line .= "<h2>Create a new domain</h2>";
		$line .= "<form action=\"./?module=domains&action=CreateDomain\" method=\"post\" name=\"CreateDomain\">";
   	 	$line .= "<table class=\"zform\">";
		$line .= "<tr>";
		$line .= "<th>Domain name:</th>";
	 	$line .= "<td><input name=\"inDomain\" type=\"text\" id=\"inDomain\" size=\"30\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Home directory:</th>";
		$line .= "<td><input name=\"inAutoHome\" type=\"radio\" id=\"inAutoHome\" value=\"1\" onclick=\"hide_div('showdomainselect');\" CHECKED />";
		$line .= "&nbsp;Create a new home directory</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>&nbsp;</th>";
		$line .= "<td><input name=\"inAutoHome\" type=\"radio\" id=\"inAutoHome\" value=\"2\" onclick=\"show_div('showdomainselect');\" />";
		$line .= "&nbsp;Use existing home directory</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>&nbsp;</th>";
		$line .= "<td>";
		$line .= "<div id=\"showdomainselect\" style=\"display:none;\">";
		$line .= "<select name=\"inDestination\" id=\"inDestination\">";
		$line .= "<option value=\"\">/ (root)</option>";
                        
		$handle = @opendir(self::GetVHOption('hosted_dir') . $currentuser['username']);
        $chkdir = self::GetVHOption('hosted_dir') . $currentuser['username'] . "/";
        if (!$handle) {
        // Log an error as the folder cannot be opened...
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
						                        
		$line .= "</select></div></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>";
		$line .= "</th>";
		$line .= "<td align=\"right\">";
		$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"CreateDomain\" value=\"1\">Create</button>";
		$line .= "</td>";
		$line .= "</tr>";
	    $line .= "</table>";
		$line .= "</form>";
		$line .= "</td>";
		$line .= "<td align=\"right\">".self::DisplayDomainUsagepChart()."</td>";
		$line .= "</tr></table>";
		
		return $line;
			
	}
	
    static function DisplayDomainUsagepChart() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "";
		$domainsquota = $currentuser['domainquota'];
		$domains = fs_director::GetQuotaUsages('domains', $currentuser['userid']);
		$total= $domainsquota;
		$used = $domains;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";		
		return $line;
	}	
	
	static function doCreateDomain(){
	global $controller;
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'CreateDomain'))){
		self::AddVhost();
		}
	return;
	}
	
	static function doDeleteDomain(){
	global $zdbh;
	global $controller;
	$currentuser = ctrl_users::GetUserDetail();
	$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
		if ($numrows = $zdbh->query($sql)->fetchColumn() <> 0) {
			$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1");
	 		$sql->execute();
			while ($rowdomains = $sql->fetch()) {
	        	if ($controller->GetControllerRequest('FORM', 'inDelete_' . $rowdomains['vh_id_pk'])) {
					if (!fs_director::CheckForEmptyValue(self::DeleteVhost($rowdomains['vh_id_pk']))){
					self::$ok=TRUE;
					return;
					}
				}
			}				
		}
	
	return;
	}		
	

	
	public function AddVhost(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		// Declare the domain name as a string...
	    $domain = $controller->GetControllerRequest('FORM', 'inDomain');
    	$destination = $controller->GetControllerRequest('FORM', 'inDestination');
		$returnurl = $controller->GetControllerRequest('URL', 'module');
	  	// Check for spaces and remove if found...
	    $domain = str_replace(' ', '', $domain);
		if (!fs_director::CheckForEmptyValue(self::CheckCreateForErrors())){	  	
    		// Check to see if its a new home directory or use a current one...
		    if ($controller->GetControllerRequest('FORM', 'inAutoHome') == 1) {
		        $homedirectoy_to_use = "/public_html/" . str_replace(".", "_", $domain);
				$vhost_path = self::GetVHOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/";
		        // Create the new home directory... (If it doesnt already exist.)
		        fs_filehandler::CreateDirectory($vhost_path);
		    } else {
		        $homedirectoy_to_use = "/public_html/" . $destination;
				$vhost_path = self::GetVHOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "/";
		    }		
		    // Only run if the Server platform is Windows.
		    if (sys_versions::ShowOSPlatformVersion() == "Windows"){
		        if (self::GetVHOption('disable_hostsen') == 'false') {
		            // Lets add the hostname to the HOSTS file so that the server can view the domain immediately...
		            @exec(ctrl_options::GetOption('root_drive') . "ZPanel/bin/zpanel/tools/setroute.exe " . $domain . "");
		            @exec(ctrl_options::GetOption('root_drive') . "ZPanel/bin/zpanel/tools/setroute.exe www." . $domain . "");
		        }
		    }
		
			// Error documents:- Error pages are added automatically if they are found in the _errorpages directory
			// and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
			fs_filehandler::CreateDirectory($vhost_path . "/_errorpages/");
			$errorpages = self::GetVHOption('static_dir')."/errorpages/";
			if (is_dir($errorpages)) {
		    	if ($handle = opendir($errorpages)) {
		        	while (($file = readdir($handle)) !== false) {
						if ($file != "." && $file != "..") {
							$page = explode( ".", $file);
							if (!fs_director::CheckForEmptyValue(self::CheckErrorDocument($page[0]))){
		            		fs_filehandler::CopyFile($errorpages . $file, $vhost_path . '/_errorpages/' . $file);
							}
		        		}
		        	}
		        closedir($handle);
		    	}
			}
		    // Lets copy the default welcome page across...
		    if ((!file_exists($vhost_path . "/index.html")) && (!file_exists($vhost_path . "/index.php")) && (!file_exists($vhost_path . "/index.htm"))) {
		        fs_filehandler::CopyFileSafe(self::GetVHOption('static_dir') . "pages/welcome.html", $vhost_path . "/index.html");
		    }
				
	    	// If all has gone well we need to now create the domain in the database...
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
			
			// Write the vhost file
			if (!fs_director::CheckForEmptyValue(self::WriteVhostConfigFile())){
				self::$ok=TRUE;
				return;
			} else {
				self::$writeerror=TRUE;
				return;
			}
			
		}		
	}	
	
	public function DeleteVhost($vh_id_pk){
		global $zdbh;
		global $controller;
		$retval = FALSE;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("UPDATE x_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_id_pk=" . $vh_id_pk . "");
		$sql->execute();		
		$retval = TRUE;
		return $retval;
	}
	
	static function WriteVhostConfigFile(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();

		$line  = "################################################################" . fs_filehandler::NewLine();
		$line .= "# Apache VHOST configuration file                               " . fs_filehandler::NewLine();
		$line .= "# Automatically generated by ZPanel ".sys_versions::ShowZpanelVersion()."                           " . fs_filehandler::NewLine();
		$line .= "################################################################" . fs_filehandler::NewLine();
		$line .= "" . fs_filehandler::NewLine();
		
		// ZPanel default virtual host container
		$line .= "NameVirtualHost *:" . self::GetVHOption('apache_port') . "" . fs_filehandler::NewLine();
		$line .= "" . fs_filehandler::NewLine();
		$line .= "# Configuration for ZPanel control panel." . fs_filehandler::NewLine();
		$line .= "<VirtualHost localhost:" . self::GetVHOption('apache_port') . ">" . fs_filehandler::NewLine();
		$line .= "ServerAdmin zadmin@ztest.com" . fs_filehandler::NewLine();
		$line .= "DocumentRoot \"" . ctrl_options::GetOption('zpanel_root') . "\"" . fs_filehandler::NewLine();
		$line .= "ServerName " . ctrl_options::GetOption('zpanel_domain') . "" . fs_filehandler::NewLine();
		$line .= "ServerAlias *." . ctrl_options::GetOption('zpanel_domain') . "" . fs_filehandler::NewLine();
		$line .= "<Location /server-status>" . fs_filehandler::NewLine();
		$line .= "	SetHandler server-status" . fs_filehandler::NewLine();
		$line .= "	Order Deny,Allow" . fs_filehandler::NewLine();
		$line .= "	Allow from all" . fs_filehandler::NewLine();
		$line .= "</Location>" . fs_filehandler::NewLine();
		$line .= "AddType application/x-httpd-php .php" . fs_filehandler::NewLine();
		$line .= "<Directory \"" . ctrl_options::GetOption('zpanel_root') . "\">" . fs_filehandler::NewLine();
		$line .= "Options FollowSymLinks" . fs_filehandler::NewLine();
		$line .= "	AllowOverride All" . fs_filehandler::NewLine();
		$line .= "	Order allow,deny" . fs_filehandler::NewLine();
		$line .= "	Allow from all" . fs_filehandler::NewLine();
		$line .= "</Directory>" . fs_filehandler::NewLine();
		$line .= "" . fs_filehandler::NewLine();
		$line .= "# Custom settings are loaded below this line (if any exist)" . fs_filehandler::NewLine();
		
		// Global custom zpanel entry
		$line .= self::GetVHOption('global_zpcustom');
		
		$line .= "</VirtualHost>" . fs_filehandler::NewLine();		
		
		$line .= "" . fs_filehandler::NewLine();
		$line .= "################################################################" . fs_filehandler::NewLine();
		$line .= "# ZPanel generated VHOST configurations below.....      " . fs_filehandler::NewLine();
		$line .= "################################################################" . fs_filehandler::NewLine();
		$line .= "" . fs_filehandler::NewLine();
		
		// Zpanel virtual host container configuration
		$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
		$sql->execute();
		while ($rowvhost = $sql->fetch()) {
		
		// Get account username vhost is create with
		$username = $zdbh->query("SELECT ac_user_vc FROM x_accounts where ac_id_pk=" . $rowvhost['vh_acc_fk'] . "")->fetch();
		
		$line .= "# DOMAIN: ".$rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
		$line .= "<virtualhost *:" . self::GetVHOption('apache_port') . ">" . fs_filehandler::NewLine();
		
		// Bandwidth Settings
		//$line .= "Include C:/ZPanel/bin/apache/conf/mod_bw/mod_bw/mod_bw_Administration.conf" . fs_filehandler::NewLine();
		
		// Server name, alias, email settings
		$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
		$line .= "ServerAlias " . $rowvhost['vh_name_vc'] . " www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
		$line .= "ServerAdmin postmaster@txt-clan.com" . fs_filehandler::NewLine();
		
		// Document root
		$line .= "DocumentRoot \"" . self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . "\"" . fs_filehandler::NewLine();
		
		// Get Package openbasedir and suhosin enabled options
		if (self::GetVHOption('use_openbase') == "true"){
			if ($rowvhost['vh_obasedir_in'] <> 0){
				$line .= "php_admin_value open_basedir \"" . self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . self::GetVHOption('openbase_seperator') . self::GetVHOption('openbase_temp') . "\"" . fs_filehandler::NewLine();
			}
		}
		if (self::GetVHOption('use_suhosin') == "true"){
			if ($rowvhost['vh_suhosin_in'] <> 0){
				$line .= self::GetVHOption('suhosin_value') . fs_filehandler::NewLine();
			}
		}
		// Logs
		$line .= "ErrorLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-error.log\" " . fs_filehandler::NewLine();
		$line .= "CustomLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log\" " . self::GetVHOption('access_log_format') . fs_filehandler::NewLine();
		$line .= "CustomLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-bandwidth.log\" " . self::GetVHOption('bandwidth_log_format') . fs_filehandler::NewLine();
		
		// Directory options
		$line .= "<Directory />" . fs_filehandler::NewLine();
		$line .= "Options FollowSymLinks Indexes" . fs_filehandler::NewLine();
		$line .= "AllowOverride All" . fs_filehandler::NewLine();
		$line .= "Order Allow,Deny" . fs_filehandler::NewLine();
		$line .= "Allow from all" . fs_filehandler::NewLine();
		$line .= "</Directory>" . fs_filehandler::NewLine();
		
		// Get Package php and cgi enabled options
        $rows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $rowvhost['vh_acc_fk'] . " AND pk_deleted_ts IS NULL");
        $rows->execute();
        $dbvals = $rows->fetch();
		if ($dbvals['pk_enablephp_in'] <> 0){
			$line .= self::GetVHOption('php_handler') . fs_filehandler::NewLine();
		}
		if ($dbvals['pk_enablecgi_in'] <> 0){
			$line .= self::GetVHOption('cgi_handler') . fs_filehandler::NewLine();
		}
		
		// Error documents:- Error pages are added automatically if they are found in the _errorpages directory
		// and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
		$errorpages = self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . "/_errorpages";
		if (is_dir($errorpages)) {
	    	if ($handle = opendir($errorpages)) {
	        	while (($file = readdir($handle)) !== false) {
					if ($file != "." && $file != "..") {
						$page = explode( ".", $file);
						if (!fs_director::CheckForEmptyValue(self::CheckErrorDocument($page[0]))){
	            		$line .= "ErrorDocument ".$page[0]." /_errorpages/".$page[0].".html" . fs_filehandler::NewLine();
						}
	        		}
	        	}
	        closedir($handle);
	    	}
		}
		
		// Directory indexes
		$line .= self::GetVHOption('dir_index') . fs_filehandler::NewLine();
		
		// Global custom global vh entry
		$line .= "# Custom Global Settings" . fs_filehandler::NewLine();
		$line .= self::GetVHOption('global_vhcustom') . fs_filehandler::NewLine();
		
		// Client custom vh entry
		$line .= "# Custom VH settings" . fs_filehandler::NewLine();
		$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
		
		// End Virtual Host Settings
		$line .= "</virtualhost>" . fs_filehandler::NewLine();
		$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
		$line .= "################################################################" . fs_filehandler::NewLine();	
		}

		// write the FTP config file
		$vhconfigfile = self::GetVHOption('apache_vhost');
		if (fs_filehandler::UpdateFile($vhconfigfile, 0777, $line)){
			return TRUE;
		} else {
			return FALSE;
		}			
	}
	
	static function CheckCreateForErrors() {
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		// Declare the domain name as a string...
	    $domain = $controller->GetControllerRequest('FORM', 'inDomain');
    	$destination = $controller->GetControllerRequest('FORM', 'inDestination');
		$returnurl = $controller->GetControllerRequest('URL', 'module');
	  	// Check for spaces and remove if found...
	    $domain = str_replace(' ', '', $domain);
	    // Check to make sure the domain is not blank before we go any further...
	    if ($domain == '') {
			self::$blank=1;
			return FALSE;
	    }
	    // Check for invalid characters in the domain...
	    if (!self::IsValidDomainName($domain)) {
			self::$badname=1;
			return FALSE;
	    }
	    // Check to make sure the domain is in the correct format before we go any further...
	    $wwwclean = stristr($domain, 'www.');
	    if ($wwwclean == true) {
			self::$error=1;
			return FALSE;
	    }
	    // Check to see if the domain already exists in ZPanel somewhere and redirect if it does....
		$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_name_vc='" . $domain . "' AND vh_deleted_ts IS NULL AND vh_type_in='1'";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() > 0) {	
				self::$alreadyexists=TRUE;
				return FALSE;
			}
		}	
    	// Check to make sure user not adding a subdomain and blocks stealing of subdomains....
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
											self::$nosub=TRUE;
											return FALSE;
	                                    }
	                                }
	                            }
	                        }
	                    }			
	                }
	            }
	        }
	    }		
		return TRUE;
	}
	
	static function IsValidDomainName($a) {
    // DESCRIPTION: Check for invalid characters in domain creation.
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
    // DESCRIPTION: Check for invalid characters in email creation.
    if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email) ) {
    	return false;
    }
    return true;
	}
	
	static function CheckErrorDocument($error){
		$errordocs = array( 100,
							101,
							102,
							200,
							201,
							202,
							203,
							204,
							205,
							206,
							207,
							300,
							301,
							302,
							303,
							304,
							305,
							306,
							307,
							400,
							401,
							402,
							403,
							404,
							405,
							406,
							407,
							408,
							409,
							410,
							411,
							412,
							413,
							414,
							415,
							416,
							417,
							418,
							419,
							420,
							421,
							422,
							423,
							424,
							425,
							426,
							500,
							501,
							502,
							503,
							504,
							505,
							506,
							507,
							508,
							509,
							510);
		if (in_array($error, $errordocs)){
			return true;
		} else {
			return false;
		}
	}
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout("Your Domain can not be empty. Please enter a valid Domain Name and try again.", "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$badname)){
		return ui_sysmessage::shout("Your Domain name is not valid. Please enter a valid Domain Name: i.e. \"domain.com\"", "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout("The domain already appears to exsist on this server.", "zannounceerror");
		}	
		if (!fs_director::CheckForEmptyValue(self::$nosub)){
		return ui_sysmessage::shout("You cannot add a Sub-Domain here. Please use the Subdomain manager to add Sub-Domains.", "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$error)){
		return ui_sysmessage::shout("Please remove 'www'. The 'www' will automatically work with all Domains / Subdomains.", "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$writeerror)){
		return ui_sysmessage::shout("There was a problem writting to the virtual host container file. Please contact your administrator and report this error. Your domain will not function until this error is corrected.", "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout("Changes to your domain web hosting has been saved successfully.", "zannounceok");
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