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
    static $ok;
    static $error_message;

    static function getAdminModules()
    {
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

    static function getConfigModules()
    {
        global $zdbh;
        global $controller;
        $line = "<h2>" . ui_language::translate("Configure Modules") . "</h2>";
        $modsql = "SELECT COUNT(*) FROM x_modules";
        if ($nummodsql = $zdbh->query($modsql)) {
            if ($nummodsql->fetchColumn() > 0) {
                $modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_folder_vc <> 'zpx_core_module' ORDER BY mo_name_vc ASC");
                $modsql->execute();
                $line .= "<form action=\"./?module=moduleadmin&action=EditModule\" method=\"post\">";
                $line .= "<table class=\"table table-striped\">";
                $line .= "<tr>";
                $line .= "<th></th>";
                $line .= "<th>" . ui_language::translate("Module") . "</th>";
                $line .= "<th>" . ui_language::translate("On") . "/" . ui_language::translate("Off") . "</th>";
                $line .= "<th>" . ui_language::translate("Category") . "</th>";
                $line .= "<th style=\"text-align:center\">" . ui_language::translate("Up-to-date?") . "</th>";
                $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                while ($groups = $groupssql->fetch()) {
                    $line .= "<th style=\"text-align:center\">" . $groups['ug_name_vc'] . "</th>";
                }

                $line .= "</tr>";
                $numline = 0;
                while ($modules = $modsql->fetch()) {
                    if ($numline == 20) {
                        $line .= "<tr>";
                        $line .= "<th></th>";
                        $line .= "<th>" . ui_language::translate("Module") . "</th>";
                        $line .= "<th>" . ui_language::translate("On") . "/" . ui_language::translate("Off") . "</th>";
                        $line .= "<th>" . ui_language::translate("Category") . "</th>";
                        $line .= "<th style=\"text-align:center\">" . ui_language::translate("Up-to-date?") . "</th>";
                        $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                        while ($groups = $groupssql->fetch()) {
                            $line .= "<th style=\"text-align:center\">" . $groups['ug_name_vc'] . "</th>";
                        }
                        $line .= "</tr>";
                    }
                    $ismodadmin = 0;
                    $line .= "<tr>";
                    $line .= "<td>" . self::ModuleStatusIcon($modules['mo_id_pk']) . "</td>";
                    $line .= "<td><a href=\"./?module=moduleadmin&showinfo=" . $modules['mo_folder_vc'] . "\">" . ui_language::translate($modules['mo_name_vc']) . "</a></td>";
                    $line .= "<td>";
                    if ($modules['mo_type_en'] != "system") {
                        $line .="<select style=\"min-width:85px;\" name=\"inDisable_" . $modules['mo_id_pk'] . "\" id=\"inDisable_" . $modules['mo_id_pk'] . "\">";
                    } else {
                        $line .="<select style=\"min-width:85px;\" name=\"inDisable_" . $modules['mo_id_pk'] . "\" id=\"inDisable_" . $modules['mo_id_pk'] . "\" disabled=\"disabled\">";
                    }
                    if ($modules['mo_enabled_en'] == 'true') {
                        $selected = "SELECTED";
                    } else {
                        $selected = "";
                    }
                    if ($modules['mo_name_vc'] == 'Module Admin') {
                        $ismodadmin = 1;
                        $disabled = "disabled=\"disabled\"";
                    } else {
                        $disabled = "";
                    }

                    $line .= "<option value=\"true\" " . $selected . " >" . ui_language::translate("Enabled") . "</option>";
                    if ($modules['mo_enabled_en'] == 'false') {
                        $selected = "SELECTED";
                    } else {
                        $selected = "";
                    }
                    $line .= "<option value=\"false\" " . $selected . " " . $disabled . ">" . ui_language::translate("Disabled") . "</option>";
                    $line .= "</select></td>";
                    $line .= "<td>";
                    if ($modules['mo_type_en'] == "user") {
                        $line .= "<select style=\"min-width:175px;\" name=\"inCategory_" . $modules['mo_id_pk'] . "\" id=\"inCategory_" . $modules['mo_id_pk'] . "\">";
                        $catssql = $zdbh->query("SELECT * FROM x_modcats ORDER BY mc_name_vc ASC");
                        while ($modulecats = $catssql->fetch()) {
                            $selected = "";
                            if ($modules['mo_category_fk'] == $modulecats['mc_id_pk']) {
                                $selected = "selected";
                            }
                            $line .= "<option value=\"" . $modulecats['mc_id_pk'] . "\" " . $selected . ">" . $modulecats['mc_name_vc'] . "</option>";
                        }
                        $line .= "</select>";
                    } elseif ($modules['mo_type_en'] == "modadmin") {
                        $line .="" . ui_language::translate("N/A (Module Admin)") . "";
                    } else {
                        $line .="" . ui_language::translate("N/A (System Module)") . "";
                    }
                    $line .= "</td><td style=\"text-align:center\">";
                    if (ui_module::GetModuleHasUpdates($modules['mo_folder_vc'])) {
                        $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\"><br>" . ui_language::translate("Latest version") . ": <a href=\"" . $modules['mo_updateurl_tx'] . "\" target=\"_blank\">" . $modules['mo_updatever_vc'] . "</a>";
                    } else {
                        $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
                    }
                    /**
                     * --
                     */
                    $line .= "</td>";
                    $groupssql = $zdbh->query("SELECT * FROM x_groups ORDER BY ug_name_vc ASC");
                    while ($groups = $groupssql->fetch()) {
                        if ($groups['ug_name_vc'] == 'Administrators' && $ismodadmin == 1) {
                            $line .= "<input type=\"hidden\" value=\"1\" name=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" id=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\"/>";
                            $disabled = "disabled=\"disabled\"";
                        } else {
                            $disabled = "";
                        }
                        $ischeck = 0;
                        if (ctrl_groups::CheckGroupModulePermissions($groups['ug_id_pk'], $modules['mo_id_pk']))
                            $ischeck = "checked=\"checked\" ";
                        if ($modules['mo_type_en'] != "system") {
                            $line .= "<td style=\"text-align:center\"><input type=\"checkbox\" value=\"1\" name=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" id=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" " . $ischeck . " " . $disabled . "/></td>";
                        } else {
                            $disabled = "disabled=\"disabled\"";
                            $line .= "<td style=\"text-align:center\"><input type=\"checkbox\" value=\"1\" name=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" id=\"inEnable_" . $groups['ug_id_pk'] . "_" . $modules['mo_id_pk'] . "\" " . $ischeck . " " . $disabled . " /></td>";
                        }
                    }
                    $line .= "</tr>";
                    $numline++;
                    if ($numline == 21) {
                        $numline = 0;
                    }
                }
                $line .= "</table><br>";
                $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\" name=\"inSave\" value=\"inSave\">" . ui_language::translate("Save changes") . "</button></form>";
            } else {
                $line .= ui_language::translate("You have no administration modules at this time.");
            }
        }
        return $line;
    }

    static function ModuleStatusIcon($mo_id_pk)
    {
        global $zdbh;
        global $controller;
        $modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_id_pk = :mo_id_pk");
        $modsql->bindParam(':mo_id_pk', $mo_id_pk);
        $modsql->execute();
        $modulestatus = $modsql->fetch();
        if ($modulestatus['mo_enabled_en'] == 'false') {
            $retval = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $retval = "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }
        return $retval;
    }

    static function doEditModule()
    {
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
                    $sql2 = $zdbh->prepare("UPDATE x_modules SET mo_enabled_en = :enabled, mo_category_fk = :category WHERE mo_id_pk = :moduleid");
                    $disable = $controller->GetControllerRequest('FORM', 'inDisable_' . $rowmodule['mo_id_pk'] . '');
                    $category = $controller->GetControllerRequest('FORM', 'inCategory_' . $rowmodule['mo_id_pk'] . '');
                    $sql2->bindParam(':enabled', $disable);
                    $sql2->bindParam(':category', $category);
                    $sql2->bindParam(':moduleid', $rowmodule['mo_id_pk']);
                    $sql2->execute();
                }
                self::$ok = TRUE;
                return;
            }
        }
        self::$error = TRUE;
        return;
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$error_message))
            return ui_sysmessage::shout(ui_language::translate(self::$error_message), 'zannounceerror', 'zannounce');
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your module options have been saved successfully!"));
        } else {
            return ui_language::translate(ui_module::GetModuleDescription());
        }
        return;
    }

    static function getIsShowModuleInfo()
    {
        global $controller;
        if (isset($_GET['showinfo']) && $controller->GetControllerRequest('URL', 'showinfo'))
            return true;
        return false;
    }

    static function getModuleInfoName()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['name'];
        } else {
            return null;
        }
    }

    static function getModuleDescription()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['desc'];
        } else {
            return null;
        }
    }

    static function getModuleDeveloperName()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['authorname'];
        } else {
            return null;
        }
    }

    static function getModuleDeveloperEmail()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['authoremail'];
        } else {
            return null;
        }
    }

    static function getModuleVersion()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['version'];
        } else {
            return null;
        }
    }

    static function getModuleDeveloperURL()
    {
        global $controller;
        if (self::getIsShowModuleInfo()) {
            $info = ui_module::GetModuleXMLTags($controller->GetControllerRequest('URL', 'showinfo'));
            return $info['authorurl'];
        } else {
            return null;
        }
    }

    static function getModuleUpdateURL()
    {
        global $controller;
        global $zdbh;
        //$retval = $zdbh->query("SELECT mo_updateurl_tx FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'showinfo') . "'")->Fetch();
        $numrows = $zdbh->prepare("SELECT mo_updateurl_tx FROM x_modules WHERE mo_folder_vc = :info");
        $info = $controller->GetControllerRequest('URL', 'showinfo');
        $numrows->bindParam(':info', $info);
        $numrows->execute();
        $retval = $numrows->fetch();
        $retval = $retval['mo_updateurl_tx'];
        return $retval;
    }

    static function getLatestVersion()
    {
        global $controller;
        global $zdbh;
        //$retval = $zdbh->query("SELECT mo_updatever_vc FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'showinfo') . "'")->Fetch();
        $numrows = $zdbh->prepare("SELECT mo_updatever_vc FROM x_modules WHERE mo_folder_vc = :info");
        $info = $controller->GetControllerRequest('URL', 'showinfo');
        $numrows->bindParam(':info', $info);
        $numrows->execute();
        $retval = $numrows->fetch();
        $retval = $retval['mo_updatever_vc'];
        return $retval;
    }

    static function getModuleType()
    {
        global $controller;
        global $zdbh;
        //$retval = $zdbh->query("SELECT mo_type_en FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'showinfo') . "'")->Fetch();
        $numrows = $zdbh->prepare("SELECT mo_type_en FROM x_modules WHERE mo_folder_vc = :info");
        $info = $controller->GetControllerRequest('URL', 'showinfo');
        $numrows->bindParam(':info', $info);
        $numrows->execute();
        $retval = $numrows->fetch();
        $retval = $retval['mo_type_en'];
        return $retval;
    }

    static function doInstallModule()
    {
        self::$error_message = "";
        self::$error = false;
        if ($_FILES['modulefile']['error'] > 0) {
            self::$error_message = "Couldn't upload the file, " . $_FILES['modulefile']['error'] . "";
        } else {
            $archive_ext = fs_director::GetFileExtension($_FILES['modulefile']['name']);
            $module_folder = fs_director::GetFileNameNoExtentsion($_FILES['modulefile']['name']);
            $module_dir = ctrl_options::GetSystemOption('sentora_root') . 'modules/' . $module_folder;
            if (!fs_director::CheckFolderExists($module_dir)) {
                if ($archive_ext != 'zpp') {
                    self::$error_message = "Package type was not detected as a .zpp (Sentora Package) archive.";
                } else {
                    if (fs_director::CreateDirectory($module_dir)) {
                        if (sys_archive::Unzip($_FILES['modulefile']['tmp_name'], $module_dir . '/')) {
                            if (!fs_director::CheckFileExists($module_dir . '/module.xml')) {
                                self::$error_message = "No module.xml file found in the unzipped archive.";
                            } else {
                                ui_module::ModuleInfoToDB($module_folder);
                                $extra_config = $module_dir . "/deploy/install.run";
                                if (fs_director::CheckFileExists($extra_config))
                                    exec(ctrl_options::GetSystemOption('php_exer') . " " . $extra_config . "");
                                self::$ok = true;
                            }
                        } else {
                            self::$error_message = "Couldn't unzip the archive (" . $_FILES['modulefile']['tmp_name'] . ") to " . $module_dir . '/';
                        }
                    } else {
                        self::$error_message = "Couldn't create module folder in " . $module_dir;
                    }
                }
            } else {
                self::$error_message = "The module " . $module_folder . " is already installed on this server!";
            }
        }
        return;
    }

}
