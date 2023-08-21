<?php

/**
 *
 * ZPanel - AutoIP, written by RusTus: www.zpanelcp.com.
 *
 */

	echo fs_filehandler::NewLine() . "START AutoIP Update Hook." . fs_filehandler::NewLine();
		if (ui_module::CheckModuleEnabled('AutoIP Updater')){
			UpdateServerIPHook();
		} else {
			echo "AutoIP Module DISABLED." . fs_filehandler::NewLine();
		}
	echo "END AutoIP Update Hook." . fs_filehandler::NewLine();
	
	function UpdateServerIPHook() {
		global $zdbh;
		$oldip = $zdbh->query("SELECT * FROM x_settings WHERE so_name_vc='server_ip'")->Fetch();
		if ($oldip){
			$autoipsettings = $zdbh->query("SELECT * FROM x_autoip")->fetch();
        	if ($autoipsettings) {
				if ($autoipsettings['ai_enabled_in'] == 1){
					$detectedip = file_get_contents($autoipsettings['ai_command_vc']);
					$regexp = "^\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b^";
					$matchresult = preg_match_all($regexp, $detectedip, $matches);
					if(!empty($matchresult)){
						$detectedip = $matches[0][0];
					}
					echo "AutoIP ENABLED..." . fs_filehandler::NewLine();
					echo "Checking if Server IP has changed..." . fs_filehandler::NewLine();
					echo "Current  IP is: " . $oldip['so_value_tx'] . fs_filehandler::NewLine();
					echo "Reported IP is: " . $detectedip . fs_filehandler::NewLine();
					echo "(Using command: '" . $autoipsettings['ai_command_vc'] . "')" . fs_filehandler::NewLine();
					if ($detectedip != $oldip['so_value_tx']){
						if (filter_var($detectedip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)){
							echo "IP Address has changed: Updating server IP and all DNS zone records now..." . fs_filehandler::NewLine();
			        		$sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx='" . $detectedip . "' WHERE so_name_vc='server_ip'");
			        		$sql->execute();
			        		$sql = $zdbh->prepare("UPDATE x_dns SET dn_target_vc='" . $detectedip . "' WHERE dn_target_vc='".$oldip['so_value_tx']."'");
			        		$sql->execute();
			        		$sql = $zdbh->prepare("UPDATE x_autoip SET ai_newip_vc='" . $detectedip . "', ai_oldip_vc='".$oldip['so_value_tx']."', ai_lastupdate_ts='".time()."' WHERE ai_id_pk=1");
			        		$sql->execute();
							TriggerDNSUpdateAutoIPHook("0");
							//TriggerApacheUpdateAutoIPHook();
							if ($autoipsettings['ai_email_vc'] != ""){
								include(ctrl_options::GetOption('sentora_root') . 'dryden/sys/email.class.php');
								$emails = explode(",", $autoipsettings['ai_email_vc']);
								foreach ($emails as $email){
									if (IsValidAutoIPEmail($email)){
										echo "Sending alert email to: " . $email . fs_filehandler::NewLine();
										$emailsubject = "AutoIP Alert";
										$emailbody = "Your ZPanel IP address has been changed!\r\rOld IP: ".$oldip['so_value_tx']."\rNew IP: ".$detectedip."\r\rAutoIP has updated your settings.";
									    $phpmailer = new sys_email();
							        	$phpmailer->Subject = $emailsubject;
								        $phpmailer->Body = $emailbody;
								        $phpmailer->AddAddress($email);
								        $phpmailer->SendEmail();
									} else{
										echo "WARNING - Email: " . $email . " is not a valid email. Skipping..." . fs_filehandler::NewLine();
									}
								}
							}
							if ($autoipsettings['ai_script_vc'] != ""){
								echo "Running script: " . $autoipsettings['ai_script_vc'] . fs_filehandler::NewLine();
								if (sys_versions::ShowOSPlatformVersion() == "Windows"){
									@exec($autoipsettings['ai_script_vc']);
								} else {
									@exec(ctrl_options::GetOption('zsudo') . " " . $autoipsettings['ai_script_vc']);
								}
							}
						}
					} else {
						echo "IP Address has NOT changed." . fs_filehandler::NewLine();
					}
				} else {
					echo "AutoIP DIABLED..." . fs_filehandler::NewLine();
				}			
			}			
		}
    }

   	function TriggerDNSUpdateAutoIPHook($id) {
		global $zdbh;
        global $controller;
        $GetRecords = ctrl_options::GetOption('dns_hasupdates');
		$records = explode(",", $GetRecords);
		foreach ($records as $record){
			$RecordArray[] = $record;
		}
		if (!in_array($id, $RecordArray)){	
        	$newlist = $GetRecords . "," . $id;
	        $newlist = str_replace(",,", ",", $newlist);
	        $sql = "UPDATE x_settings SET so_value_tx='" . $newlist . "' WHERE so_name_vc='dns_hasupdates'";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
	        return true;
		}
    }

    function TriggerApacheUpdateAutoIPHook() {
        global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx='true'
								WHERE so_name_vc='apache_changed'");
        $sql->execute();
    }

	function execAutoIPScriptInBackground($cmd) {
	    if (sys_versions::ShowOSPlatformVersion() == "Windows"){
	        pclose(popen("start /B ". $cmd, "r")); 
	    }
	    else {
	        exec($cmd . " > /dev/null &");  
	    }
	}

    function IsValidAutoIPEmail($email) {
        if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email)) {
            return false;
        }
        return true;
    }
?>