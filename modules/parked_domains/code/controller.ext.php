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
	
	static function getDomains(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
			$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=3";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
			
			$line = "<h2>Current parked domains</h2>";
			$line .= "<form action=\"./?module=parked_domains&action=CreateDomain\" method=\"post\">";
    		$line .= "<table class=\"zgrid\">";
			$line .= "<tr>";
			$line .= "<th>Domain name</th>";
			$line .= "<th>Date parked</th>";
			$line .= "<th>Status</th>";
			$line .= "<th></th>";
			$line .= "</tr>";
			
			$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=3");
		 	$sql->execute();
			
			while ($rowdomains = $sql->fetch()) {
				$line .= "<tr>";
				$line .= "<td>" . $rowdomains['vh_name_vc'] . "</td>";
				$line .= "<td>" . date(ctrl_options::GetOption('zpanel_df'), $rowdomains['vh_created_ts']) . "</td>";
				$line .= "<td>";
			        if ($rowdomains['vh_active_in'] == 1) {
						$line .= "<font color=\"green\">Live</font>";
			        } else {
						$line .= "<font color=\"orange\">Pending</font> <a href=\"#\" title=\"Your domain will become active at the next scheduled update.  This can take up to one hour.\"><img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/help_small.png\"></a>";
			        }
				$line .= "</td>";
				$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inDelete_".$rowdomains['vh_id_pk']."\" id=\"inDelete_".$rowdomains['vh_id_pk']."\">Delete</button></td>";
				$line .= "</tr>";
			}
			
			$line .= "</table>";
			$line .= "</form>";
				} else {
		$line = ui_sysmessage::shout("You currently do not have any parked domains configured.");		
				}
			}
		
		return $line;	
	}

	static function getCreateDomainForm(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
		$line  = "<h2>Create a new parked domain</h2>";
		$line .= "<form action=\"./?module=parked_domains&action=CreateDomain\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>Domain name:</th>";
        $line .= "<td><input name=\"inDomain\" type=\"text\" id=\"inDomain\" size=\"30\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th colspan=\"2\" align=\"right\">";
        $line .= "<input type=\"hidden\" name=\"inAction\" value=\"NewParkedDomain\" />";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inSubmit\" id=\"inSubmit\">Create</button></th>";
        $line .= "</tr>";
        $line .= "</table>";
		$line .= "</form>";	
		
		return $line;
			
	}
	
	static function doCreateDomain(){
	global $zdbh;
	global $controller;


	self::$complete = TRUE;
	}
	
	static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$complete)){
            return ui_sysmessage::shout("Changes to your domain web hosting has been saved successfully.");
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
		$module_icon = "/etc/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
	
}

?>
