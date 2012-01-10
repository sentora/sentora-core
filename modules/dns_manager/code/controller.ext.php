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
		$line = "";
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
	
	static function doDisplayRecords(){
		global $zdbh;
		global $controller;
		self::$editdomain = $controller->GetControllerRequest('FORM', 'inDomain');
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