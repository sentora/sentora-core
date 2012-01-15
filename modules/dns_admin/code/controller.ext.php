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
	static $service;
	static $purged;
	static $deleted;
	static $deletedtype;
	static $type;
	static $reset;
	static $addmissing;

    static function getDNSConfig() {
        $display = self::DisplayDNSConfig();
        return $display;
    }


    static function DisplayDNSConfig() {
        global $zdbh;
		global $controller;
        $line = "<h2>" . ui_language::translate("Configure your DNS Settings") . "</h2>";
		$line .= "<div style=\"display: block; margin-right:20px;\">";
		$line .= "<div class=\"ui-tabs ui-widget ui-widget-content ui-corner-all\" id=\"dnsTabs\">";
		$line .= "<ul class=\"domains ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\">";
		$line .= "<li><a href=\"#general\">General</a></li>";
		$line .= "<li><a href=\"#tools\">Tools</a></li>";
		$line .= "<li><a href=\"#services\">Services</a></li>";
		$line .= "</ul>";
		//general
		$line .= "<div class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"general\">";
        $line .= "<form action=\"./?module=dns_admin&action=UpdateDNSConfig\" method=\"post\">";
        $line .= "<table class=\"zgrid\">";
        $count = 0;
        $sql = "SELECT COUNT(*) FROM x_dns_settings WHERE dns_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns_settings WHERE dns_usereditable_en = 'true'");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $count++;
                    $line .= "<tr valign=\"top\"><th nowrap=\"nowrap\">" . $row['dns_cleanname_vc'] . "</th><td><textarea cols=\"30\" rows=\"1\" name=\"" . $row['dns_name_vc'] . "\">" . $row['dns_value_tx'] . "</textarea></td><td>" . $row['dns_desc_tx'] . "</td></tr>";
                }
                $line .= "<tr><th colspan=\"3\"><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSaveSystem\">Save Changes</button></th><td></td><td></td></tr>";
            }
        }
        $line .= "</table>";
        $line .= "</form>";
		$line .= "</div>";
		//tools
		$line .= "<div class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"tools\">";
		$line .= "<form action=\"./?module=dns_admin&action=UpdateTools\" method=\"post\">";
        $line .= "<table class=\"zgrid\">";
		$line .= "<tr>";
		$line .= "<th>Reset all records to default</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inResetAll\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Add Records to Missing Domains";
		$line .= "</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inAddMissing\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Delete Record Type from ALL Records ";
        $line .= "<select name=\"inType\" id=\"inType\">";
        $line .= "<option value=\"A\">A</option>";
		$line .= "<option value=\"AAAA\">AAAA</option>";
		$line .= "<option value=\"CNAME\">CNAME</option>";
		$line .= "<option value=\"MX\">MX</option>";
		$line .= "<option value=\"TXT\">TXT</option>";
		$line .= "<option value=\"SRV\">SRV</option>";
		$line .= "<option value=\"SPF\">SPF</option>";
		$line .= "<option value=\"NS\">NS</option>";
        $line .= "</select>";
		$line .= "</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDeleteType\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Purge Deleted Zone Records From Database</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inPurge\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Delete ALL Zone Records</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inDeleteAll\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
        $line .= "</table>";
		$line .= "</form>";
		$line .= "</div>";
		//Services
		$line .= "<div class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"services\">";
		$line .= "<form action=\"./?module=dns_admin&action=UpdateService\" method=\"post\">";
		$line .= "<table class=\"none\" border=\"0\" cellpading=\"0\" cellspacing=\"0\" width=\"100%\"><tr valign=\"top\"><td width=\"100%\">";
        $line .= "<table class=\"zgrid\">";
		$line .= "<tr>";
		$line .= "<th>Start Service</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inStartService\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Stop Service</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inStopService\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Reload BIND</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inReloadService\" value=\"1\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<th>Service Port Status</th>";
        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(53))) {
        	$line .= "<td><font color=\"red\">STOPPED</font></td>";
        } else {
            $line .= "<td><font color=\"green\">RUNNING</font></td>";
        }
		$line .= "</tr>";
        $line .= "</table>";
		$line .= "</td><td>";
        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(53))) {
        	$line .= "<img src=\"/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.png\" border=\"0\"/>";
        } else {
            $line .= "<img src=\"/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.png\" border=\"0\"/>";
        }				
		$line .="</td></tr></table>";
		$line .= "</form>";
		$line .= "</div>";
		$line .= "</div>";
		$line .= self::DisplayDNSUsagepChart();
        return $line;
    }

    static function doUpdateDNSConfig() {
        global $zdbh;
        global $controller;
        $sql = "SELECT COUNT(*) FROM x_dns_settings WHERE dns_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns_settings WHERE dns_usereditable_en = 'true'");
                $sql->execute();
                while ($row = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['dns_name_vc']))) {
                        $updatesql = $zdbh->prepare("UPDATE x_dns_settings SET dns_value_tx = '" . $controller->GetControllerRequest('FORM', $row['dns_name_vc']) . "' WHERE dns_name_vc = '" . $row['dns_name_vc'] . "'");
                        $updatesql->execute();
                    }
                }
            }
        }
    }
	
    static function doUpdateService() {
        global $zdbh;
        global $controller;
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inStartService'))) {
			self::StartBind();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inStopService'))) {
			self::StopBind();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inReloadService'))) {
			self::ReloadBind();
		}
    }

    static function doUpdateTools() {
        global $zdbh;
        global $controller;
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inResetAll'))) {
			self::ResetAll();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inAddMissing'))) {
			self::AddMissing();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDeleteType'))) {
			self::DeleteType();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inPurge'))) {
			self::Purge();
		}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDeleteAll'))) {
			self::DeleteAll();
		}
    }
	
	static function StartBind(){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
			exec('net start '.self::GetDNSOption('bind_service').'', $out);
		}else{
			system('/etc/zpanel/bin/zsudo service '.self::GetDNSOption('bind_service').' start', $out);
			sleep(2);
		}
	}

	static function StopBind(){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
			exec('net stop '.self::GetDNSOption('bind_service').'', $out);
		}else{
			system('/etc/zpanel/bin/zsudo service '.self::GetDNSOption('bind_service').' stop', $out);
			sleep(2);
		}
	}
	
	static function ReloadBind(){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
			$reload_bind = self::GetDNSOption('bind_dir').'bin/rndc.exe reload';
		}else{
			$reload_bind = "/etc/zpanel/bin/zsudo service ".self::GetDNSOption('bind_service')." reload";
		}
		pclose(popen($reload_bind,'r'));
	}
	
	static function ResetAll(){
        global $zdbh;
        global $controller;
		$vhosts = array();
		$numrecords = 0;
		//Get a list of current domains with records
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$vhosts[] = $row['dn_vhost_fk'];
					$numrecords++;
				}
			}
		}
		self::$reset = $numrecords;
		//Delete current records
		self::DeleteAll();
		//Create Default Records
		foreach	($vhosts as $vhost){
			self::CreateDefaultRecords($vhost);
		}
	}

	static function AddMissing(){
        global $zdbh;
        global $controller;
		$vhosts = array();
		$numrecords = 0;
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
                $sql->execute();
                while ($row = $sql->fetch()) {
				$vhosts[] = $row['vh_id_pk'];
				}
			}
		}					
		if (!fs_director::CheckForEmptyValue($vhosts)){
			foreach	($vhosts as $vhost){
				$sql = "SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk = ".$vhost." AND dn_deleted_ts IS NULL";
        		if ($numrows = $zdbh->query($sql)) {
        			if ($numrows->fetchColumn() == 0) {
						self::CreateDefaultRecords($vhost);
						$numrecords++;
					}
				}
			}
			self::$addmissing = $numrecords;
		}
	}

	static function DeleteType(){
        global $zdbh;
        global $controller;
		$numrecords = 0;
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_type_vc = '" . $controller->GetControllerRequest('FORM', 'inType') . "' AND dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_type_vc = '" . $controller->GetControllerRequest('FORM', 'inType') . "' AND dn_deleted_ts IS NULL");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$delete_record = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=".time()." WHERE dn_id_pk = ".$row['dn_id_pk']." AND dn_type_vc = '" . $controller->GetControllerRequest('FORM', 'inType') . "'");
	                $delete_record->execute();
					$numrecords++;
				}
				self::$deletedtype = $numrecords;
				self::$type = $controller->GetControllerRequest('FORM', 'inType');
			}
		}
	}
	
	static function Purge(){
        global $zdbh;
        global $controller;
		$numrecords = 0;
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NOT NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NOT NULL");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$delete_record = $zdbh->prepare("DELETE FROM x_dns WHERE dn_id_pk = ".$row['dn_id_pk']."");
	                $delete_record->execute();
					
					$numrecords++;
				}
				self::$purged = $numrecords;
			}
		}
	}
	
	static function DeleteAll(){
        global $zdbh;
        global $controller;
		$numrecords = 0;
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$delete_record = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=".time()." WHERE dn_id_pk = ".$row['dn_id_pk']."");
	                $delete_record->execute();
					$numrecords++;
				}
				self::$deleted = $numrecords;
			}
		}
	}

    static function DisplayDNSUsagepChart() {
        global $zdbh;
        global $controller;
		$numtotalrecords = 0;
		$numactiverecords = 0;
        $sql = "SELECT COUNT(*) FROM x_dns";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$numtotalrecords++;
				}
			}
		}
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL");
                $sql->execute();
                while ($row = $sql->fetch()) {
					$numactiverecords++;
				}
			}
		}
		$total   = $numtotalrecords;
		$active  = $numactiverecords;
		$deleted = $total - $active;
		$line  = "<h2>DNS Database Usage</h2>";		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$active."::".$deleted."&labels=Active Domain Records:   ".$active."::Deleted Domain Records: ".$deleted."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$reset)) {
            return ui_sysmessage::shout(number_format(self::$reset) . " " . ui_language::translate("Domains records where reset to default"));
        }
        if (!fs_director::CheckForEmptyValue(self::$addmissing)) {
            return ui_sysmessage::shout(number_format(self::$addmissing) . " " . ui_language::translate("Domains records were created"));
        }
        if (!fs_director::CheckForEmptyValue(self::$deletedtype)) {
            return ui_sysmessage::shout(number_format(self::$deletedtype) . " '" .  self::$type . "' " . ui_language::translate("Records where marked as deleted from the database"));
        }
        if (!fs_director::CheckForEmptyValue(self::$deleted)) {
            return ui_sysmessage::shout(number_format(self::$deleted) . " " . ui_language::translate("Records where marked as deleted from the database"));
        }
        if (!fs_director::CheckForEmptyValue(self::$purged)) {
            return ui_sysmessage::shout(number_format(self::$purged) . " " . ui_language::translate("Records where purged from the database"));
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your DNS settings have been saved successfully!"));
        } else {
            return ui_language::translate(ui_module::GetModuleDescription());
        }
        return;
    }

    static function getModuleName() {
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "./modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    static function GetDNSOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT dns_value_tx FROM x_dns_settings WHERE dns_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['dns_value_tx'];
        } else {
            return false;
        }
    }

	static function CreateDefaultRecords($vh_acc_fk){
		global $zdbh;
		global $controller;
		$domainID = $vh_acc_fk;
		$domainName = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_id_pk=" . $domainID . " AND vh_deleted_ts IS NULL")->Fetch();
		$userID = $domainName['vh_acc_fk'];
		if (!fs_director::CheckForEmptyValue(ctrl_options::GetOption('server_ip'))){
			$target = ctrl_options::GetOption('server_ip');
		} else {
			$target = $_SERVER["SERVER_ADDR"];
		}
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'A',
															'@',
															3600,
															'".$target."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'CNAME',
															'www',
															3600,
															'@',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'CNAME',
															'ftp',
															3600,
															'@',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'A',
															'mail',
															86400,
															'".$target."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'MX',
															'@',
															86400,
															'mail.".$domainName['vh_name_vc']."',
															10,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'A',
															'ns1',
															172800,
															'".$target."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'A',
															'ns2',
															172800,
															'".$target."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'NS',
															'@',
															172800,
															'ns1.".$domainName['vh_name_vc']."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															".$userID.",
															'".$domainName['vh_name_vc']."',
															".$domainID.",
															'NS',
															'@',
															172800,
															'ns2.".$domainName['vh_name_vc']."',
															NULL,
															NULL,
															NULL,
															".time().")");		
		$sql->execute();			
		return;
	}
	
}
/*
if (ShowServerPlatform() == "Windows") {
	$bindlog = GetSystemOption('windows_drive').':Zpanel/logs/bind/bind.log';
}else{
	$bindlog = '/var/zpanel/logs/bind/bind.log';
}

if (file_exists($bindlog)){
	$handle = @fopen($bindlog, "r");
	$getlog = array();
		if ($handle) { 
    		while (!feof($handle)) {
        	$buffer = fgets($handle, 4096);
			$getlog[] = $buffer;
				if (strstr($buffer,'error:')){
        			$logerror[] = $buffer;
				}
				if (strstr($buffer,'warning:')){
        		$logwarning[] = $buffer;
				}
    		}fclose($handle);
		}
}
*/
?>