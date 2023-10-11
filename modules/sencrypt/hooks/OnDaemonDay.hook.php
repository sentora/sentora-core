<?php
/**
	* Controller for sencrypt module for sentora version 2.0.0
	* Version : 3.0.0
	* Author : TGates
	* Additional work by Diablo925, Jettaman
 */
 
// Lescript automatic updating script.
//
// This is an example of how Lescript can be used to automatically update
// expiring certificates.
//
// This code is based on FreePBX's LetsEncrypt integration
//
// Copyright (c) 2016 Rob Thomas <rthomas@sangoma.com>
// Licence:  AGPLv3.
//
// In addition, Stanislav Humplik <sh@analogic.cz> is explicitly granted permission
// to relicence this code under the open source licence of their choice.
 

# for LEscript you can use any logger according to Psr\Log\LoggerInterface
class Logger {
	function __call($name, $arguments) {
		echo date('Y-m-d H:i:s')." [$name] ${arguments[0]}\n";
	}
}
$logger = new Logger();

echo fs_filehandler::NewLine() . "START Sencrypt Manager SSL Renewal Hook." . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('Sencrypt SSL')) {
	
    echo "Sencrypt Manager module ENABLED..." . fs_filehandler::NewLine();
	
	if ( ctrl_options::GetSystemOption('panel_ssl_tx') != null) {
		
		echo fs_filehandler::NewLine() . "RENEWING Control Panel certificates..." . fs_filehandler::NewLine();
			# Run renew panel cert function
			renewPanelCertificates();
			
		echo fs_filehandler::NewLine()."RENEWING Control Panel certificates completed." . fs_filehandler::NewLine();
	}
	
	echo fs_filehandler::NewLine() . "RENEWING client certificates..." . fs_filehandler::NewLine();
		# Run renew cert function
		renewCertificates();
		
	echo "RENEWING client certificates completed." . fs_filehandler::NewLine();
	
	# Restart Apache service
	RestartHttpdServicesForSSL();

} else {
	
    echo "Sencrypt Manager module DISABLED...nothing to do." . fs_filehandler::NewLine();
}

echo "END Sencrypt Manager SSL Renewal Hook." . fs_filehandler::NewLine();

# Start functions here
function renewCertificates() {
	global $zdbh, $controller;
	$logger = new Logger();

	$rowvhost = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_active_in = '1' AND vh_ssl_tx IS NOT NULL AND vh_ssl_port_in IS NOT NULL AND vh_enabled_in = '1' AND vh_deleted_ts IS NULL");
	$rowvhost->execute();
	$sslVhosts = $rowvhost->fetchAll();
	$result = "";
	
	foreach($sslVhosts as $sslVhost) {
		//if (strpos($sslVhost['vh_ssl_tx'], 'Sencrypt') !== false) {
		if ($sslVhost['vh_ssl_tx'] !== false) {
			
			$vhostOwner = ctrl_users::GetUserDetail($sslVhost['vh_acc_fk']);
			$domainPath = ctrl_options::GetSystemOption('hosted_dir') . $vhostOwner['username'] . "/public_html" . $sslVhost['vh_directory_vc'];
			echo "Checking certificate for Client: " . $vhostOwner['username'] . " / Domain: " . $sslVhost['vh_name_vc'] . fs_filehandler::NewLine();
			//echo "At location: " . $domainPath . fs_filehandler::NewLine(); - For DEBUGGING
			
			// Configuration:
			$domains = $sslVhost['vh_name_vc'];
			$domains = array($domains);
			$domain = $sslVhost['vh_name_vc'];
			$webroot = $domainPath;
			
			$accountDir = ctrl_options::GetSystemOption('hosted_dir') . $vhostOwner['username'] . "/ssl/sencrypt/letsencrypt/";
			# Changed to help with backup and compability
			$certlocation = ctrl_options::GetSystemOption('hosted_dir') . $vhostOwner['username'] . "/ssl/sencrypt/letsencrypt/" . $sslVhost['vh_name_vc'] . "/";
			
			# Require Lescript for renewal of SSL certs
			require_once 'modules/sencrypt/code/Lescript.php';
			
			// Always use UTC
			date_default_timezone_set("UTC");
			
			// Do we need to create or upgrade our cert? Assume no to start with.
			$needsgen = false;
			
			# Set country/state - tg & Jettaman	
			$user_ip = ctrl_options::GetSystemOption('server_ip');
			$ip_response = file_get_contents('http://ip-api.com/json/'.$user_ip);
			$ip_array = json_decode($ip_response);
			$countryCode = $ip_array->countryCode; 
			$state = $ip_array->regionName;
			$ipStatus = $ip_array->status;
			$querydata = $ip_array->query;
			
			# Default if API failed
			//$countryCode = "US";
			//$state = "NM";
			//$ipStatus = "sucess";
			
			# Check if Domain is LIVE and Pointing to this server
			# Check DNS before continuing
			if (checkDNSIsLive($domain, $ipStatus, $querydata, $user_ip) == false) {
				//$dnsInvalid = true;
				//return false;
				echo "   DNS is not LIVE or POINTING to server. SKIPPING." . fs_filehandler::NewLine();
				
			} else {
				// Do we HAVE a certificate for all our domains?
				foreach ($domains as $d) {
					$certfile = "$certlocation/cert.pem";
					if (!file_exists($certfile)) {
						// We don't have a cert, so we need to request one.
						$needsgen = true;
					} else {
						// We DO have a certificate.
						$certdata = openssl_x509_parse(file_get_contents($certfile));
						echo "   Checking certificate for renewal: " . $d . "..." . fs_filehandler::NewLine();
						// If it expires in less than a month, we want to renew it.
						$renewafter = $certdata['validTo_time_t']-(86400*30);
						
						if (time() > $renewafter) {
							// Less than a month left, we need to renew.
							echo "   --- Renewing certificate : " . $d . " for ... 90 Days" . fs_filehandler::NewLine();
							$needsgen = true;
						}					
					}
				}
			}
			
			// Do we need to generate a certificate?
			if ($needsgen) {
				try {
					//$le = new Analogic\ACME\Lescript($accountDir, $certlocation, $webroot, $logger);
					# or without logger:
					$le = new Analogic\ACME\Lescript($accountDir, $certlocation, $webroot, $logger = NULL, $countryCode, $state);
					$le->initAccount();

					# Check if domain is a subdomain
					$sql = "SELECT vh_type_in FROM x_vhosts WHERE vh_acc_fk=:userid AND vh_name_vc=:domain AND vh_enabled_in = '1' AND vh_deleted_ts IS NULL";
					$query = $zdbh->prepare($sql);
					$query->bindParam(':userid', $sslVhost['vh_acc_fk']);
					$query->bindParam(':domain', $domain);
					$query->execute();
				
					# Get domain type
					$domainType = $query->fetchColumn();
											
					if ($domainType == 2 ) {
						// Create domain without www. becuase its a subdomain
						$le->signDomains(array($domain));
					} else {
						// Create a SSL with www. because its a root domain
						$le->signDomains(array($domain, 'www.'.$domain));
					}

				}
				catch (\Exception $e) {
					echo "ERROR!";
					$logger->error($e->getMessage());
					$logger->error($e->getTraceAsString());
					$errorCatahed = $e->getMessage();
					# Throw error and log to file
					error_log( date('Y-m-d H:i:s') . " - DOMAIN: "  . fs_filehandler::NewLine() . $errorCatahed . fs_filehandler::NewLine(), 3, ctrl_options::GetSystemOption('sentora_root') . 'modules/sencrypt/sencrypt.log');
					// Exit with an error code, something went wrong.
					exit(1);
				}
			}
			
			echo "Domain: " . $sslVhost['vh_name_vc'] . " analyzed." . fs_filehandler::NewLine() . fs_filehandler::NewLine();
		}
	}
	
}

function renewPanelCertificates() {
	global $zdbh, $controller;
	$logger = new Logger();
	
	$result = "";
	
		if ((ctrl_options::GetSystemOption('panel_ssl_tx') != NULL) && (ctrl_options::GetSystemOption('sentora_port' ) == 443 )) {
			
			# Other panel subomains to include - BETA DONT NOT ENABLE
			//$otherSubDomains = "ftp, webmail, mail, smtp, imap, pop";
			
			# Renew values
			$panelOwner = "zadmin";
			$domainPath = ctrl_options::GetSystemOption('sentora_root');
			echo "Checking certificate for Control Panel Domain: " . ctrl_options::GetSystemOption('sentora_domain') . fs_filehandler::NewLine();
			//echo "At location: " . $domainPath . fs_filehandler::NewLine();  - For DEBUGGING
			
			// Configuration:
			$domains = ctrl_options::GetSystemOption('sentora_domain');
			$domains = array($domains);
			$domain = ctrl_options::GetSystemOption('sentora_domain');
			$webroot = $domainPath;
			
			$accountDir = ctrl_options::GetSystemOption('hosted_dir') . $panelOwner . "/ssl/sencrypt/letsencrypt/";
			# Changed to help with backup and compability
			$certlocation = ctrl_options::GetSystemOption('hosted_dir') . $panelOwner . "/ssl/sencrypt/letsencrypt/" . $domain . "/";
			
			# Require Lescript for renewal of SSL certs
			require_once 'modules/sencrypt/code/Lescript.php';
			
			// Always use UTC
			date_default_timezone_set("UTC");
			
			// Do we need to create or upgrade our cert? Assume no to start with.
			$needsgen = false;
			
			# Set country/state - tg & Jettaman	
			$user_ip = ctrl_options::GetSystemOption('server_ip');
			$ip_response = file_get_contents('http://ip-api.com/json/'.$user_ip);
			$ip_array = json_decode($ip_response);
			$countryCode = $ip_array->countryCode; 
			$state = $ip_array->regionName;
			$ipStatus = $ip_array->status;
			$querydata = $ip_array->query;
			
			# Default if API failed
			//$countryCode = "US";
			//$state = "NM";
			//$ipStatus = "sucess";
			
			# Check if Domain is LIVE and Pointing to this server
			# Check DNS before continuing
			if (checkDNSIsLive($domain, $ipStatus, $querydata, $user_ip) == false) {
				//$dnsInvalid = true;
				//return false;
				echo "   DNS is not LIVE or POINTING to server. SKIPPING." . fs_filehandler::NewLine();
				
			} else {
				// Do we HAVE a certificate for all our domains?
				$certfile = "$certlocation/cert.pem";
				if (!file_exists($certfile)) {
					// We don't have a cert, so we need to request one.
					$needsgen = true;
				} else {
					// We DO have a certificate.
					$certdata = openssl_x509_parse(file_get_contents($certfile));
					echo "   Checking certificate for renewal: " . $domain . "..." . fs_filehandler::NewLine();
					// If it expires in less than a month, we want to renew it.
					$renewafter = $certdata['validTo_time_t']-(86400*30);
					
					if (time() > $renewafter) {
						// Less than a month left, we need to renew.
						echo "   --- Renewing certificate : " . $domain . " for ... 90 Days" . fs_filehandler::NewLine();
						$needsgen = true;
					} 				
				}
			}
			
			// Do we need to generate a certificate?
			if ($needsgen) {
				try {
					//$le = new Analogic\ACME\Lescript($accountDir, $certlocation, $webroot, $logger);
					# or without logger:
					$le = new Analogic\ACME\Lescript($accountDir, $certlocation, $webroot, $logger = NULL, $countryCode, $state);
					$le->initAccount();
									
					# Create panel SSL if no other sub domains are listed
					//if ($otherSubDomain == NULL ) {
						
						// Create panel domain cart
						$le->signDomains(array($domain));
						
					/*	
					} else {
						
						# Create a SSL for panel domain and other subdomains - BETA code DO NOT ENABLE
						
						# Checks if domain is sub or root by checking how many dots(.) there are. Returns dot(.) count
						if (substr_count($domain, '.') == 2 ) {
							# If panel domain is a sub domain
							$removeNeedle = ".";
							$pos = strpos($domain, $removeNeedle);
							$rootDomain = substr($domain, $pos . ".");
							
						} else {
							# If panel domain is a root domain
							$rootDomain = "." . $domain;
						
						}
						
						# If other panel subdomains are listed include them in SSL cert
						if ($otherSubDomains) {
						 
							$str_arr = preg_split ("/\,/", $otherSubDomains);
							
							# Set subdomain array
							$subList = array("");
							
							# Push each subdomain domain into an array for SSL
							foreach ($str_arr as $value) {
								array_push($subList, ', ' . $value.$rootDomain);
							}
							
							# Put list into string format for letsencrypt
							$subFinalList = implode($subList);
							
						}
						
						# Sign panel domain plus other subdomain
						$le->signDomains(array($domain . $subFinalList));
						
					}
					*/
					
				}
				catch (\Exception $e) {
					echo "ERROR!";
					$logger->error($e->getMessage());
					$logger->error($e->getTraceAsString());
					$errorCatahed = $e->getMessage();
					# Throw error and log to file
					error_log( date('Y-m-d H:i:s') . " - PANEL DOMAIN: "  . fs_filehandler::NewLine() . $errorCatahed . fs_filehandler::NewLine(), 3, ctrl_options::GetSystemOption('sentora_root') . 'modules/sencrypt/sencrypt.log');
					// Exit with an error code, something went wrong. Disable because it stops Daemon from completing.
					exit(1);
				}
			}

			echo "Control Panel Domain: " . $domain . " analyzed." . fs_filehandler::NewLine();
		}
		
}

function RestartHttpdServicesForSSL() {
	   
    global $zdbh;
   
	# Restart Apache after SSL renewal	
	echo "Finished Renewing Sencrypt SSL's... Now reloading Apache..." . fs_filehandler::NewLine();

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

function checkDNSIsLive ($domain, $ipStatus, $querydata, $server_ip) {
	 
	# Check DNS for domain is live and public before continuing
	# Pull IP from Sentora DB
	ctrl_options::GetSystemOption('server_ip');
	
	# Check Ip's match
	if(checkdnsrr($domain,"A")) {
		if (($ipStatus == "success") && ($querydata == $server_ip)) {
			return true;		
		}
	} else {
		return false;
	}
}

?>