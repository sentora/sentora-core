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

    static function getDNSConfig() {
        $display = self::DisplayDNSConfig();
        return $display;
    }


    static function DisplayDNSConfig() {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Configure your DNS Settings") . "</h2>";
		$line .= "<div style=\"display: block; margin-right:20px;\">";
		$line .= "<div class=\"ui-tabs ui-widget ui-widget-content ui-corner-all\" id=\"dnsTabs\">";
		$line .= "<ul class=\"domains ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\">";
		$line .= "<li><a href=\"#general\">General</a></li>";
		$line .= "<li><a href=\"#tools\">Tools</a></li>";
		$line .= "</ul>";
		$line .= "<div class=\"records dnsRecordA ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"general\">";
        $line .= "<form action=\"./?module=dns_admin&action=UpdateDNSConfig\" method=\"post\">";
        $line .= "<table class=\"zgrid\">";
        $count = 0;
        $sql = "SELECT COUNT(*) FROM x_dns_settings WHERE dns_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns_settings WHERE dns_usereditable_en = 'true'");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $count++;
                    $line .= "<tr valign=\"top\"><th>" . $row['dns_cleanname_vc'] . "</th><td><textarea cols=\"45\" rows=\"1\" name=\"" . $row['dns_name_vc'] . "\">" . $row['dns_value_tx'] . "</textarea></td><td>" . $row['dns_desc_tx'] . "</td></tr>";
                }
                $line .= "<tr><th><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSaveSystem\">Save Changes</button></th><td></td><td></td></tr>";
            }
        }
        $line .= "</table>";
        $line .= "</form>";
		$line .= "</div>";
		$line .= "<div class=\"records dnsRecordA ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"tools\">";
		$line .= "</div>";
        $line .= "<table class=\"zgrid\">";
		$line .= "<tr>";
		$line .= "<th>Reset all records to default</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inResetAll\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Add Records to Missing Domains</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inResetAll\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Delete Record Type from ALL Records</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inResetAll\">GO</button></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>Delete ALL Zone Records</th>";
		$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inResetAll\">GO</button></td>";
		$line .= "</tr>";
        $line .= "</table>";
		$line .= "</div>";
        return $line;
    }

    static function doUpdateDNSConfig() {
        global $zdbh;
        global $controller;
        $sql = "SELECT COUNT(*) FROM x_dns_settings WHERE dns_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns_settings WHERE dns_usereditable_en = 'true'");
                $sql->execute();
                while ($row = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['dns_name_vc']))) {
                        $updatesql = $zdbh->prepare("UPDATE x_dns_settings SET dns_value_tx = '" . $controller->GetControllerRequest('FORM', $row['dns_name_vc']) . "' WHERE dns_name_vc = '" . $row['dns_name_vc'] . "'");
                        $updatesql->execute();
                    }
                }
            }
        }
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your DNS settings have been saved successfully!"));
        } else {
            return ui_language::translate(ui_module::GetModuleDescription());
        }
        return;
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

    static function GetDNSOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT dns_value_tx FROM x_dns_settings WHERE dns_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['dns_value_tx'];
        } else {
            return false;
        }
    }

}

?>