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

    /**
     * The 'worker' methods.
     */
    static function ExecuteUpdateTheme($uid, $theme)
    {
        global $zdbh;

        /* Set CSS back to default */
        self::ExecuteUpdateCSS($uid, 'default');

        /* Set new theme */
        $sql = $zdbh->prepare("
            UPDATE x_accounts
            SET ac_usertheme_vc = :theme
            WHERE ac_reseller_fk = :uid
            OR ac_id_pk = :uid2");
        $sql->bindParam(':theme', $theme);
        $sql->bindParam(':uid', $uid);
        $sql->bindParam(':uid2', $uid);
        $sql->execute();
        return true;
    }

    static function ExecuteUpdateCSS($uid, $css)
    {
        global $zdbh;
        $sql = $zdbh->prepare("
            UPDATE x_accounts
            SET ac_usercss_vc = :css
            WHERE ac_reseller_fk = :uid
            OR ac_id_pk = :uid2");
        $sql->bindParam(':css', $css);
        $sql->bindParam(':uid', $uid);
        $sql->bindParam(':uid2', $uid);
        $sql->execute();
        return true;
    }

    static function ExecuteShowCurrentTheme($uid)
    {
        return ui_template::GetUserTemplate();
    }

    static function ExecuteShowCurrentCSS($uid)
    {
        global $zdbh;
        //$result = $zdbh->query("SELECT ac_usercss_vc FROM x_accounts WHERE ac_id_pk = " . $uid . "")->Fetch();
        $numrows = $zdbh->prepare("SELECT ac_usercss_vc FROM x_accounts WHERE ac_id_pk = :uid");
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        $result = $numrows->fetch();
        if ($result) {
            return $result['ac_usercss_vc'];
        } else {
            return false;
        }
    }

    static function ExecuteStylesList()
    {
        return ui_template::ListAvaliableTemplates();
    }

    static function ExecuteCSSList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ui_template::ListAvaliableCSS(self::ExecuteShowCurrentTheme($currentuser['userid']));
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function getCurrentTheme()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ExecuteShowCurrentTheme($currentuser['userid']);
    }

    static function getCurrentCSS()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ExecuteShowCurrentCSS($currentuser['userid']);
    }

    static function getSelectThemeMenu()
    {
        $html = "";
        foreach (self::ExecuteStylesList() as $theme) {
            if ($theme['name'] != self::getCurrentTheme()) {
                $html .="<option value = \"" . $theme['name'] . "\">" . $theme['name'] . "</option>\n";
            } else {
                $html .="<option value = \"" . $theme['name'] . "\" selected=\"selected\">" . $theme['name'] . "</option>\n";
            }
        }
        return $html;
    }

    static function getSelectCSSMenu()
    {
        $html = "";
        foreach (self::ExecuteCSSList() as $css) {
            if ($css['name'] != self::getCurrentCSS()) {
                $html .="<option value = \"" . $css['name'] . "\">" . $css['name'] . "</option>\n";
            } else {
                $html .="<option value = \"" . $css['name'] . "\" selected=\"selected\">" . $css['name'] . "</option>\n";
            }
        }
        return $html;
    }

    static function getIsSelectCSS()
    {
        global $controller;
        $getvars = $controller->GetAllControllerRequests('URL');
        if (isset($getvars['selectcss']))
            return true;
        return false;
    }

    static function doSaveTheme()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        self::ExecuteUpdateTheme($currentuser['userid'], $formvars['inTheme']);
        if (count(self::ExecuteCSSList($formvars['inTheme'])) > 1) {
            header("location: ./?module=" . $controller->GetCurrentModule() . "&selectcss=true");
        } else {
            self::ExecuteUpdateCSS($currentuser['userid'], "");
            header("location: ./?module=" . $controller->GetCurrentModule() . "&saved=true");
        }
        exit;
    }

    static function doSaveCSS()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        self::ExecuteUpdateCSS($currentuser['userid'], $formvars['inCSS']);
        header("location: ./?module=" . $controller->GetCurrentModule() . "&saved=true");
        exit;
    }

    static function getResult()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (isset($urlvars['saved'])) {
            return ui_sysmessage::shout(ui_language::translate("Your theme configuration has been saved and has been updated for all clients!"), "zannounceok");
        }
        if (isset($urlvars['selectcss'])) {
            return ui_sysmessage::shout(ui_language::translate("This theme has more than one variation, please choose a variation you'd like to use.."), "zannounceerror");
        }
        return false;
    }

    /**
     * Webinterface sudo methods.
     */
}
