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

    static $error;

    static function doUpdatePassword() {
        global $zdbh;
        global $controller;

        $currentuser = ctrl_users::GetUserDetail();

        $current_pass = $controller->GetControllerRequest('FORM', 'inCurPass');
        $newpass = $controller->GetControllerRequest('FORM', 'inNewPass');
        $conpass = $controller->GetControllerRequest('FORM', 'inConPass');
        $doresetmysql = $controller->GetControllerRequest('FORM', 'inResMySQL');

        if (md5($current_pass) <> $currentuser['password']) {
            # Current password does not match!
            self::$error = "matcherror";
        } else {
            if ($newpass == $conpass) {
                # Check that the new password matches the confirmation box.
                if ($doresetmysql <> '1') {
                    # User has selected to update ZPanel account password only!
                    $sql = $zdbh->prepare("UPDATE x_accounts SET ac_pass_vc='" . md5($newpass) . "' WHERE ac_id_pk=" . $currentuser['userid'] . "");
                    $sql->execute();
                    $error = "ok";
                } else {
                    # User has selected to change both passwords.
                    $sql = $zdbh->prepare("UPDATE x_accounts SET ac_pass_vc='" . md5($newpass) . "' WHERE ac_id_pk=" . $currentuser['userid'] . "");
                    $sql->execute();
                    # Set the MySQL password as well.
                    $sql = $zdbh->prepare("SET PASSWORD FOR `" . $currentuser['username'] . "`@`%`=PASSWORD('" . $newpass . "')");
                    $sql->execute();
                    $error = "ok-both";
                }
            } else {
                $error = "matcherror";
            }
        }
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$error)) {
            if (self::$error == "ok") {
                return ui_sysmessage::shout(ui_language::translate("Your account password been changed successfully!"));
            }
            if (self::$error == "ok-both") {
                return ui_sysmessage::shout(ui_language::translate("Your account and MySQL password been changed successfully!"));
            }
            if (self::$error == "matcherror") {
                return ui_sysmessage::shout(ui_language::translate("An error occured and your ZPanel account password could not be updated. Please ensure you entered all passwords correctly and try again."));
            }
        } else {
           return;
        }
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

    static function UpdatePassword($uid, $password) {
        global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_accounts SET ac_pass_vc='" . md5($password) . "' WHERE ac_id_pk=$uid");
        $sql->execute();
        return true;
    }

	static function getModuleDesc() {
		$message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

}

?>
