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
	static $editdomain;
	
	static function getInit(){
		global $controller;
		$line = "";
		$line .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/dns.css\"></script>";
		$line .= "<script type=\"text/javascript\" src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/dns.js\"></script>";
		return $line;
	}
	
	static function getRecordAction(){
		global $controller;
		if (fs_director::CheckForEmptyValue(self::$editdomain)){
			$display = self::DisplayDomains();
		} else {
			$display = self::DisplayRecords();
		}
		return $display;
	}
	
	static function DisplayRecords(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$domain = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_id_pk=" . self::$editdomain . " AND vh_deleted_ts IS NULL")->Fetch();
		$line  = "";
		$line .= "<h2>".ui_language::translate("DNS Records for:")." ".$domain['vh_name_vc']."</h2>";
		$line .= "<a href=\"./?module=" . $controller->GetControllerRequest('URL', 'module') . "\">".ui_language::translate("Select Another Domain")."</a>";
		return $line;
	}
	
	static function DisplayDomains(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		//$rowclient = $zdbh->query("SELECT * FROM x_accounts WHERE ac_id_pk=" . self::$clientid . " AND ac_deleted_ts IS NULL AND ac_reseller_fk=" . $currentuser['userid'] . "")->Fetch();
		$line  = "";
		$line  = "";
		$line .= "<h2>".ui_language::translate("Manage Domains")."</h2>";
		$line .= "<form action=\"./?module=dns_manager&action=DisplayRecords\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<td><select name=\"inDomain\" id=\"inDomain\">";
        $line .= "<option value=\"\" selected=\"selected\">-- ".ui_language::translate("Select a domain")." --</option>";
		$sql  = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL");
		$sql->execute();
    	while ($rowdomains = $sql->fetch()) {
        	$line .= "<option value=\"" . $rowdomains['vh_id_pk'] . "\">" . $rowdomains['vh_name_vc'] . "</option>";
    	}
        $line .= "</select></td>";
		$line .= "<td>";
		$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSelect\" value=\"" . $rowdomains['vh_id_pk'] . "\">".ui_language::translate("Select")."</button>";
		$line .= "</td>";
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
				self::$editdomain=TRUE;
				self::$clientid = $rowclients['ac_id_pk'];
				return;
			}
			if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $rowclients['ac_id_pk'] . ''))){
				self::DeleteClient($rowclients['ac_id_pk']);
				return;
			}
		}		

	}

	static function getA_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='A' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">\n";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>\n";
			$line .= "<div class=\"TTL\">\n";
			$line .= "<input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\">\n";
			$line .= "<input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\">\n";
			$line .= "</div>\n";
			$line .= "<div class=\"in\">IN</div>\n";
			$line .= "<div class=\"type\">A</div>\n";
			$line .= "<div class=\"target\">\n";
			$line .= "<input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\">\n";
			$line .= "<input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\">\n";
			$line .= "</div>\n";
			$line .= "<span class=\"delete enableToolTip\"></span>\n";
			$line .= "<span class=\"undo enableToolTip\"></span>\n";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"A\" type=\"hidden\">\n";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">\n";
			$line .= "<br>\n";
			$line .= "</div>\n";
		}		
		return $line;
	}

	static function getAAAA_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='AAAA' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">\n";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>\n";
			$line .= "<div class=\"TTL\">\n";
			$line .= "<input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\">\n";
			$line .= "<input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\">\n";
			$line .= "</div>\n";
			$line .= "<div class=\"in\">IN</div>\n";
			$line .= "<div class=\"type\">AAAA</div>\n";
			$line .= "<div class=\"target\">\n";
			$line .= "<input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\">\n";
			$line .= "<input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\">\n";
			$line .= "</div>\n";
			$line .= "<span class=\"delete enableToolTip\"></span>\n";
			$line .= "<span class=\"undo enableToolTip\"></span>\n";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"AAAA\" type=\"hidden\">\n";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">\n";
			$line .= "<br>\n";
			$line .= "</div>\n";
		}		
		return $line;
	}

	static function getCNAME_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='CNAME' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\">";
			$line .= "<input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\">";
			$line .= "<input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">CNAME</div>";
			$line .= "<div class=\"target\">";
			$line .= "<input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\">";
			$line .= "<input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\">";
			$line .= "</div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"CNAME\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}

	static function getMX_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='MX' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\"><input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\"><input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">MX</div>";
			$line .= "<div class=\"priority\"><input name=\"priority[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_priority_in']."\" type=\"text\"><input name=\"original_priority[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_priority_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"target\"><input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\"><input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\"></div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"MX\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}

	static function getTXT_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='TXT' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\"><input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\"><input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">TXT</div>";
			$line .= "<div class=\"target\"><input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\"><input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\"></div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"TXT\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}
	
	static function getSRV_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='SRV' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\"><input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\"><input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">SRV</div>";
			$line .= "<div class=\"priority\"><input name=\"priority[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_priority_in']."\" type=\"text\"><input name=\"original_priority[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_priority_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"weight\"><input name=\"weight[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_weight_in']."\" type=\"text\"><input name=\"original_weight[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_weight_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"port\"><input name=\"port[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_port_in']."\" type=\"text\"><input name=\"original_port[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_port_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"target\"><input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\"><input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\"></div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"SRV\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}

	static function getSPF_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='SPF' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\"><input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\"><input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">SPF</div>";
			$line .= "<div class=\"target\"><input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\"><input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\"></div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"SPF\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}
	
	static function getNS_Records(){
		global $zdbh;
		global $controller;
		$line = NULL;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='NS' AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
		$sql->execute();
		while ($rowdns = $sql->fetch()) {
			$line .= "<div class=\"dnsRecord row\">";
			$line .= "<div class=\"hostName\"><span>".$rowdns['dn_host_vc']."</span></div>";
			$line .= "<div class=\"TTL\"><input name=\"ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"text\"><input name=\"original_ttl[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_ttl_in']."\" type=\"hidden\"></div>";
			$line .= "<div class=\"in\">IN</div>";
			$line .= "<div class=\"type\">NS</div>";
			$line .= "<div class=\"target\"><input name=\"target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"text\"><input name=\"original_target[".$rowdns['dn_id_pk']."]\" value=\"".$rowdns['dn_target_vc']."\" type=\"hidden\"></div>";
			$line .= "<span class=\"delete enableToolTip\"></span>";
			$line .= "<span class=\"undo enableToolTip\"></span>";
			$line .= "<input name=\"type[".$rowdns['dn_id_pk']."]\" value=\"NS\" type=\"hidden\">";
			$line .= "<input class=\"delete\" name=\"delete[".$rowdns['dn_id_pk']."]\" value=\"false\" type=\"hidden\">";
			$line .= "<br>";
			$line .= "</div>";
		}		
		return $line;
	}

	static function doSaveDNS(){
		global $zdbh;
		global $controller;
		//$line = print_r($_POST);
		self::SaveDNS();
		//self::$editdomain = $controller->GetControllerRequest('FORM', 'inDomain');
		//return $line;
		self::$ok = TRUE;
	}
	
	static function doDisplayRecords(){
		global $zdbh;
		global $controller;
		self::$editdomain = $controller->GetControllerRequest('FORM', 'inDomain');
		return;
	}

	static function SaveDNS(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$dnsrecords  = array();
		//Grab form inputs in array and assign them to variables
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainName'))){
			$domainName 		= $controller->GetControllerRequest('FORM', 'domainName');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'ttl'))){
			$ttl 				= $controller->GetControllerRequest('FORM', 'ttl');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_ttl'))){
			$original_ttl 		= $controller->GetControllerRequest('FORM', 'original_ttl');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'target'))){
			$target 			= $controller->GetControllerRequest('FORM', 'target');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_target'))){
			$original_target 	= $controller->GetControllerRequest('FORM', 'original_target');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'type'))){
			$type 				= $controller->GetControllerRequest('FORM', 'type');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'delete'))){
			$delete 			= $controller->GetControllerRequest('FORM', 'delete');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'hostName'))){
			$hostName 			= $controller->GetControllerRequest('FORM', 'hostName');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'priority'))){
			$priority 			= $controller->GetControllerRequest('FORM', 'priority');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_priority'))){
			$original_priority  = $controller->GetControllerRequest('FORM', 'original_priority');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'weight'))){
			$weight 			= $controller->GetControllerRequest('FORM', 'weight');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_weight'))){
			$original_weight 	= $controller->GetControllerRequest('FORM', 'original_weight');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'port'))){
			$port 				= $controller->GetControllerRequest('FORM', 'port');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_port'))){
			$original_port 		= $controller->GetControllerRequest('FORM', 'original_port');}
		if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'newRecords'))){
			$newRecords 		= $controller->GetControllerRequest('FORM', 'newRecords');}
		//Get all existing records for domain and add the id's to an array
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
				$sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_deleted_ts IS NULL");
				$sql->execute();
				while ($rowdns = $sql->fetch()) {
					$dnsrecords[] = $rowdns['dn_id_pk'];
				}
			}
		}
		//Existing Records
		//Sort through the dns record array by id and update as needed
		foreach ($dnsrecords as $id){
			if ($delete[$id] == "true"){
				//The record has been marked for deletion, so lets delete it!
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=" . time() . " WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
			} else {
				//The record needs updating instead.
				//TTL
				if (isset($ttl[$id]) && !fs_director::CheckForEmptyValue($ttl[$id]) && $ttl[$id] != $original_ttl[$id] && is_numeric($ttl[$id])){
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_ttl_in=" . trim($ttl[$id]) . " WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
				}
				//TARGET
				if (isset($target[$id]) && !fs_director::CheckForEmptyValue($target[$id]) && $target[$id] != $original_target[$id]){
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_target_vc='" . trim($target[$id]) . "' WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
				}
				//PRIORITY
				if (isset($priority[$id]) && !fs_director::CheckForEmptyValue($priority[$id]) && $priority[$id] != $original_priority[$id]){
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_priority_in=" . trim($priority[$id]) . " WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
				}
				//WEIGHT
				if (isset($weight[$id]) && !fs_director::CheckForEmptyValue($weight[$id]) && $weight[$id] != $original_weight[$id]){
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_weight_in=" . trim($weight[$id]) . " WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
				}
				//PORT
				if (isset($port[$id]) && !fs_director::CheckForEmptyValue($port[$id]) && $port[$id] != $original_port[$id]){
				$sql = $zdbh->prepare("UPDATE x_dns SET dn_port_in=" . trim($port[$id]) . " WHERE dn_id_pk = ".$id." AND dn_deleted_ts IS NULL");
				$sql->execute();
				}
			}
		}
		//NEW Records
		//Find all new records in post array
		if (isset($newRecords) && !fs_director::CheckForEmptyValue($newRecords)){
			$numnew = $newRecords;
			$id = 1;
			while ($numnew >= $id){
				if ($delete['new_'.$id] != "true"){
					if (isset($hostName['new_'.$id]) && !fs_director::CheckForEmptyValue($hostName['new_'.$id])){
						$hostName_new = "'".$hostName['new_'.$id]."'";
					} else {
						$hostName_new = "NULL";
					}
					if (isset($type['new_'.$id]) && !fs_director::CheckForEmptyValue($type['new_'.$id])){
						$type_new = "'".$type['new_'.$id]."'";
					} else {
						$type_new = "NULL";
					}
					if (isset($ttl['new_'.$id]) && !fs_director::CheckForEmptyValue($ttl['new_'.$id])){
						$ttl_new = $ttl['new_'.$id];
					} else {
						$ttl_new = "NULL";
					}
					if (isset($target['new_'.$id]) && !fs_director::CheckForEmptyValue($target['new_'.$id])){
						$target_new = "'".$target['new_'.$id]."'";
					} else {
						$target_new = "NULL";
					}				
					if (isset($priority['new_'.$id]) && !fs_director::CheckForEmptyValue($priority['new_'.$id])){
						$priority_new = $priority['new_'.$id];
					} else {
						$priority_new = "NULL";
					}
					if (isset($weight['new_'.$id]) && !fs_director::CheckForEmptyValue($weight['new_'.$id])){
						$weight_new = $weight['new_'.$id];
					} else {
						$weight_new = "NULL";
					}
					if (isset($port['new_'.$id]) && !fs_director::CheckForEmptyValue($port['new_'.$id])){
						$port_new = $port['new_'.$id];
					} else {
						$port_new = "NULL";
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
															".$currentuser['userid'].",
															'ztest.com',
															1,
															".$type_new.",
															".$hostName_new.",
															".$ttl_new.",
															".$target_new.",
															".$priority_new.",
															".$weight_new.",
															".$port_new.",
															".time().")");
				
				/*
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
															1,
															'ztest.com',
															1,
															'A',
															'test',
															8600,
															'192.168.100.100',
															1,
															1,
															1,
															".time().")");
				*/			
				$sql->execute();				
				}
			$id++;
			}
		}
		
		/*
		echo "dnsrecords:<br>".print_r($dnsrecords)."<br>";
		echo "domainName:<br>".print_r($domainName)."<br>";
		echo "ttl:<br>".print_r($ttl)."<br>";
		echo "original_ttl:<br>".print_r($original_ttl)."<br>";
		echo "target:<br>".print_r($target)."<br>";
		echo "original_target:<br>".print_r($original_target)."<br>";
		echo "type:<br>".print_r($type)."<br>";
		echo "delete:<br>".print_r($delete)."<br>";
		echo "hostname:<br>".print_r($hostName)."<br>";
		echo "priority:<br>".print_r($priority)."<br>";
		echo "original_priority:<br>".print_r($original_priority)."<br>";
		echo "weight:<br>".print_r($weight)."<br>";
		echo "original_weight:<br>".print_r($original_weight)."<br>";
		echo "port:<br>".print_r($port)."<br>";
		echo "original_port:<br>".print_r($original_port)."<br>";
		echo "newrecords:<br>".print_r($newRecords)."<br>";
		*/
		return;
	}
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("Changes to your DNS have been saved successfully!"));
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
		$module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
	
}

?>