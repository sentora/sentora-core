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


    static function getBugReport() {

		# Set the default infomation submitted in the bug form
		$currentuser   = ctrl_users::GetUserDetail();
		$zpanelurl     = $_SERVER['SERVER_NAME'];
		$serversoft    = $_SERVER['SERVER_SOFTWARE'];
		$phpversion    = sys_versions::ShowPHPVersion();
		$mysqlversion  = sys_versions::ShowMySQLVersion();
		$apacheversion = sys_versions::ShowApacheVersion();
		$zpanelversion = ctrl_options::GetOption('dbversion');
		$returnurl     = $_SERVER['HTTP_HOST'];
		$secure = base64_encode('' . $returnurl     . '|||' 
								   . $zpanelurl     . '|||' 
								   . $serversoft    . '|||' 
								   . $apacheversion . '|||' 
								   . $phpversion    . '|||' 
								   . $mysqlversion  . '|||' 
								   . $zpanelversion . '');
        $res = array();
        array_push($res, array( 'currentuseremail'	=> $currentuser['email'],
								'bugreport_url'     => $_SERVER['SERVER_NAME'],
							    'secure' 			=> $secure));

         return $res;
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
