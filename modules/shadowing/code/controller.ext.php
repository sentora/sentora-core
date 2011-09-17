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

	static $currentuser;
	static $clientuserid;
	static $shout;

    static function getShadowAccounts() {
		global $zdbh;
		$currentuser = ctrl_users::GetUserDetail();
		$line = "";
		
		$sql = "SELECT COUNT(*) FROM x_accounts WHERE ac_reseller_fk = '" . $currentuser['userid'] . "'";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() <> 0) {
						
	 			$sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk = '" . $currentuser['userid'] . "'");
	 			$sql->execute();
		
				while ($rowclients = $sql->fetch()) {
					$clientuserid = ctrl_users::GetUserDetail($rowclients['ac_id_pk']);
					$line .= "<tr><td>".$clientuserid['username']."</td><td>TODO</td><td>TODO</td><td>TODO</td><td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inShadow_".$rowclients['ac_id_pk']."\" id=\"inShadow_".$rowclients['ac_id_pk']."\">Shadow</button></td></tr>\n";
					//$line .= "<input type=\"submit\" name=\"inShadow_".$rowclients['ac_id_pk']."\" id=\"inShadow_".$rowclients['ac_id_pk']."\" value=\"Shadow\">";
				}
			}else{
			$line = "<tr><td colspan=\"5\">You have no Clients at this time.</td></tr>\n";
			}
		}
		return $line;
	}
	
	
	
	
	
    static function doShadowUser() {
		global $zdbh;
        global $controller;

		self::$currentuser = ctrl_users::GetUserDetail();
		$sql = "SELECT COUNT(*) FROM x_accounts WHERE ac_reseller_fk = '" . self::$currentuser['userid'] . "'";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() <> 0) {
			
				$sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk = '" . self::$currentuser['userid'] . "'");
	 			$sql->execute();
					while ($rowclients = $sql->fetch()) {
					
						if ($controller->GetControllerRequest('FORM', 'inShadow_'.$rowclients['ac_id_pk'])) {
							 self::$shout = "";
                             ctrl_auth::SetUserSession($rowclients['ac_id_pk']);
                             header("location: /");
                             exit;
						}
					}
			}
		}
		
    }
	

	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }

}

?>
