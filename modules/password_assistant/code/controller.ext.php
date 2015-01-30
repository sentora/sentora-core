<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
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
class module_controller extends ctrl_module
{

    static $error;
    static $badpassword;

    static function doUpdatePassword()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $current_pass = $controller->GetControllerRequest('FORM', 'inCurPass');
        $newpass = $controller->GetControllerRequest('FORM', 'inNewPass');
        $conpass = $controller->GetControllerRequest('FORM', 'inConPass');

        $crypto = new runtime_hash;
        $crypto->SetPassword($newpass);
        $randomsalt = $crypto->RandomSalt();
        $crypto->SetSalt($randomsalt);
        $new_secure_password = $crypto->CryptParts($crypto->Crypt())->Hash;


        $sql = $zdbh->prepare("SELECT ac_pass_vc, ac_passsalt_vc FROM x_accounts WHERE ac_id_pk= :uid");
        $sql->bindParam(':uid', $currentuser['userid']);
        $sql->execute();
        $result = $sql->fetch();
        $userpasshash = new runtime_hash;
        $userpasshash->SetPassword($current_pass);
        $userpasshash->SetSalt($result['ac_passsalt_vc']);
        $current_secure_password = $userpasshash->CryptParts($userpasshash->Crypt())->Hash;

        if (fs_director::CheckForEmptyValue($newpass)) {
            // Current password is blank!
            self::$error = "error";
        } elseif ($current_secure_password <> $result['ac_pass_vc']) {
            // Current password does not match!
            self::$error = "nomatch";
        } else {
            if ($newpass == $conpass) {
                // Check for password length...
                if (strlen($newpass) < ctrl_options::GetSystemOption('password_minlength')) {
                    self::$badpassword = true;
                    return false;
                }
                // Check that the new password matches the confirmation box.
                $sql = $zdbh->prepare("UPDATE x_accounts SET ac_pass_vc=:new_secure_password, ac_passsalt_vc= :randomsalt WHERE ac_id_pk=:userid");
                $sql->bindParam(':randomsalt', $randomsalt);
                $sql->bindParam(':new_secure_password', $new_secure_password);
                $sql->bindParam(':userid', $currentuser['userid']);
                $sql->execute();
                self::$error = "ok";
            } else {
                self::$error = "error";
            }
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$error)) {
            if (self::$error == "ok") {
                return ui_sysmessage::shout(ui_language::translate("Your account password been changed successfully!"), "zannounceok");
            }
            if (self::$error == "nomatch") {
                return ui_sysmessage::shout(ui_language::translate("Sorry, your current password does not match the one on your account!"), "zannounceerror");
            }
            if (self::$error == "error") {
                return ui_sysmessage::shout(ui_language::translate("An error occured and your Sentora account password could not be updated. Please ensure you entered all passwords correctly and try again."), "zannounceerror");
            }
        } else {
            if (!fs_director::CheckForEmptyValue(self::$badpassword)) {
                return ui_sysmessage::shout(ui_language::translate("Your password did not meet the minimun length requirements. Characters needed for password length") . ": " . ctrl_options::GetSystemOption('password_minlength'), "zannounceerror");
            }
            return;
        }
    }

    static function UpdatePassword($uid, $password)
    {
        global $zdbh;
        $crypto = new runtime_hash;
        $crypto->SetPassword($password);
        $randomsalt = $crypto->RandomSalt();
        $crypto->SetSalt($randomsalt);
        $secure_password = $crypto->CryptParts($crypto->Crypt())->Hash;
        $sql = $zdbh->prepare("UPDATE x_accounts SET ac_pass_vc=:secure_password, ac_passsalt_vc= :randomsalt WHERE ac_id_pk=:userid");
        $sql->bindParam(':randomsalt', $randomsalt);
        $sql->bindParam(':secure_password', $secure_password);
        $sql->bindParam(':userid', $uid);
        $sql->execute();
        return true;
    }

}
