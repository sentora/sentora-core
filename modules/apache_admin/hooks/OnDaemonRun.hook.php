<?php

# Start smarty template engine
require '/etc/sentora/panel/etc/lib/smarty/libs/Smarty.class.php';
	
#** un-comment the following line to show the debug console
//$smarty->debugging = false;

echo fs_filehandler::NewLine() . "START Apache Config Hook." . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('Apache Config')) {
    echo "Apache Admin module ENABLED..." . fs_filehandler::NewLine();
    TriggerApacheQuotaUsage();
    if (ctrl_options::GetSystemOption('apache_changed') == strtolower("true")) {
        echo "Apache Config has changed..." . fs_filehandler::NewLine();
		
        echo "Begin writing Apache Config to: " . ctrl_options::GetSystemOption('apache_vhost') . fs_filehandler::NewLine();
        WriteVhostConfigFile();
		
		# If Apache vhost file passes configuration test, run Apache vhost file Backup. Helping To prevent backing up a currupt vhost.conf
		if ( ctrl_options::GetSystemOption('apache_changed') != strtolower("true") ) {
			
			if (ctrl_options::GetSystemOption('apache_backup') == strtolower("true")) {
				echo "Backing up Apache Config to: " . ctrl_options::GetSystemOption('apache_budir') . fs_filehandler::NewLine();
				BackupVhostConfigFile();
			}
		}

    } else {
        echo "Apache Config has NOT changed...nothing to do." . fs_filehandler::NewLine();
    }
} else {
    echo "Apache Admin module DISABLED...nothing to do." . fs_filehandler::NewLine();
}
echo "END Apache Config Hook." . fs_filehandler::NewLine();

/**
 *
 * @param string $vhostName
 * @param numeric $customPort
 * @param string $userEmail[5~ * @return string
 *
 */
function BuildVhostPortForward($vhostName, $customPort, $userEmail) {
		
	$customPort_in = $customPort;
	
    $line = "# DOMAIN: " . $vhostName . fs_filehandler::NewLine();
    $line .= "# PORT FORWARD FROM ".ctrl_options::GetSystemOption('apache_port')." TO: " . $customPort_in . fs_filehandler::NewLine();
    $line .= "<Virtualhost *:".$apache_port.">" . fs_filehandler::NewLine();
    $line .= "ServerName " . $vhostName . fs_filehandler::NewLine();
	if ($vhostName != ctrl_options::GetSystemOption('sentora_domain') ) 
		$line .= "ServerAlias www." . $vhostName . fs_filehandler::NewLine();
    $line .= "ServerAdmin " . $userEmail . fs_filehandler::NewLine();
    $line .= "RewriteEngine on" . fs_filehandler::NewLine();
    $line .= "ReWriteCond %{SERVER_PORT} !^" . $customPort_in . "$" . fs_filehandler::NewLine();
    $line .= ( $customPort_in === "443" ) ? "RewriteRule ^/(.*) https://%{HTTP_HOST}/$1 [NC,R,L] " . fs_filehandler::NewLine() : "RewriteRule ^/(.*) http://%{HTTP_HOST}:" . $customPort . "/$1 [NC,R,L] " . fs_filehandler::NewLine();
    $line .= "</virtualhost>" . fs_filehandler::NewLine();
    //$line .= "# END DOMAIN: " . $vhostName . fs_filehandler::NewLine() . fs_filehandler::NewLine();
	//$line .= "################################################################" . fs_filehandler::NewLine();
	$line .= "##-------" . fs_filehandler::NewLine();
	$line .= fs_filehandler::NewLine();
		
    return $line;
}

# vhost SSL ReWrite http to https -tg
function BuildVhostReWriteSSL($vhostName, $userEmail) {
		
    $line = "# DOMAIN: " . $vhostName . fs_filehandler::NewLine();
    $line .= "# SSL REDIRECT" . fs_filehandler::NewLine();
    $line .= "<Virtualhost *:".ctrl_options::GetSystemOption('apache_port').">" . fs_filehandler::NewLine();
    $line .= "ServerName " . $vhostName . fs_filehandler::NewLine();
	if ($vhostName != ctrl_options::GetSystemOption('sentora_domain') ) 
		$line .= "ServerAlias www." . $vhostName . fs_filehandler::NewLine();
    $line .= "ServerAdmin " . $userEmail . fs_filehandler::NewLine();
    $line .= "RewriteEngine On" . fs_filehandler::NewLine();
	$line .= "RewriteCond %{HTTPS} !=on" . fs_filehandler::NewLine();
	$line .= "RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R,L]" . fs_filehandler::NewLine();
    $line .= "</virtualhost>" . fs_filehandler::NewLine();
    //$line .= "# END DOMAIN: " . $vhostName . fs_filehandler::NewLine() . fs_filehandler::NewLine();
	//$line .= "################################################################" . fs_filehandler::NewLine();
	$line .= fs_filehandler::NewLine();
	$line .= "##-------" . fs_filehandler::NewLine();
	$line .= fs_filehandler::NewLine();
		
    return $line;
}

function WriteVhostConfigFile() {
    global $zdbh;
	
	# Start Smarty Session
	$smarty = new Smarty;
	$smarty->setTemplateDir('/etc/sentora/configs/apache/templates/');
	$smarty->setCompileDir('/etc/sentora/panel/etc/lib/smarty/templates_c/');
	
	if ((double) sys_versions::ShowApacheVersion() < 2.4) {
        $apgrant = "0";
    } else {
        $apgrant = "1";
    }
	
    # Get email for server admin of Sentora
    $getserveremail = $zdbh->query("SELECT ac_email_vc FROM x_accounts where ac_id_pk=1")->fetch();
    $serveremail = ( $getserveremail['ac_email_vc'] != "" ) ? $getserveremail['ac_email_vc'] : "postmaster@" . ctrl_options::GetSystemOption('sentora_domain');

    $VHostDefaultPort = ctrl_options::GetSystemOption('apache_port');
    $customPorts = array(ctrl_options::GetSystemOption('sentora_port'));
	
    $portQuery = $zdbh->prepare("SELECT vh_custom_port_in FROM x_vhosts WHERE vh_deleted_ts IS NULL");
    $portQuery->execute();
		while ($rowport = $portQuery->fetch()) {
			$customPorts[] = (empty($rowport['vh_custom_port_in'])) ? $VHostDefaultPort : $rowport['vh_custom_port_in'];
			
			# Add vh_ssl_port_in ports to list array 
			$portQuery2 = $zdbh->prepare("SELECT vh_ssl_port_in FROM x_vhosts WHERE vh_deleted_ts IS NULL");
			$portQuery2->execute();
			while ($rowport2 = $portQuery2->fetch()) {
				$customPorts[] = (empty($rowport2['vh_ssl_port_in'])) ? $VHostDefaultPort : $rowport2['vh_ssl_port_in'];
			}	
		}
	
    $customPortList = array_unique($customPorts);
					
    /*
     * ###########################################################################?###################################
     * #
     * # Default Virtual Host Container
     * #
     * ###########################################################################?###################################
     */

    $line = "################################################################" . fs_filehandler::NewLine();
    $line .= "# Apache VHOST configuration file" . fs_filehandler::NewLine();
    $line .= "# Automatically generated by Sentora " . sys_versions::ShowSentoraVersion() . fs_filehandler::NewLine();
    $line .= "# Generated on: " . date(ctrl_options::GetSystemOption('sentora_df'), time()) . fs_filehandler::NewLine();
    $line .= "#==== YOU MUST NOT EDIT THIS FILE : IT WILL BE OVERWRITTEN ====" . fs_filehandler::NewLine();
    $line .= "# Use Sentora Menu -> Admin -> Module Admin -> Apache config" . fs_filehandler::NewLine();
    $line .= "################################################################" . fs_filehandler::NewLine();
    $line .= fs_filehandler::NewLine();

    # Listen is mandatory for each port <> 80 (80 is defined in system config)
	//$line .= "Listen " . ctrl_options::GetSystemOption('apache_port');
	# For each custom port
    foreach ($customPortList as $port) {
        $line .= "Listen " . $port . fs_filehandler::NewLine();
    }
	
	$line .= fs_filehandler::NewLine();

	$line .= "# Configuration for Sentora control panel." . fs_filehandler::NewLine();
	
	if (ctrl_options::GetSystemOption('panel_ssl_tx') == null) {
		
		##
		## Sentora Control Panel default vhost entry
		##
		
		//$line .= "# Configuration for Sentora control panel." . fs_filehandler::NewLine();
		$line .= "<VirtualHost *:" . ctrl_options::GetSystemOption('sentora_port') . ">" . fs_filehandler::NewLine();
		$line .= "ServerAdmin " . $serveremail . fs_filehandler::NewLine();
		$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('sentora_root') . '"' . fs_filehandler::NewLine();
		$line .= "ServerName " . ctrl_options::GetSystemOption('sentora_domain') . fs_filehandler::NewLine();
		
		# Vhost PHP settings
		$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
		//$line .= "php_admin_value open_basedir " . '"' . ctrl_options::GetSystemOption('sentora_root') . ctrl_options::GetSystemOption('openbase_seperator') 
				//. '/etc/sentora/configs/' . ctrl_options::GetSystemOption('openbase_seperator')
				//. ctrl_options::GetSystemOption('temp_dir') . ctrl_options::GetSystemOption('openbase_seperator')  
				//. ctrl_options::GetSystemOption('hosted_dir') . '"' . fs_filehandler::NewLine();
				
		# Set Function Blacklist 
		if (ctrl_options::GetSystemOption('use_suhosin') == "true") {
				$line .= "php_admin_value sp.configuration_file " . '"/etc/sentora/configs/php/sp/sentora.rules"' . fs_filehandler::NewLine();
		}		
				
		$line .= "php_admin_value session.save_path " . '"/var/sentora/sessions"' . fs_filehandler::NewLine();
		
		$line .= 'ErrorLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-error.log" ' . fs_filehandler::NewLine();
		$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-access.log" ' . ctrl_options::GetSystemOption('access_log_format') . fs_filehandler::NewLine();
		$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-bandwidth.log" ' . ctrl_options::GetSystemOption('bandwidth_log_format') . fs_filehandler::NewLine();
		
		// Error documents:- Error pages are added automatically if they are found in the /etc/static/errorpages
		// directory and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
		$errorpages = ctrl_options::GetSystemOption('sentora_root') . "/etc/static/errorpages";
		if (is_dir($errorpages)) {
			if ($handle = opendir($errorpages)) {
				while (($file = readdir($handle)) !== false) {
					if ($file != "." && $file != "..") {
						$page = explode(".", $file);
						if (!fs_director::CheckForEmptyValue(CheckErrorDocument($page[0]))) {
							$line .= "ErrorDocument " . $page[0] . " /etc/static/errorpages/" . $page[0] . ".html" . fs_filehandler::NewLine();
						}
					}
				}
				closedir($handle);
			}
		}
		$line .= '<Directory "' . ctrl_options::GetSystemOption('sentora_root') . '">' . fs_filehandler::NewLine();
		$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
		$line .= "    AllowOverride All" . fs_filehandler::NewLine();
	
		if ((double) sys_versions::ShowApacheVersion() < 2.4) {
			$line .= "    Require all granted" . fs_filehandler::NewLine();
		} else {
			$line .= "    Require all granted" . fs_filehandler::NewLine();
		}
	
		$line .= "</Directory>" . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
		$line .= "# Custom settings (if any exist)" . fs_filehandler::NewLine();
	
		// Global custom Sentora entry
		$line .= ctrl_options::GetSystemOption('global_zpcustom') . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
	
		$line .= "</VirtualHost>" . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
		
		$line .= "# END PANEL DOMAIN: " . ctrl_options::GetSystemOption('sentora_domain') . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
	
		# Load template file into vhost cofig to save
		//$line .= $smarty->fetch("vhost_cp.tpl") . fs_filehandler::NewLine();
	
	# Forwrd Sentora Panel if SSL is in use
	# If vhost SSL_TX not null create spearate <virtualhost>
	# Build Vhost SSL section
    } elseif (ctrl_options::GetSystemOption('panel_ssl_tx') != null) {
		
		# Build HTTP to HTTPS Redirect
		$line .= BuildVhostReWriteSSL(ctrl_options::GetSystemOption('sentora_domain'), $serveremail);
		
		//$line .= "################################################################" . fs_filehandler::NewLine();
		//$line .= fs_filehandler::NewLine();
		//$line .= "# Configuration for Sentora control panel SSL." . fs_filehandler::NewLine();
		$line .= "# PANEL HAS SSL ENABLED" . fs_filehandler::NewLine();
		$line .= "<VirtualHost *:" . ctrl_options::GetSystemOption('sentora_port') . ">" . fs_filehandler::NewLine();
		$line .= "ServerAdmin " . $serveremail . fs_filehandler::NewLine();
		$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('sentora_root') . '"' . fs_filehandler::NewLine();
		$line .= "ServerName " . ctrl_options::GetSystemOption('sentora_domain') . fs_filehandler::NewLine();
		
		# Vhost PHP settings
		$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
		//$line .= "php_admin_value open_basedir " . '"' . ctrl_options::GetSystemOption('sentora_root') . ctrl_options::GetSystemOption('openbase_seperator') 
				//. '/etc/sentora/configs/' . ctrl_options::GetSystemOption('openbase_seperator')
				//. ctrl_options::GetSystemOption('temp_dir') . ctrl_options::GetSystemOption('openbase_seperator')  
				//. ctrl_options::GetSystemOption('hosted_dir') . '"' . fs_filehandler::NewLine();
				
		# Set Function Blacklist 
		if (ctrl_options::GetSystemOption('use_suhosin') == "true") {
				$line .= "php_admin_value sp.configuration_file " . '"/etc/sentora/configs/php/sp/sentora.rules"' . fs_filehandler::NewLine();
		}		
		
		$line .= "php_admin_value session.save_path " . '"/var/sentora/sessions"' . fs_filehandler::NewLine();
	
		$line .= 'ErrorLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-error.log" ' . fs_filehandler::NewLine();
		$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-access.log" ' . ctrl_options::GetSystemOption('access_log_format') . fs_filehandler::NewLine();
		$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . 'sentora-bandwidth.log" ' . ctrl_options::GetSystemOption('bandwidth_log_format') . fs_filehandler::NewLine();
	
		// Error documents:- Error pages are added automatically if they are found in the /etc/static/errorpages
		// directory and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
		$errorpages = ctrl_options::GetSystemOption('sentora_root') . "/etc/static/errorpages";
		if (is_dir($errorpages)) {
			if ($handle = opendir($errorpages)) {
				while (($file = readdir($handle)) !== false) {
					if ($file != "." && $file != "..") {
						$page = explode(".", $file);
						if (!fs_director::CheckForEmptyValue(CheckErrorDocument($page[0]))) {
							$line .= "ErrorDocument " . $page[0] . " /etc/static/errorpages/" . $page[0] . ".html" . fs_filehandler::NewLine();
						}
					}
				}
				closedir($handle);
			}
		}
		$line .= '<Directory "' . ctrl_options::GetSystemOption('sentora_root') . '">' . fs_filehandler::NewLine();
		$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
		$line .= "    AllowOverride All" . fs_filehandler::NewLine();
	
		if ((double) sys_versions::ShowApacheVersion() < 2.4) {
			$line .= "    Require all granted" . fs_filehandler::NewLine();
		} else {
			$line .= "    Require all granted" . fs_filehandler::NewLine();
		}
		
		$line .= "</Directory>" . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();

		# SSL Settings
		$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
		$line .= ctrl_options::GetSystemOption('panel_ssl_tx') . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();

		$line .= "# Custom settings are loaded below this line (if any exist)" . fs_filehandler::NewLine();
		// Global custom Sentora entry
		$line .= ctrl_options::GetSystemOption('global_zpcustom') . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
		
		$line .= "</VirtualHost>" . fs_filehandler::NewLine();
		//$line .= fs_filehandler::NewLine();
		
		$line .= "# END PANEL DOMAIN: " . ctrl_options::GetSystemOption('sentora_domain') . fs_filehandler::NewLine();
		$line .= fs_filehandler::NewLine();
		
		############################## add back old code
		//$line .= $smarty->fetch("vhost_cp_ssl.tpl") . fs_filehandler::NewLine();
		
	}
	
	#*********Write to file
	//writetofile($server_panel . "sentora-cp.conf", $line);
	#***********
	
	//$line .= fs_filehandler::NewLine();
    $line .= "################################################################" . fs_filehandler::NewLine();
    $line .= "# Sentora generated VHOST configurations below....." . fs_filehandler::NewLine();
    $line .= "################################################################" . fs_filehandler::NewLine();
    $line .= fs_filehandler::NewLine();

    /*
     * ##############################################################################################################
     * #
     * # All Virtual Host Containers
     * #
     * ##############################################################################################################
     */
	
	#
    # Sentora virtual host container configuration
	#
	$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
    $sql->execute();
    while ($rowvhost = $sql->fetch()) {
	
	# Grab some variables we will use for later...
	$vhostuser = ctrl_users::GetUserDetail($rowvhost['vh_acc_fk']);
	$bandwidth = ctrl_users::GetQuotaUsages('bandwidth', $vhostuser['userid']);
	$diskspace = ctrl_users::GetQuotaUsages('diskspace', $vhostuser['userid']);
	# Set the vhosts to "LIVE"
	$vsql = $zdbh->prepare("UPDATE x_vhosts SET vh_active_in=1 WHERE vh_id_pk=:id");
	$vsql->bindParam(':id', $rowvhost['vh_id_pk']);
	$vsql->execute();
	
	# Add a default email if no email found for client.
	$useremail = ( fs_director::CheckForEmptyValue($vhostuser['email']) ) ? "postmaster@" . $rowvhost['vh_name_vc'] : $vhostuser['email'];
	
	# Check if domain or subdomain to see if we add an alias with 'www'
	$serveralias = ( $rowvhost['vh_type_in'] == 2 ) ? '' : " www." . $rowvhost['vh_name_vc'];
	
	# Check if site is ssl enabled to pevent duplicate Port 443
	//if ($rowvhost['vh_ssl_tx'] == !null) {
		//$vhostPort = $VHostDefaultPort;
	//} else {
		$vhostPort = ( fs_director::CheckForEmptyValue($rowvhost['vh_custom_port_in']) ) ? $VHostDefaultPort : $rowvhost['vh_custom_port_in'];
	//};
	
	$vhostIp = ( fs_director::CheckForEmptyValue($rowvhost['vh_custom_ip_vc']) ) ? "*" : $rowvhost['vh_custom_ip_vc'];
	
	# Get Package php and cgi enabled options
	$rows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_id_pk=:packageid AND pk_deleted_ts IS NULL");
	$rows->bindParam(':packageid', $vhostuser['packageid']);
	$rows->execute();
	$packageinfo = $rows->fetch();
	
	#*************************************************
		  
	$RootDir =  ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . '/public_html' . $rowvhost['vh_directory_vc'];  
	$vh_snuff_path = "/etc/sentora/configs/php/sp/";
	$vh_vhostuser = $vhostuser['username'];
	
	# Start Snuff Protection managemenet HERE. ------- DO NOT EDIT THIS CODE BELOW!!!!!
	# If Snuff for vhost is DISABLED continue here
	if (ctrl_options::GetSystemOption('use_suhosin') == "true") {
		if($rowvhost['vh_suhosin_in'] == 0) {
			# Snuff Default rules
			$func_blklist_sys = ($rowvhost['vh_suhosin_in'] <> 0) ? 'php_admin_value sp.configuration_file "' . $vh_snuff_path . 'disabled.rules"' : '';	
			
			# If not using custom rules. Delete any custom snuff rules if they exist. 
			if ( file_exists( $vh_snuff_path . $vh_vhostuser . "/" . $rowvhost['vh_name_vc'] . '.rules' ) ) {
				
				# Clear/Delete vhost snuff custom rules file
				unlink ( $vh_snuff_path . $vh_vhostuser . "/" . $rowvhost['vh_name_vc'] . '.rules' );
				//WriteDataToFile($vh_snuff_path . $vh_vhostuser . "/" . $rowvhost['vh_name_vc'] . '.rules', "");
			}
		} else {
			# If SNUFF protection is ENABLED continue here
			
			# Snuff Default rules
			$func_blklist_sys = ($rowvhost['vh_suhosin_in'] <> 0) ? 'php_admin_value sp.configuration_file "' . $vh_snuff_path . $vh_vhostuser . "/" . $rowvhost['vh_name_vc'] . '.rules"' : '';
		
			# Check sp user path exists if not make folder for sp vhost configs			
			if ( !is_dir( $vh_snuff_path . $vh_vhostuser ) ) {
				fs_director::CreateDirectory( $vh_snuff_path . $vh_vhostuser );
			}
		
			$linesp = "################################################################" . fs_filehandler::NewLine();
			$linesp .= "# Snuffleupagus configuration file for: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
			$linesp .= "# Automatically generated by Sentora " . sys_versions::ShowSentoraVersion() . fs_filehandler::NewLine();
			$linesp .= "# Generated on: " . date(ctrl_options::GetSystemOption('sentora_df'), time()) . fs_filehandler::NewLine();
			$linesp .= "#==== YOU MUST NOT EDIT THIS FILE : IT WILL BE OVERWRITTEN ====" . fs_filehandler::NewLine();
			$linesp .= "# Use Sentora Menu -> Admin -> Module Admin -> Apache config" . fs_filehandler::NewLine();
			$linesp .= "################################################################" . fs_filehandler::NewLine();
			$linesp .= fs_filehandler::NewLine();
			
			# If custom Snuff rules. Create vhost rule file here.	
			if($rowvhost['vh_custom_sp_tx'] != null) {
				$linesp .= $rowvhost['vh_custom_sp_tx'] . fs_filehandler::NewLine();
				$linesp .= fs_filehandler::NewLine();
			}
			
			# Add SP default rules
			$linesp .= $smarty->fetch("vhost_sp_rules.tpl") . fs_filehandler::NewLine();
			
			#*********Write to file
			WriteDataToFile($vh_snuff_path . $vh_vhostuser . "/" . $rowvhost['vh_name_vc'] . '.rules'  , $linesp);
			#***********
	
		}
	}
	# Stop Snuff Protection managemenet HERE. ------- DO NOT EDIT THIS CODE ABOVE!!!!!
	
	# Domain is enabled
	# Line1: Domain enabled & Client also is enabled.
	# Line2: Domain enabled & Client may be disabled, but 'Allow Disabled' = 'true' in apache settings.
	if ($rowvhost['vh_enabled_in'] == 1 && ctrl_users::CheckUserEnabled($rowvhost['vh_acc_fk']) ||
		$rowvhost['vh_enabled_in'] == 1 && ctrl_options::GetSystemOption('apache_allow_disabled') == strtolower("true")) {
	
		/*
		 * ##################################################
		 * #
		 * # Disk Quotas Check
		 * #
		 * ##################################################
		 */
	
		# Domain is beyond its diskusage
		if ($vhostuser['diskquota'] != 0 && $diskspace > $vhostuser['diskquota']) {
			
			if ($rowvhost['vh_ssl_tx'] == null) {
			
				# Load template file into vhost cofig to save
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED FOR DISK QUOTA OVERAGE" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'diskexceeded"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'diskexceeded">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
		
				############################## add back old code
				//$line .= $smarty->fetch("vhost_disk_quota.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				
				if ($rowvhost['vh_portforward_in'] <> 0) {
					$line .= fs_filehandler::NewLine();
					$line .= BuildVhostPortForward($rowvhost['vh_name_vc'], $vhostPort, $useremail);
				}
			
			# If vhost SSL_TX not null create spearate <virtualhost>
			} elseif ($rowvhost['vh_ssl_tx'] != null && $rowvhost['vh_ssl_port_in'] != null ) {
				
				# Build HTTP to HTTPS Redirect
				$line .= BuildVhostReWriteSSL($rowvhost['vh_name_vc'], $useremail);
				
				# Build Vhost SSL section
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED FOR DISK QUOTA OVERAGE & HAS SSL ENABLED" . fs_filehandler::NewLine();
				//$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $rowvhost['vh_ssl_port_in'] . ">" . fs_filehandler::NewLine();
				
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'diskexceeded"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'diskexceeded">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				
				# SSL Settings
				$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_ssl_tx'] . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
				
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_disk_quota_ssl.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
			}
			$line .= fs_filehandler::NewLine();
			
			
			#*********Write to file
			//writetofile($vh_path . $rowvhost['vh_name_vc']. ".conf"  , $line);
			#***********	
			
		/*
		 * ##################################################
		 * #
		 * # Bandwidth Quotas Check
		 * #
		 * ##################################################
		 */
	
		# Domain is beyond its quota
		} elseif ($vhostuser['bandwidthquota'] != 0 && $bandwidth > $vhostuser['bandwidthquota']) {
			
			if ($rowvhost['vh_ssl_tx'] == null) {
			
				# Load template file into vhost cofig to save
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED FOR BANDWIDTH OVERAGE" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'bandwidthexceeded"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'bandwidthexceeded">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_bandwidth.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				
				$line .= fs_filehandler::NewLine();
				if ($rowvhost['vh_portforward_in'] <> 0) {
					$line .= BuildVhostPortForward($rowvhost['vh_name_vc'], $vhostPort, $useremail);
				}
			
			# If vhost SSL_TX not null create spearate <virtualhost>
			} elseif ($rowvhost['vh_ssl_tx'] != null && $rowvhost['vh_ssl_port_in'] != null ) {
				
				# Build HTTP to HTTPS Redirect
				$line .= BuildVhostReWriteSSL($rowvhost['vh_name_vc'], $useremail);
				
				# Build Vhost SSL section
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED FOR BANDWIDTH OVERAGE & HAS SSL ENABLED" . fs_filehandler::NewLine();
				//$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $rowvhost['vh_ssl_port_in'] . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'bandwidthexceeded"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'bandwidthexceeded">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				
				# SSL Settings
				$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_ssl_tx'] . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
				
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_bandwidth_ssl.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
			}
			$line .= fs_filehandler::NewLine();
			
			# Write to file
			//writetofile($vh_path . $rowvhost['vh_name_vc']. ".conf"  , $line);
			#***********				
			
		/*
		 * ##################################################
		 * #
		 * # Parked Domain
		 * #
		 * ##################################################
		 */
	
		# Domain is a PARKED domain.
		} elseif ($rowvhost['vh_type_in'] == 3) {
			
			if ($rowvhost['vh_ssl_tx'] == null) {			
			
				# Load template file into vhost config to save
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN PARKED" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('parking_path') . '"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('parking_path') . '">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				// Global custom global vh entry
				$line .= "# Custom Global Settings (if any exist)" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('global_vhcustom') . fs_filehandler::NewLine();
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_parked.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				
				$line .= fs_filehandler::NewLine();
				if ($rowvhost['vh_portforward_in'] <> 0) {
					$line .= BuildVhostPortForward($rowvhost['vh_name_vc'], $vhostPort, $useremail);
				}
			
			# If vhost SSL_TX not null create spearate <virtualhost>
			} elseif ($rowvhost['vh_ssl_tx'] != null && $rowvhost['vh_ssl_port_in'] != null) {
				
				# Build HTTP to HTTPS Redirect
				$line .= BuildVhostReWriteSSL($rowvhost['vh_name_vc'], $useremail);
				
				# Build Vhost SSL section
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN IS PARKED & HAS SSL ENABLED" . fs_filehandler::NewLine();
				//$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $rowvhost['vh_ssl_port_in'] . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('parking_path') . '"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('parking_path') . '">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				
				# SSL Settings
				$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_ssl_tx'] . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
				
				// Global custom global vh entry
				$line .= "# Custom Global Settings (if any exist)" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('global_vhcustom') . fs_filehandler::NewLine();
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_parked_ssl.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
			}
			$line .= fs_filehandler::NewLine();
							
			# Write to file
			//writetofile($vh_path . $rowvhost['vh_name_vc']. ".conf"  , $line);
			#***********
			
		/*
		 * ##################################################
		 * #
		 * # Regular or Sub domain With PHP7/PHP-FPM MOD - PHP 7+
		 * #
		 * ##################################################
		 */
	
		#check
		#Domain is a regular domain or a subdomain with PHP MOD.
		} else {
		   
		    if ($rowvhost['vh_ssl_tx'] == null) {
		   
				# Create Apache Vhost direcory and log folders
				# Temp
				if ( !is_dir( ctrl_options::GetSystemOption('hosted_dir') . $vhostuser[ 'username' ] . "/tmp" ) ) {
					fs_director::CreateDirectory( ctrl_options::GetSystemOption( 'hosted_dir' ) . $vhostuser[ 'username' ] . "/tmp" );
				}
				# Logs
				if (!is_dir(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/")) {
					fs_director::CreateDirectory(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/");
				}
				###
				#START HERE
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				// Server name, alias, email settings
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				// Document root
				$line .= 'DocumentRoot ' . '"' . $RootDir . '"' . fs_filehandler::NewLine();
				// Get Package openbasedir and PHP handler enabled options
				if (ctrl_options::GetSystemOption('use_openbase') == "true") {
					if ($rowvhost['vh_obasedir_in'] <> 0) {
						$line .= 'php_admin_value open_basedir "' 
							  . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/public_html" 
							  . $rowvhost['vh_directory_vc'] . '/' . ctrl_options::GetSystemOption('openbase_seperator') 
							  . ctrl_options::GetSystemOption('openbase_temp') . '"' . fs_filehandler::NewLine();
					}
				}
				
				# Set Function Blacklist 
				if (ctrl_options::GetSystemOption('use_suhosin') == "true") {
					if ($rowvhost['vh_suhosin_in'] <> 0) {
						//$line .= ctrl_options::GetSystemOption('suhosin_value') . fs_filehandler::NewLine();
						$line .= $func_blklist_sys . fs_filehandler::NewLine();
					}
				}
				
				# PHP_admin_values
				$line .= 'php_admin_value upload_tmp_dir ' . '"' . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/tmp/" . "\"" . fs_filehandler::NewLine();
				$line .= 'php_admin_value session.save_path ' . '"' . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/tmp/" . "\"" . fs_filehandler::NewLine();
				// Logs
				if (!is_dir(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/")) {
					fs_director::CreateDirectory(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/");
				}
				$line .= 'ErrorLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-error.log" ' . fs_filehandler::NewLine();
				$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-access.log" ' . ctrl_options::GetSystemOption('access_log_format') . fs_filehandler::NewLine();
				$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-bandwidth.log" ' . ctrl_options::GetSystemOption('bandwidth_log_format') . fs_filehandler::NewLine();
		
				// Directory options
				$line .= '<Directory ' . $RootDir . '>' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				
				# Vhost PHP settings. For future use ( PHP Mod/FPM/Fcgi ) Coming soon!!!! :-)
				//if ($packageinfo['pk_enablephp_in'] <> 0) {
					# Test build PHP handler
					//$line .= BuildVhostPHPHandler($packageinfo['pk_enablephp_in'], $rowchost['vh_phphandler_id']);
				//}
		
				
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
		
				// Enable Gzip until we set this as an option , we might commenbt this too and allow manual switch
				$line .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript" . fs_filehandler::NewLine();
				
				// Get Package php and cgi enabled options
				$rows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_id_pk=:packageid AND pk_deleted_ts IS NULL");
				$rows->bindParam(':packageid', $vhostuser['packageid']);
				$rows->execute();
				$packageinfo = $rows->fetch();
				
				# curently disabled because un secure
				# need correct cleaning in interface for full removal or in comment here until restoration
				#                if ( $packageinfo[ 'pk_enablecgi_in' ] <> 0 ) {
				#                     $line .= ctrl_options::GetSystemOption( 'cgi_handler' ) . fs_filehandler::NewLine();
				#                     if ( !is_dir( ctrl_options::GetSystemOption( 'hosted_dir' ) . $vhostuser[ 'username' ] . "/public_html" . $rowvhost[ 'vh_directory_vc' ] . "/_cgi-bin" ) ) {
				#                         fs_director::CreateDirectory( ctrl_options::GetSystemOption( 'hosted_dir' ) . $vhostuser[ 'username' ] . "/public_html" . $rowvhost[ 'vh_directory_vc' ] . "/_cgi-bin" );
				#                     }
				#                 }
				
				// Error documents:- Error pages are added automatically if they are found in the _errorpages directory
				// and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
				$errorpages = ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/public_html" . $rowvhost['vh_directory_vc'] . "/_errorpages";
				if (is_dir($errorpages)) {
					if ($handle = opendir($errorpages)) {
						while (($file = readdir($handle)) !== false) {
							if ($file != "." && $file != "..") {
								$page = explode(".", $file);
								if (!fs_director::CheckForEmptyValue(CheckErrorDocument($page[0]))) {
									$line .= "ErrorDocument " . $page[0] . " /_errorpages/" . $page[0] . ".html" . fs_filehandler::NewLine();
								}
							}
						}
						closedir($handle);
					}
				}
		
				// Directory indexes
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
		
				// Global custom global vh entry
				$line .= "# Custom Global Settings (if any exist)" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('global_vhcustom') . fs_filehandler::NewLine();
		
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
		
				// End Virtual Host Settings
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				# Load template file into vhost config to save
				//$line .= $smarty->fetch("vhost_domain.tpl") . fs_filehandler::NewLine();
				
				# End Virtual Host Settings
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				
				
				if ($rowvhost['vh_portforward_in'] <> 0) {
					$line .= fs_filehandler::NewLine();
					$line .= BuildVhostPortForward($rowvhost['vh_name_vc'], $vhostPort, $useremail);
				}
							
			# If vhost SSL_TX not null create spearate <virtualhost>
			} elseif ($rowvhost['vh_ssl_tx'] != null && $rowvhost['vh_ssl_port_in'] != null) {
				
				# Build HTTP to HTTPS Redirect
				$line .= BuildVhostReWriteSSL($rowvhost['vh_name_vc'], $useremail);
				
				# Build Vhost SSL section
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS SSL ENABLED" . fs_filehandler::NewLine();
				
				#START HERE
				//$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $rowvhost['vh_ssl_port_in'] . ">" . fs_filehandler::NewLine();
		
				// Server name, alias, email settings
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				// Document root
		
				$line .= 'DocumentRoot ' . '"' . $RootDir . '"' . fs_filehandler::NewLine();
				// Get Package openbasedir and PHP handler enabled options
				if (ctrl_options::GetSystemOption('use_openbase') == "true") {
					if ($rowvhost['vh_obasedir_in'] <> 0) {
						$line .= 'php_admin_value open_basedir "' 
							  . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/public_html" 
							  . $rowvhost['vh_directory_vc'] . '/' . ctrl_options::GetSystemOption('openbase_seperator') 
							  . ctrl_options::GetSystemOption('openbase_temp') . '"' . fs_filehandler::NewLine();
					}
				}
				# PHP_admin_values
				$line .= 'php_admin_value upload_tmp_dir ' . '"' . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/tmp/" . "\"" . fs_filehandler::NewLine();
				$line .= 'php_admin_value session.save_path ' . '"' . ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/tmp/" . "\"" . fs_filehandler::NewLine();
				# Set Function Blacklist 
				if (ctrl_options::GetSystemOption('use_suhosin') == "true") {
					if ($rowvhost['vh_suhosin_in'] <> 0) {
						//$line .= ctrl_options::GetSystemOption('suhosin_value') . fs_filehandler::NewLine();
						$line .= $func_blklist_sys . fs_filehandler::NewLine();
					}
				}
				
				// Logs
				if (!is_dir(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/")) {
					fs_director::CreateDirectory(ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/");
				}
				$line .= 'ErrorLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-error.log" ' . fs_filehandler::NewLine();
				$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-access.log" ' . ctrl_options::GetSystemOption('access_log_format') . fs_filehandler::NewLine();
				$line .= 'CustomLog "' . ctrl_options::GetSystemOption('log_dir') . "domains/" . $vhostuser['username'] . "/" . $rowvhost['vh_name_vc'] . '-bandwidth.log" ' . ctrl_options::GetSystemOption('bandwidth_log_format') . fs_filehandler::NewLine();
		
				// Directory options
				$line .= '<Directory ' . $RootDir . '>' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
		
				# Vhost PHP settings. For future use ( PHP Mod/FPM/Fcgi ) Coming soon!!!! :-)
				//if ($packageinfo['pk_enablephp_in'] <> 0) {
					# Test build PHP handler
					//$line .= BuildVhostPHPHandler($packageinfo['pk_enablephp_in'], $rowchost['vh_phphandler_id']);
				//}
				
				$line .= ctrl_options::GetSystemOption('php_handler') . fs_filehandler::NewLine();
		
				// Enable Gzip until we set this as an option , we might commenbt this too and allow manual switch
				$line .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript" . fs_filehandler::NewLine();
				// Get Package php and cgi enabled options
				$rows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_id_pk=:packageid AND pk_deleted_ts IS NULL");
				$rows->bindParam(':packageid', $vhostuser['packageid']);
				$rows->execute();
				$packageinfo = $rows->fetch();
			
				# curently disabled because un secure
				# need correct cleaning in interface for full removal or in comment here until restoration
				#                if ( $packageinfo[ 'pk_enablecgi_in' ] <> 0 ) {
				#                     $line .= ctrl_options::GetSystemOption( 'cgi_handler' ) . fs_filehandler::NewLine();
				#                     if ( !is_dir( ctrl_options::GetSystemOption( 'hosted_dir' ) . $vhostuser[ 'username' ] . "/public_html" . $rowvhost[ 'vh_directory_vc' ] . "/_cgi-bin" ) ) {
				#                         fs_director::CreateDirectory( ctrl_options::GetSystemOption( 'hosted_dir' ) . $vhostuser[ 'username' ] . "/public_html" . $rowvhost[ 'vh_directory_vc' ] . "/_cgi-bin" );
				#                     }
				#                 }
		
				// Error documents:- Error pages are added automatically if they are found in the _errorpages directory
				// and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
				$errorpages = ctrl_options::GetSystemOption('hosted_dir') . $vhostuser['username'] . "/public_html" . $rowvhost['vh_directory_vc'] . "/_errorpages";
				if (is_dir($errorpages)) {
					if ($handle = opendir($errorpages)) {
						while (($file = readdir($handle)) !== false) {
							if ($file != "." && $file != "..") {
								$page = explode(".", $file);
								if (!fs_director::CheckForEmptyValue(CheckErrorDocument($page[0]))) {
									$line .= "ErrorDocument " . $page[0] . " /_errorpages/" . $page[0] . ".html" . fs_filehandler::NewLine();
								}
							}
						}
						closedir($handle);
					}
				}
		
				// Directory indexes
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
		
				# SSL Settings
				$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_ssl_tx'] . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
		
				// Global custom global vh entry
				$line .= "# Custom Global Settings (if any exist)" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('global_vhcustom') . fs_filehandler::NewLine();
		
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
		
				// End Virtual Host Settings
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_domain_ssl.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
			}
			$line .= fs_filehandler::NewLine();
			
			#*********Write to file
			//writetofile($vh_path . $rowvhost['vh_name_vc']. ".conf"  , $line);
			#***********
		}
	
		/*
		 * ##################################################
		 * #
		 * # Disabled domain
		 * #
		 * ##################################################
		 */
		} else {
		# Domain is NOT enabled
		
			if ($rowvhost['vh_ssl_tx'] == null) {
		
				# Load template file into vhost cofig to save
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'disabled"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'disabled">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_disabled.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
			
			# If vhost SSL_TX not null create spearate <virtualhost>
			} elseif ( $rowvhost['vh_ssl_tx'] != NULL && $rowvhost['vh_ssl_port_in'] != NULL ) {
				
				# Build HTTP to HTTPS Redirect
				$line .= BuildVhostReWriteSSL($rowvhost['vh_name_vc'], $useremail);
				
				# Build Vhost SSL section
				$line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "# THIS DOMAIN HAS BEEN DISABLED & HAS SSL ENABLED" . fs_filehandler::NewLine();
				//$line .= "<virtualhost " . $vhostIp . ":" . $vhostPort . ">" . fs_filehandler::NewLine();
				$line .= "<virtualhost " . $vhostIp . ":" . $rowvhost['vh_ssl_port_in'] . ">" . fs_filehandler::NewLine();
				
				$line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				//$line .= "ServerAlias www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				if (!empty($serveralias))
					$line .= "ServerAlias " . $serveralias . fs_filehandler::NewLine();
				$line .= "ServerAdmin " . $useremail . fs_filehandler::NewLine();
				$line .= 'DocumentRoot "' . ctrl_options::GetSystemOption('static_dir') . 'disabled"' . fs_filehandler::NewLine();
				$line .= '<Directory "' . ctrl_options::GetSystemOption('static_dir') . 'disabled">' . fs_filehandler::NewLine();
				$line .= "    Options +FollowSymLinks -Indexes" . fs_filehandler::NewLine();
				$line .= "    AllowOverride All" . fs_filehandler::NewLine();
				$line .= "    Require all granted" . fs_filehandler::NewLine();
				$line .= "</Directory>" . fs_filehandler::NewLine();
				$line .= ctrl_options::GetSystemOption('dir_index') . fs_filehandler::NewLine();
				
				# SSL Settings
				$line .= "# SSL settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_ssl_tx'] . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
				
				// Client custom vh entry
				$line .= "# Custom VH settings (if any exist)" . fs_filehandler::NewLine();
				$line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();
				$line .= "</virtualhost>" . fs_filehandler::NewLine();
				
				############################## add back old code
				//$line .= $smarty->fetch("vhost_disabled_ssl.tpl") . fs_filehandler::NewLine();
				
				$line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
				$line .= "################################################################" . fs_filehandler::NewLine();
				$line .= fs_filehandler::NewLine();
			}
		
		#*********Write to file
		//writetofile($vh_path . $rowvhost['vh_name_vc']. ".conf"  , $line);
		#***********
		}
//}
		
	}
	
    /*
     * ##############################################################################################################
     * #
     * # Write vhost file to disk
     * #
     * ##############################################################################################################
    
	 */
	/*
    # write the vhost config file
    $vhconfigfile = ctrl_options::GetSystemOption('apache_vhost');
    if (fs_filehandler::UpdateFile($vhconfigfile, 0777, $line)) {
        # Reset Apache settings to reflect that config file has been written, until the next change.
        $time = time();
        $vsql = $zdbh->prepare("UPDATE x_settings
                                    SET so_value_tx=:time
                                    WHERE so_name_vc='apache_changed'");
        $vsql->bindParam(':time', $time);
        $vsql->execute();
        echo "Finished writting Apache Config... Now reloading Apache..." . fs_filehandler::NewLine();

        $returnValue = 0;

        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
            system("" . ctrl_options::GetSystemOption('httpd_exe') . " " . ctrl_options::GetSystemOption('apache_restart') . "", $returnValue);
        } else {
            $command = ctrl_options::GetSystemOption('zsudo');
            $args = array(
                "service",
                ctrl_options::GetSystemOption('apache_sn'),
                ctrl_options::GetSystemOption('apache_restart')
            );
            $returnValue = ctrl_system::systemCommand($command, $args);
        }

        echo "Apache reload " . ((0 === $returnValue ) ? "suceeded" : "failed") . "." . fs_filehandler::NewLine();
    } else {
        return false;
    }
	*/
	
	# Check, Write config and Restart/Reload Webserver service if apache config passes check

	# Move current vhosts.conf file to backup incase Apache config test fails on new config
	$vhconfigfile = ctrl_options::GetSystemOption('apache_vhost');
	$BackupFileName = $vhconfigfile . "_bak_" . time();
	rename($vhconfigfile, $BackupFileName);
	
	# Write to new Apache vhosts.conf file
	WriteDataToFile($vhconfigfile, $line);
		
	# Check Apache vhosts file for errors
	echo "Checking Apache vhost config for errors..." . fs_filehandler::NewLine();
	# If Apache config check return 0 or False
	if ( CheckApacheVhostConfig() == FALSE ) {
		
		# Delete vhost backup file and Failed config if exists
		unlink($BackupFileName);
		
		# Delete httpd-vhost.conf_failed config file after apache config passes syntax check
		if (!is_file($vhconfigfile . "-failed")) {
			unlink($vhconfigfile . "-failed");
		}
			
		# Restart Apache service
		RestartHttpdServices();
				
	} else {
		
		echo "   Error: Restoring orginal vhost file. Check in Sentora Panel Apache vhost config settings or httpd-vhosts.conf file for errors and retry." . fs_filehandler::NewLine();
		
		# If config failed. Copy file to (httpd-vhosts.conf_failed)
		fs_filehandler::CopyFile($vhconfigfile, $vhconfigfile . "-failed");
		
		# Restore orginal apache vhosts config file if failed.
		fs_filehandler::CopyFile($BackupFileName, $vhconfigfile);
		unlink($BackupFileName);
		
	}
}

function RestartHttpdServices() {
	   
    global $zdbh;
   
	# Reset Apache settings to reflect that config file has been written, until the next change.
	$time = time();
	$vsql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx=:time
								WHERE so_name_vc='apache_changed'");
	$vsql->bindParam(':time', $time);
	$vsql->execute();
	echo "Finished writting Apache Config... Now reloading Apache..." . fs_filehandler::NewLine();

	$returnValue = 0;

	if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		system("" . ctrl_options::GetSystemOption('httpd_exe') . " " . ctrl_options::GetSystemOption('apache_restart') . "", $returnValue);
	} else {
		$command = ctrl_options::GetSystemOption('zsudo');
		$args = array(
			"service",
			ctrl_options::GetSystemOption('apache_sn'),
			ctrl_options::GetSystemOption('apache_restart')
		);
		$returnValue = ctrl_system::systemCommand($command, $args);
	}

	echo "Apache reload " . ((0 === $returnValue ) ? "suceeded" : "failed") . "." . fs_filehandler::NewLine();

}

function WriteDataToFile($panel, $line) {
	
	# Write the entire vhost string
	file_put_contents($panel , $line);
	
}

function CheckApacheVhostConfig() {
	
	# Check Apache vhost.conf for errors
	if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		system("httpd -t " , $ConfigReturnValue); # NEEDS MORE TESTING
		
	} else { # Linux systems
		$command = "apachectl";
		$args = "configtest";

		$ConfigReturnValue = ctrl_system::systemCommand($command, $args);
	}
	
	echo "   Apache vhost Config test " . (( 0 === $ConfigReturnValue ) ? "SUCEEDED" : "FAILED") . "." . fs_filehandler::NewLine();
	
	return $ConfigReturnValue;
}

function CheckErrorDocument($error) {
    $errordocs = array(100, 101, 102, 200, 201, 202, 203, 204, 205, 206, 207,
        300, 301, 302, 303, 304, 305, 306, 307, 400, 401, 402,
        403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413,
        414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424,
        425, 426, 500, 501, 502, 503, 504, 505, 506, 507, 508,
        509, 510);
    if (in_array($error, $errordocs)) {
        return true;
    } else {
        return false;
    }
}

function BackupVhostConfigFile() {
    echo "Apache VHost backups are enabled... Backing up current vhost.conf to: " . ctrl_options::GetSystemOption('apache_budir') . fs_filehandler::NewLine();
    if (!is_dir(ctrl_options::GetSystemOption('apache_budir'))) {
        fs_director::CreateDirectory(ctrl_options::GetSystemOption('apache_budir'));
    }
	
	# Vhost backup file name
	$CurrBackupVhostName = ctrl_options::GetSystemOption('apache_budir') . "VHOST_BACKUP_" . time();
	
	//copy(ctrl_options::GetSystemOption('apache_vhost'), ctrl_options::GetSystemOption('apache_budir') . "VHOST_BACKUP_" . time());
    copy(ctrl_options::GetSystemOption('apache_vhost'), $CurrBackupVhostName);
	
    fs_director::SetFileSystemPermissions(ctrl_options::GetSystemOption('apache_budir') . ctrl_options::GetSystemOption('apache_vhost') . ".BU", 0777);
	
    if (ctrl_options::GetSystemOption('apache_purgebu') == strtolower("true")) {
        echo "Apache VHost purges are enabled... Purging backups older than: " . ctrl_options::GetSystemOption('apache_purge_date') . " days..." . fs_filehandler::NewLine();
        echo "[FILE][PURGE_DATE][FILE_DATE][ACTION]" . fs_filehandler::NewLine();
        $purge_date = ctrl_options::GetSystemOption('apache_purge_date');
        if ($handle = @opendir(ctrl_options::GetSystemOption('apache_budir'))) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $filetime = @filemtime(ctrl_options::GetSystemOption('apache_budir') . $file);

                    if ($filetime == NULL) {
                        $filetime = @filemtime(utf8_decode(ctrl_options::GetSystemOption('apache_budir') . $file));
                    }
                    $filetime = floor((time() - $filetime) / 86400);
                    echo $file . " - " . $purge_date . " - " . $filetime . "";
                    if ($purge_date < $filetime) {
                        #delete the file
                        echo " - Deleting file..." . fs_filehandler::NewLine();
                        unlink(ctrl_options::GetSystemOption('apache_budir') . $file);
                    } else {
                        echo " - Skipping file..." . fs_filehandler::NewLine();
                    }
                }
            }
        }
        echo "Purging old backups complete..." . fs_filehandler::NewLine();
    }
    echo "Apache backups complete..." . fs_filehandler::NewLine();
}
	   	   
#
# Error fucntion for all sites - Smarty template
#
function is_errorpages($type, $vhUser, $vhDir) {
	global $zdbh;
	
	# Set error pages location
	if ($type == 'cp') {
		# Return CP error pages				
		$errorpages = ctrl_options::GetSystemOption('sentora_root') . "/etc/static/errorpages";
	} elseif ($type == 'vh') {
		# Return VH error pages
		$errorpages = ctrl_options::GetSystemOption('hosted_dir') . $vhUser . '/public_html' . $vhDir . '/_errorpages';
	}
	
	if (is_dir($errorpages)) {
		if ($handle = opendir($errorpages)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != "." && $file != "..") {
					$page = explode(".", $file);
					if (!fs_director::CheckForEmptyValue(CheckErrorDocument($page[0]))) {
						
						if ($type == 'cp') {				
							$loaderrorpages[] = "ErrorDocument " . $page[0] . " /etc/static/errorpages/" . $page[0] . ".html";
						} elseif ($type == 'vh') {
							$loaderrorpages[] = "ErrorDocument " . $page[0] . " /_errorpages/" . $page[0] . ".html";
						}
						
					}
				}
			}
			closedir($handle);
			return $loaderrorpages;				
		}
	}
}
	
function TriggerApacheQuotaUsage() {
    global $zdbh;
    global $controller;
    $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
    $sql->execute();
    while ($rowvhost = $sql->fetch()) {
        if ($rowvhost['vh_enabled_in'] == 1 && ctrl_users::CheckUserEnabled($rowvhost['vh_acc_fk']) ||
            $rowvhost['vh_enabled_in'] == 1 && ctrl_options::GetSystemOption('apache_allow_disabled') == strtolower("true")) {

            //$checksize = $zdbh->query("SELECT * FROM x_bandwidth WHERE bd_month_in = " . date("Ym") . " AND bd_acc_fk = " . $rowvhost['vh_acc_fk'] . "")->fetch();

            $date = date("Ym");
            $findsize = $zdbh->prepare("SELECT * FROM x_bandwidth WHERE bd_month_in = :date AND bd_acc_fk = :acc");
            $findsize->bindParam(':date', $date);
            $findsize->bindParam(':acc', $rowvhost['vh_acc_fk']);
            $findsize->execute();
            $checksize = $findsize->fetch();

            $currentuser = ctrl_users::GetUserDetail($rowvhost['vh_acc_fk']);
            if ($checksize['bd_diskover_in'] != $checksize['bd_diskcheck_in'] && $checksize['bd_diskover_in'] == 1) {
                echo "Disk usage over quota, triggering Apache..." . fs_filehandler::NewLine();
                $updateapache = $zdbh->prepare("UPDATE x_settings SET so_value_tx = 'true' WHERE so_name_vc ='apache_changed'");
                $updateapache->execute();

                //$updateapache = $zdbh->query("UPDATE x_bandwidth SET bd_diskcheck_in = 1 WHERE bd_acc_fk =" . $rowvhost['vh_acc_fk'] . "");
                $updateapache2 = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskcheck_in = 1 WHERE bd_acc_fk = :acc");
                $updateapache2->bindParam(':acc', $rowvhost['vh_acc_fk']);
                $updateapache2->execute();
            }
            if ($checksize['bd_diskover_in'] != $checksize['bd_diskcheck_in'] && $checksize['bd_diskover_in'] == 0) {
                echo "Disk usage under quota, triggering Apache..." . fs_filehandler::NewLine();
                $updateapache = $zdbh->prepare("UPDATE x_settings SET so_value_tx = 'true' WHERE so_name_vc ='apache_changed'");
                $updateapache->execute();

                //$updateapache = $zdbh->query("UPDATE x_bandwidth SET bd_diskcheck_in = 0 WHERE bd_acc_fk =" . $rowvhost['vh_acc_fk'] . "");
                $updateapache2 = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskcheck_in = 0 WHERE bd_acc_fk = :acc");
                $updateapache2->bindParam(':acc', $rowvhost['vh_acc_fk']);
                $updateapache2->execute();
            }
            if ($checksize['bd_transover_in'] != $checksize['bd_transcheck_in'] && $checksize['bd_transover_in'] == 1) {
                echo "Bandwidth usage over quota, triggering Apache..." . fs_filehandler::NewLine();
                $updateapache = $zdbh->prepare("UPDATE x_settings SET so_value_tx = 'true' WHERE so_name_vc ='apache_changed'");
                $updateapache->execute();

                //$updateapache = $zdbh->query("UPDATE x_bandwidth SET bd_transcheck_in = 1 WHERE bd_acc_fk =" . $rowvhost['vh_acc_fk'] . "");
                $updateapache2 = $zdbh->prepare("UPDATE x_bandwidth SET bd_transcheck_in = 1 WHERE bd_acc_fk = :acc");
                $updateapache2->bindParam(':acc', $rowvhost['vh_acc_fk']);
                $updateapache2->execute();
            }
            if ($checksize['bd_transover_in'] != $checksize['bd_transcheck_in'] && $checksize['bd_transover_in'] == 0) {
                echo "Bandwidth usage under quota, triggering Apache..." . fs_filehandler::NewLine();
                $updateapache = $zdbh->prepare("UPDATE x_settings SET so_value_tx = 'true' WHERE so_name_vc ='apache_changed'");
                $updateapache->execute();

                //$updateapache = $zdbh->query("UPDATE x_bandwidth SET bd_transcheck_in = 0 WHERE bd_acc_fk =" . $rowvhost['vh_acc_fk'] . "");
                $updateapache2 = $zdbh->prepare("UPDATE x_bandwidth SET bd_transcheck_in = 0 WHERE bd_acc_fk = :acc");
                $updateapache2->bindParam(':acc', $rowvhost['vh_acc_fk']);
                $updateapache2->execute();
            }
        }
    }
}

?>
