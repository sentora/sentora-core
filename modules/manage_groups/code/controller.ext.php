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

    static function getModuleName() {
        $module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    static function getGroupList() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_groups WHERE ug_reseller_fk=" . $currentuser['userid'] . "";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $line = "<form action=\"./?module=manage_groups&action=EditGroup\" method=\"post\">";
                $line .= "<table class=\"zgrid\">";
                $line .= "<tr>";
                $line .= "<th>" . ui_language::translate("Group") . "</th>";
                $line .= "<th>" . ui_language::translate("Description") . "</th>";
                $line .= "<th></th>";
                $line .= "</tr>";
                $sql = $zdbh->prepare($sql);
                $sql->execute();
                while ($rowgroups = $sql->fetch()) {
                    $line .= "<tr>";
                    $line .= "<td>" . $rowgroups['ug_name_vc'] . "</td>";
                    $line .= "<td>" . $rowgroups['ug_notes_tx'] . "</td>";
                    $line .= "<td></td>";
                    $line .= "</tr>";
                }
                $line .= "</table>";
                $line . "</form>";
            } else {
                $line = ui_language::translate("No groups currently configured on your account.");
            }
        }
        return $line;
    }

}

?>