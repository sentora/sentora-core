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

    static function getApacheConfig() {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_mail_settings WHERE mbs_usereditable_en = 'true' ORDER BY mbs_cleanname_vc";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmailsettings = $sql->fetch()) {
                array_push($res, array('cleanname'   => $rowmailsettings['mbs_cleanname_vc'],
									   'name' 		 => $rowmailsettings['mbs_name_vc'],
									   'description' => $rowmailsettings['mbs_desc_tx'],
									   'value' 		 => $rowmailsettings['mbs_value_tx']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function doUpdateMailConfig() {
        global $zdbh;
        global $controller;
        $sql = "SELECT * FROM x_mail_settings WHERE mbs_usereditable_en = 'true'";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
 			 $sql = $zdbh->prepare($sql);
             $sql->execute();
                while ($row = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['mbs_name_vc']))) {
                        $updatesql = $zdbh->prepare("UPDATE x_mail_settings SET mbs_value_tx = '" . $controller->GetControllerRequest('FORM', $row['mbs_name_vc']) . "' WHERE mbs_name_vc = '" . $row['mbs_name_vc'] . "'");
                        $updatesql->execute();
                    }
                }
        }
    }


    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your VHost settings have been saved successfully!"));
        }
        return;
    }

    static function getModuleDescription() {
        $module_desc = ui_language::translate(ui_module::GetModuleDescription());
        return $module_desc;
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
}

?>