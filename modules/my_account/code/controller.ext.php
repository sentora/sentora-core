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

    static $ok;
    static $blank;
    static $emailerror;

    static function getAccountSettings()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $res = array();
        array_push($res, array('fullname' => runtime_xss::xssClean($currentuser['fullname']),
            'email' => runtime_xss::xssClean($currentuser['email']),
            'phone' => runtime_xss::xssClean($currentuser['phone']),
            'address' => runtime_xss::xssClean($currentuser['address']),
            'postcode' => runtime_xss::xssClean($currentuser['postcode'])));
        return $res;
    }

    static function getLangList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $res = array();
        $column_names = ui_language::GetColumnNames('x_translations');
        foreach ($column_names as $column_name) {
            if ($column_name != 'tr_id_pk') {
                $column_name = explode('_', $column_name);
                $lang = $column_name[1];
                if ($lang == $currentuser['language']) {
                    $selected = "SELECTED";
                } else {
                    $selected = "";
                }
                array_push($res, array('language' => $lang, 'selected' => $selected));
            }
        }
        return $res;
    }

    static function doUpdateAccountSettings()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $userid = $currentuser['userid'];
        $email = $controller->GetControllerRequest('FORM', 'inEmail');
        $fullname = $controller->GetControllerRequest('FORM', 'inFullname');
        $language = $controller->GetControllerRequest('FORM', 'inLanguage');
        $phone = $controller->GetControllerRequest('FORM', 'inPhone');
        $address = $controller->GetControllerRequest('FORM', 'inAddress');
        $postalCode = $controller->GetControllerRequest('FORM', 'inPostalCode');

        if (!fs_director::CheckForEmptyValue(self::ExecuteUpdateAccountSettings($userid, $email, $fullname, $language, $phone, $address, $postalCode))) {
            runtime_hook::Execute('OnAfterUpdateMyAccount');
            self::$ok = true;
        }
    }

    static function ExecuteUpdateAccountSettings($userid, $email, $fullname, $language, $phone, $address, $postalCode)
    {
        global $zdbh;
        $email = strtolower(str_replace(' ', '', $email));
        $fullname = ucwords($fullname);
        if (fs_director::CheckForEmptyValue(self::CheckUpdateForErrors($email, $fullname, $language, $phone, $address, $postalCode))) {
            return false;
        }
        $currentuser = ctrl_users::GetUserDetail();
        $sql = $zdbh->prepare("UPDATE x_accounts SET ac_email_vc = :email WHERE ac_id_pk = :userid");
        $sql->bindParam(':email', $email);
        $sql->bindParam(':userid', $userid);
        $sql->execute();
        $sql = $zdbh->prepare("UPDATE x_profiles SET ud_fullname_vc = :fullname, ud_language_vc = :language, ud_phone_vc = :phone, ud_address_tx  = :address, ud_postcode_vc = :postcode WHERE ud_user_fk = :userid");
        $sql->bindParam(':fullname', $fullname);
        $sql->bindParam(':language', $language);
        $sql->bindParam(':phone', $phone);
        $sql->bindParam(':address', $address);
        $sql->bindParam(':postcode', $postalCode);
        $sql->bindParam(':userid', $userid);
        $sql->execute();
        return true;
    }

    static function CheckUpdateForErrors($email, $fullname, $language, $phone, $address, $postalCode)
    {
        global $zdbh;
        if (fs_director::CheckForEmptyValue($email) ||
                fs_director::CheckForEmptyValue($fullname) ||
                fs_director::CheckForEmptyValue($language) ||
                fs_director::CheckForEmptyValue($phone) ||
                fs_director::CheckForEmptyValue($address) ||
                fs_director::CheckForEmptyValue($postalCode)) {
            self::$blank = true;
            return false;
        }
        if (!self::IsValidEmail($email)) {
            self::$emailerror = true;
            return false;
        }
        return true;
    }

    static function IsValidEmail($email)
    {
        if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email)) {
            return false;
        }
        return true;
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You must fill out all fields!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$emailerror)) {
            return ui_sysmessage::shout(ui_language::translate("Your email address is not valid!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your account settings have been saved successfully!"), "zannounceok");
        }
        return;
    }

}
