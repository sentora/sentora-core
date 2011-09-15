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

	static $hasupdated;

	static function getAccountSettings (){
	
		$currentuser = ctrl_users::GetUserDetail();
	
 		$line  = "<tr>";
		$line .= "<th>Full name:</th>";
		$line .= "<td><input name=\"inFullname\" type=\"text\" id=\"inFullname\" size=\"40\" value=\"" . $currentuser['fullname'] . "\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Email Address:</th>";
		$line .= "<td><input name=\"inEmail\" type=\"text\" id=\"inEmail\" size=\"40\" value=\"" . $currentuser['email'] . "\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Phone Number:</th>";
		$line .= "<td><input name=\"inPhone\" type=\"text\" id=\"inPhone\" size=\"20\" value=\"" . $currentuser['phone'] . "\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Choose Language</th>";
		$line .= "<td>";
		$line .= "TO DO";
		$line .= "</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Postal Address:</th>";
		$line .= "<td><textarea name=\"inAddress\" id=\"inAddress\" cols=\"45\" rows=\"5\">" . $currentuser['address'] . "</textarea></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Postal Code:</th>";
		$line .= "<td><input name=\"inPostalCode\" type=\"text\" id=\"inPostalCode\" size=\"15\" value=\"" . $currentuser['postcode'] . "\" /></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>&nbsp;</th>";
		$line .= "<td align=\"right\"><input type=\"submit\" value=\"Update Account\" /></td>";
		$line .= "</tr>	";
	
	return $line;
	}
	
	
	static function doUpdateAccountSettings(){
		global $zdbh;
		global $controller;
		
		$currentuser = ctrl_users::GetUserDetail();
			
		$sql = $zdbh->prepare("UPDATE x_accounts SET ac_email_vc = '". $controller->GetControllerRequest('FORM', 'inEmail')."' WHERE ac_id_pk = '".$currentuser['userid']."'");
	 	$sql->execute();

		$sql = $zdbh->prepare("UPDATE x_profiles SET ud_fullname_vc = '". $controller->GetControllerRequest('FORM', 'inFullname')."',
													 ud_phone_vc = '". $controller->GetControllerRequest('FORM', 'inPhone')."',
													 ud_address_tx = '". $controller->GetControllerRequest('FORM', 'inAddress')."',
													 ud_postcode_vc = '". $controller->GetControllerRequest('FORM', 'inPostalCode')."' WHERE 
													 ud_user_fk = '".$currentuser['userid']."'");
	 	$sql->execute();	
		self::$hasupdated = "yes";
	}
	
	
	static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$hasupdated)){
            return ui_sysmessage::shout("Changes to your account settings have been saved successfully!");
		}else{
			return "<p>Below is your current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.</p>";
		}
        return;
    }


}

?>
