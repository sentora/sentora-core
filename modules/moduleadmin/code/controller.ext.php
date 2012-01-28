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
    static $ok;

    static function getAdminModules() {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Administration Modules") . "</h2>";
        $modsql = "SELECT COUNT(*) FROM x_modules WHERE mo_type_en = 'modadmin' AND mo_enabled_en = 'true' ORDER BY mo_name_vc ASC";
        if ($nummodsql = $zdbh->query($modsql)) {
            if ($nummodsql->fetchColumn() > 0) {
                $modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_type_en = 'modadmin' AND mo_enabled_en = 'true' ORDER BY mo_name_vc ASC");
                $modsql->execute();
                $line .="<table>";
                while ($modules = $modsql->fetch()) {
                    $translatename = ui_language::translate($modules['mo_name_vc']);
                    $line .="<tr><td>";
                    $line .= "<a href=\"./?module=" . $modules['mo_folder_vc'] . "\">" . $translatename . "</a>";
                    $line .="</td></tr>";
                }
                $line .="</table>";
            } else {
                $line .= ui_language::translate("You have no administration modules at this time.");
            }
        }
        return $line;
    }

    static function getConfigModules() {
        global $zdbh;
        global $controller;
        $line = "<h2>" . ui_language::translate("Configure Modules") . "</h2>";
        $modsql = "SELECT COUNT(*) FROM x_modules WHERE mo_type_en = 'user'";
        if ($nummodsql = $zdbh->query($modsql)) {
            if ($nummodsql->fetchColumn() > 0) {
                $modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_type_en <> 'system' ORDER BY mo_name_vc ASC");
                $modsql->execute();
                $line .= "<form action=\"./?module=moduleadmin&action=EditModule\" method=\"post\">";
                $line .= "<table class=\"zgrid\">";
                $line .= "<tr>";
                $line .= "<th></th>";
                $line .= "<th>" . ui_language::translate("Module") . "</th>";
                $line .= "<th>" . ui_language::translate("On") . "/" . ui_language::translate("Off") . "</th>";
                $line .= "<th>" . ui_language::translate("Category") . "</th>";
                $line .= "<th>" . ui_language::translate("Up-to-date?") . "</th>";
                $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                while ($groups = $groupssql->fetch()) {
                    $line .= "<th>" . $groups['ug_name_vc'] . "</th>";
                }

                $line .= "</tr>";
                while ($modules = $modsql->fetch()) {
                    $line .= "<tr>";
                    $line .= "<td>" . self::ModuleStatusIcon($modules['mo_id_pk']) . "</td>";
                    $line .= "<td><a href=\"./?module=moduleadmin&showinfo=" . $modules['mo_folder_vc'] . "\">" . ui_language::translate($modules['mo_name_vc']) . "</a></td>";
                    $line .= "<td><select name=\"inDisable_" . $modules['mo_id_pk'] . "\" id=\"inDisable_" . $modules['mo_id_pk'] . "\">";
                    if ($modules['mo_enabled_en'] == 'true') {
                        $selected = "SELECTED";
                    } else {
                        $selected = "";
                    }
                    $line .= "<option value=\"true\" " . $selected . ">" . ui_language::translate("Enabled") . "</option>";
                    if ($modules['mo_enabled_en'] == 'false') {
                        $selected = "SELECTED";
                    } else {
                        $selected = "";
                    }
                    $line .= "<option value=\"false\" " . $selected . ">" . ui_language::translate("Disabled") . "</option>";
                    $line .= "</select></td>";
                    $line .= "<td>";
                    if ($modules['mo_type_en'] == "user") {
                        $line .= "<select name=\"inCategory_" . $modules['mo_id_pk'] . "\" id=\"inCategory_" . $modules['mo_id_pk'] . "\">";
                        $catssql = $zdbh->query("SELECT * FROM x_modcats ORDER BY mc_name_vc ASC");
                        while ($modulecats = $catssql->fetch()) {
                            $selected = "";
                            if ($modules['mo_category_fk'] == $modulecats['mc_id_pk']) {
                                $selected = "selected";
                            }
                            $line .= "<option value=\"" . $modulecats['mc_id_pk'] . "\" " . $selected . ">" . $modulecats['mc_name_vc'] . "</option>";
                        }
                        $line .= "</select>";
                    } else {
                        $line .="" . ui_language::translate("N/A (System module)") . "";
                    }
                    $line .= "</td><td>";
                    if (ui_module::GetModuleHasUpdates($modules['mo_folder_vc'])) {
                        $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\"> - " .ui_language::translate("Latest version").": <a href=\"".$modules['mo_updateurl_tx']."\" target=\"_blank\">" .$modules['mo_updatever_vc']. "</a>";
                    } else {
                        $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
                    }
                    /**
                     * --
                     */
                    $line .= "</td>";
                    $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                    while ($groups = $groupssql->fetch()) {
                        $ischeck = 0;
                        if (ctrl_groups::CheckGroupModulePermissions($groups['ug_id_pk'], $modules['mo_id_pk']))
                            $ischeck = "checked=\"checked\" ";
                        $line .= "<td><input type=\"checkbox\" value=\"1\" name=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" id=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" " . $ischeck . "/></td>";
                    }
                    $line .= "</tr>";
                }
                $line .= "</table>";
                $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSave\" value=\"inSave\">" . ui_language::translate("Save changes") . "</button></form>";
            } else {
                $line .= ui_language::translate("You have no administration modules at this time.");
            }
        }
        return $line;
    }

    static

    function ModuleStatusIcon($mo_id_pk) {
        global $zdbh;
        global $controller;
        $modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_id_pk = '" . $mo_id_pk . "'");
        $modsql->execute();
        $modulestatus = $modsql->fetch();
        if ($modulestatus['mo_enabled_en'] == 'false') {
            $retval = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $retval = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }
        return $retval;
    }

    static

    function doEditModule() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT COUNT(*) FROM x_modules";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_type_en <> 'system' ORDER BY mo_name_vc ASC");
                $sql->execute();
                while ($rowmodule = $sql->fetch()) {
                    $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                    while ($groups = $groupssql->fetch()) {
                        if (isset($_POST['inEnable_' . $groups['ug_id_pk'] . '_' . $rowmodule['mo_id_pk'] . ''])) {
                            ctrl_groups::AddGroupModulePermissions($groups['ug_id_pk'], $rowmodule['mo_id_pk']);
                        } else {
                            ctrl_groups::DeleteGroupModulePermissions($groups['ug_id_pk'], $rowmodule['mo_id_pk']);
                        }
                    }
                    $sql2 = $zdbh->prepare("UPDATE x_modules SET mo_enabled_en = '" . $controller->GetControllerRequest('FORM', 'inDisable_' . $rowmodule['mo_id_pk'] . '') . "', mo_category_fk = '" . $controller->GetControllerRequest('FORM', 'inCategory_' . $rowmodule['mo_id_pk'] . '') . "' WHERE mo_id_pk = " . $rowmodule['mo_id_pk'] . "");
                    $sql2->execute();
                }
                self::$ok = TRUE;
                return;
            }
        }
        self::$error = TRUE;
        return;
    }

    static

    function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your module options have been saved successfully!"));
        } else {
            return ui_language::translate(ui_module::GetModuleDescription());
        }
        return;
    }

    static

    function getModuleName() {
        $module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

    static

    function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

}

?>
