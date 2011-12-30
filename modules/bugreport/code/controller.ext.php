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

	
	static function getBugReport(){
		# Set the default infomation submitted in the bug form
		$zpanelurl = $_SERVER['SERVER_NAME'];
		$serversoft = $_SERVER['SERVER_SOFTWARE'];
		$phpversion = sys_versions::ShowPHPVersion();
		$mysqlversion = sys_versions::ShowMySQLVersion();
		$apacheversion = sys_versions::ShowApacheVersion();
		$zpanelversion = ctrl_options::GetOption('dbversion');
		$returnurl = $_SERVER['HTTP_HOST'];
		$currentuser = ctrl_users::GetUserDetail();
		
    	$line  = '<form name="frmReport" id="frmReport" target="_blank" method="post" action="'.ctrl_options::GetOption('bugreport_url').'?secure='. base64_encode('' . $returnurl . '|||' . $zpanelurl . '|||' . $serversoft . '|||' . $apacheversion . '|||' . $phpversion . '|||' . $mysqlversion . '|||' . $zpanelversion . '').'">';
	    $line .= '<table class="zform">';
	    $line .= '    <tr>';
	    $line .= '       <th>Contact email:</th>';
	    $line .= '       <td><input name="bugEmail" id="bugEmail" value="'.$currentuser['email'].'" size="60" type="text"></td>';
	    $line .= '    </tr>';
	    $line .= '    <tr>';
	    $line .= '       <th valign="top">Bug Summary:</th>';
	    $line .= '       <td><input name="bugSummary" id="bugSummary" size="60" type="text">';
	    $line .= '       </td>';
	    $line .= '    </tr>';
	    $line .= '    <tr>';
	    $line .= '       <th valign="top">Bug Description:</th>';
	    $line .= '       <td><textarea name="bugDescription" cols="50" rows="10" id="bugDescription"></textarea></td>';
	    $line .= '    </tr>';
	    $line .= '    <tr>';
	    $line .= '       <td>&nbsp;</td>';
	    $line .= '       <td align="right"><button class="fg-button ui-state-default ui-corner-all" type="submit" id="button" name="Submit">Send Report</button></td>';
	    $line .= '    </tr>';
	    $line .= ' </table>';
		$line .= '</form>';
		
	return $line;

	}


	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }
	
	static function getModuleDescription() {
		$module_name = ui_module::GetModuleDescription();
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
}

?>
