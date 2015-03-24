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
    static $showvhost;

    static function getApacheConfig()
    {
        if (!fs_director::CheckForEmptyValue(self::$showvhost)) {
            $display = self::DisplayApacheVhost();
        } else {
            $display = self::DisplayApacheConfig();
        }
        return $display;
    }

    static function getVhostConfig()
    {
        $display = self::DisplayVhostConfig();
        return $display;
    }

    static function getDisabledVhostConfig()
    {
        $display = self::DisplayDisabledVhostConfig();
        return $display;
    }

    static function getDisplayVhostOverrides()
    {
        $display = self::DisplayVhostOverrides();
        return $display;
    }

    static function DisplayApacheConfig()
    {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Configure your Apache Settings") . "</h2>";
        $line .= "<form action=\"./?module=apache_admin&action=UpdateApacheConfig\" method=\"post\">";
        $line .= "<table class=\"table table-striped\">";
        $count = 0;
        $sql = "SELECT COUNT(*) FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true'";
        $moduleName = ui_module::GetModuleName();
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':module', $moduleName);
        $numrows->execute();
        if ($numrows) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true' ORDER BY so_cleanname_vc");
                $sql->bindParam(':module', $moduleName);
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $count++;
                    if (ctrl_options::CheckForPredefinedOptions($row['so_defvalues_tx'])) {
                        $fieldhtml = ctrl_options::OuputSettingMenuField($row['so_name_vc'], $row['so_defvalues_tx'], $row['so_value_tx']);
                    } else {
                        $fieldhtml = ctrl_options::OutputSettingTextArea($row['so_name_vc'], $row['so_value_tx']);
                    }
                    $line .= "<tr valign=\"top\"><th nowrap=\"nowrap\">" . ui_language::translate($row['so_cleanname_vc']) . "</th><td>" . $fieldhtml . "</td><td>" . ui_language::translate($row['so_desc_tx']) . "</td></tr>";
                }
                $line .= "<tr><th>" . ui_language::translate("Force Update") . "</th><td><input type=\"checkbox\"></td><td>" . ui_language::translate("Force vhost.conf to be updated on next daemon run. Any change in settings also triggers vhost.conf to be updated.") . "</td></tr>";
                $line .= "<tr><th colspan=\"3\"><button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"inSaveSystem\">" . ui_language::translate("Save Changes") . "</button><button class=\"button-loader btn btn-default\" type=\"button\" onclick=\"window.location.href='./?module=moduleadmin';return false;\">" . ui_language::translate("Cancel") . "</button></th></tr>";
            }
        }
        $line .= "</table>";
        $line .= runtime_csfr::Token();
        $line .= "</form>";
        return $line;
    }

    static function DisplayVhostConfig()
    {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Override a Virtual Host Setting") . "</h2>";
        $line .= "<form action=\"./?module=apache_admin&action=DisplayVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr><td>";
        $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"inSelectVhost\">" . ui_language::translate("Select Vhost") . "</button>";
        $line .= "</td><td>";
        $line .= "<select name=\"inVhost\" id=\"inVhost\">";
        $line .= "<option value=\"\" selected=\"selected\">-- " . ui_language::translate("Select a domain") . " --</option>";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_enabled_in=1 AND vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $line .= "<option value=\"" . $row['vh_name_vc'] . "\">" . $row['vh_name_vc'] . "</option>";
                }
            }
        }
        $line .= "</select>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= runtime_csfr::Token();
        $line .= "</form>";
        return $line;
    }

    static function getIsDisplayDisabledVhostConfig()
    {
        global $zdbh;
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_enabled_in=0 AND vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                return true;
            }
        }
        return false;
    }

    static function DisplayVhostOverrides()
    {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("All Virtual Hosts with Overrides") . "</h2>";
        $line .= "<form action=\"./?module=apache_admin&action=DisplayVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr><td>";
        $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"inSelectVhost\">" . ui_language::translate("Select Vhost") . "</button>";
        $line .= "</td><td>";
        $line .= "<select name=\"inVhost\" id=\"inVhost\">";
        $line .= "<option value=\"\" selected=\"selected\">-- " . ui_language::translate("Select a domain") . " --</option>";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    if (
                            $row['vh_suhosin_in'] == 0 ||
                            $row['vh_obasedir_in'] == 0 ||
                            $row['vh_custom_tx'] != "" ||
                            !fs_director::CheckForEmptyValue($row['vh_custom_port_in']) ||
                            $row['vh_portforward_in'] == 1 ||
                            !fs_director::CheckForEmptyValue($row['vh_custom_ip_vc'])
                    ) {
                        $line .= "<option value=\"" . $row['vh_name_vc'] . "\">" . $row['vh_name_vc'] . "</option>";
                    }
                }
            }
        }
        $line .= "</select>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= runtime_csfr::Token();
        $line .= "</form>";
        return $line;
    }

    static function getIsDisplayVhostOverrides()
    {
        global $zdbh;
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    if (
                            $row['vh_suhosin_in'] == 0 ||
                            $row['vh_obasedir_in'] == 0 ||
                            $row['vh_custom_tx'] != "" ||
                            !fs_director::CheckForEmptyValue($row['vh_custom_port_in']) ||
                            $row['vh_portforward_in'] == 1 ||
                            !fs_director::CheckForEmptyValue($row['vh_custom_ip_vc'])
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    static function DisplayDisabledVhostConfig()
    {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Disabled Virtual Hosts") . "</h2>";
        //$line .= ui_language::translate("Select a Virtual Host below.");
        $line .= "<form action=\"./?module=apache_admin&action=DisplayVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr><td>";
        $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"inSelectVhost\">" . ui_language::translate("Select Vhost") . "</button>";
        $line .= "</td><td>";
        $line .= "<select name=\"inVhost\" id=\"inVhost\">";
        $line .= "<option value=\"\" selected=\"selected\">-- " . ui_language::translate("Select a domain") . " --</option>";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_enabled_in=0 AND vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_enabled_in=0 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $line .= "<option value=\"" . $row['vh_name_vc'] . "\">" . $row['vh_name_vc'] . "</option>";
                }
            }
        }
        $line .= "</select>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= runtime_csfr::Token();
        $line .= "</form>";
        return $line;
    }

    static function DisplayApacheVhost()
    {
        global $zdbh;
        global $controller;
        $line = "<h2>" . ui_language::translate("Virtual Host Override") . "</h2>";
        $line .= ui_language::translate("Set options for virtual host") . ": <b>" . $controller->GetControllerRequest('FORM', 'inVhost') . "</b>";
        $line .= "<br><br>";
        $line .= "<form action=\"./?module=apache_admin&action=SaveVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_name_vc=:vhost AND vh_deleted_ts IS NULL";

        $inVhost = $controller->GetControllerRequest('FORM', 'inVhost');
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':vhost', $inVhost);
        $numrows->execute();

        if ($numrows) {
            if ($numrows->fetchColumn() <> 0) {
                $inVhost2 = $controller->GetControllerRequest('FORM', 'inVhost');
                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_name_vc=:vhost AND vh_deleted_ts IS NULL");
                $sql->bindParam(':vhost', $inVhost2);
                $sql->execute();
                $row = $sql->fetch();

                $line .= "<tr><th>" . ui_language::translate("Domain Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_enabled_in\" id=\"vh_enabled_in\" value=\"1\" " . fs_director::IsChecked($row['vh_enabled_in']) . "/></td></tr>";
                $line .= "<tr><th>" . ui_language::translate("Suhosin Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_suhosin_in\" id=\"vh_suhosin_in\" value=\"1\" " . fs_director::IsChecked($row['vh_suhosin_in']) . "/></td></tr>";
                $line .= "<tr><th>" . ui_language::translate("OpenBase Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_obasedir_in\" id=\"vh_obasedir_in\" value=\"1\" " . fs_director::IsChecked($row['vh_obasedir_in']) . "/></td></tr>";
                $line .= "<tr><th>" . ui_language::translate("Port Override") . "</th><td><input type=\"text\" name=\"vh_custom_port_in\" id=\"vh_custom_port_in\" maxlength=\"6\" value=\"" . $row['vh_custom_port_in'] . "\"/>";
                $line .= "<tr><th>" . ui_language::translate("Forward Port 80 to Overriden Port") . ":</th><td><input type=\"checkbox\" name=\"vh_portforward_in\" id=\"vh_portforward_in\" value=\"1\" " . fs_director::IsChecked($row['vh_portforward_in']) . "/>" . ui_language::translate("Warning requires Apache mod_rewrite to be installed on the server.") . "</td></tr>";
                $line .= "<tr><th>" . ui_language::translate("IP Override") . "</th><td><input type=\"text\" name=\"vh_custom_ip_vc\" id=\"vh_custom_ip_vc\" maxlength=\"20\" value=\"" . $row['vh_custom_ip_vc'] . "\"/>";
                $line .= "<tr valign=\"top\"><th>" . ui_language::translate("Custom Entry") . ":</th><td><textarea cols=\"60\" rows=\"10\" name=\"vh_custom_tx\">" . $row['vh_custom_tx'] . "</textarea></td></tr>";
            }
        }

        $line .= "<tr><td colspan=\"2\">";
        $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"vh_id_pk\" value=\"" . $row['vh_id_pk'] . "\">" . ui_language::translate("Save Vhost") . "</button><button class=\"button-loader btn btn-default\" type=\"button\" onclick=\"window.location.href='./?module=apache_admin';return false;\">" . ui_language::translate("Cancel") . "</button>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= runtime_csfr::Token();
        $line .= "</form>";
        return $line;
    }

    static function doDisplayVhost()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        self::$showvhost = TRUE;
    }

    static function doUpdateApacheConfig()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $sql = "SELECT COUNT(*) FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true'";

        $moduleName = ui_module::GetModuleName();
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':module', $moduleName);
        $numrows->execute();

        if ($numrows) {
            if ($numrows->fetchColumn() <> 0) {
                $moduleName2 = ui_module::GetModuleName();
                $sql = $zdbh->prepare("SELECT * FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true'");
                $sql->bindParam(':module', $moduleName2);
                $sql->execute();
                while ($row = $sql->fetch()) {
                    //if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['so_name_vc']))) {
                    $value = $controller->GetControllerRequest('FORM', $row['so_name_vc']);
                    $name = $row['so_name_vc'];
                    $updatesql = $zdbh->prepare("UPDATE x_settings SET so_value_tx = :value WHERE so_name_vc = :name");
                    $updatesql->bindParam(':value', $value);
                    $updatesql->bindParam(':name', $name);
                    $updatesql->execute();
                    self::SetWriteApacheConfigTrue();
                    //}
                }
            }
        }
    }

    static function doSaveVhost()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $port = $controller->GetControllerRequest('FORM', 'vh_custom_port_in');
        if (empty($port)) {
            $port = NULL;
        } else {
            $port = $controller->GetControllerRequest('FORM', 'vh_custom_port_in');
        }

        $ip = $controller->GetControllerRequest('FORM', 'vh_custom_ip_vc');
        if (empty($ip)) {
            $ip = NULL;
        } else {
            $ip = $controller->GetControllerRequest('FORM', 'vh_custom_ip_vc');
        }



        $sql = $zdbh->prepare("UPDATE x_vhosts SET
			vh_enabled_in  = ?,
			vh_suhosin_in  = ?,
			vh_obasedir_in = ?,
			vh_custom_port_in   = ?,
                        vh_portforward_in   = ?,
                        vh_custom_ip_vc   = ?,
			vh_custom_tx   = ?
			WHERE
			vh_id_pk = ?
			AND vh_deleted_ts IS NULL");
        $sql->execute(
                array(
                    fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_enabled_in')),
                    fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_suhosin_in')),
                    fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_obasedir_in')),
                    $port,
                    fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_portforward_in')),
                    $ip,
                    $controller->GetControllerRequest('FORM', 'vh_custom_tx'),
                    $controller->GetControllerRequest('FORM', 'vh_id_pk'),
                )
        );
        self::SetWriteApacheConfigTrue();
        self::$ok = true;
        return true;
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your settings have been saved successfully!"));
        } else {
            return ui_language::translate(ui_module::GetModuleDescription());
        }
        return;
    }

    static function SetWriteApacheConfigTrue()
    {
        global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx='true'
								WHERE so_name_vc='apache_changed'");
        $sql->execute();
    }

}