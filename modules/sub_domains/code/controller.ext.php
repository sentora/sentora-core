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
		
			$sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=2";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
			
			$line = "<h2>Current sub-domains</h2>";
			$line .= "<form action=\"./?module=sub_domains&action=CreateDomain\" method=\"post\">";
    		$line .= "<table class=\"zgrid\">";
        	$line .= "<tr>";
	        $line .= "<th>Sub-domain name</th>";
	        $line .= "<th>Home directory</th>";
	        $line .= "<th>Status</th>";
	        $line .= "<th></th>";
	        $line .= "</tr>";
			
					$sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_deleted_ts IS NULL AND vh_type_in=2");
		 			$sql->execute();
				
						while ($rowdomains = $sql->fetch()) {
				        	$line .= "<tr>";
					        $line .= "<td>".$rowdomains['vh_name_vc']."</td>";
					        $line .= "<td>".$rowdomains['vh_directory_vc']."</td>";
					        $line .= "<td>";
				
					        if ($rowdomains['vh_active_in'] == 1) {
					            $line .= "<font color=\"green\">Live</font>";
					        } else {
					            $line .= "<font color=\"orange\">Pending</font> <a href=\"#\" title=\"Your domain will become active at the next scheduled update.  This can take up to one hour.\"><img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/help_small.png\"></a>";
					        }
							
					        $line .= "</td>";
					        $line .= "<td><input type=\"submit\" name=\"inDelete_".$rowdomains['vh_id_pk']."\" id=\"inDelete_".$rowdomains['vh_id_pk']."\" value=\"Delete\" /></td>";
					        $line .= "</tr>";
						}
	
		$line .= "</table>";
		$line .= "</form>";
				} else {
		$line = ui_sysmessage::shout("You currently do not have any sub-domains configured.");		
				}
			}
		
		return $line;	
	}

	static function getCreateDomainForm(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
		
		$line  = "<h2>Create a new sub-domain</h2>";
		$line .= "<form action=\"./?module=sub_domains&action=CreateDomain\" method=\"post\">";
   	 	$line .= "<table class=\"zgrid\">";
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
                        
		$handle = @opendir(ctrl_options::GetOption('hosted_dir') . $currentuser['username']);
        $chkdir = ctrl_options::GetOption('hosted_dir') . $currentuser['username'] . "/";
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
		$line .= "<th colspan=\"2\" align=\"right\">";
		$line .= "<input type=\"submit\" name=\"inSubmit\" id=\"inSubmit\" value=\"Create\" /></th>";
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


}

?>
