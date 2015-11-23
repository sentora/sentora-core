<?php

/**
 *
 * Sentora - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (Sentora) is free software: you can redistribute it and/or modify
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

    static function getConfig()
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true' ORDER BY so_cleanname_vc";
        $module = ui_module::GetModuleName();
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':module', $module);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':module', $module);
            $res = array();
            $sql->execute();
            while ($rowsettings = $sql->fetch()) {
                if (ctrl_options::CheckForPredefinedOptions($rowsettings['so_defvalues_tx'])) {
                    $fieldhtml = ctrl_options::OuputSettingMenuField($rowsettings['so_name_vc'], $rowsettings['so_defvalues_tx'], $rowsettings['so_value_tx']);
                } else {
                    $fieldhtml = ctrl_options::OutputSettingTextArea($rowsettings['so_name_vc'], $rowsettings['so_value_tx']);
                }
                if (strpos(ctrl_options::OutputSettingTextArea($rowsettings['so_name_vc']),'smtp_password') !== false) {
                    $fieldhtml = '<input type="password" name="smtp_password" value="'.$rowsettings['so_value_tx'].'">';
                }
                array_push($res, array('cleanname' => ui_language::translate($rowsettings['so_cleanname_vc']),
                    'name' => $rowsettings['so_name_vc'],
                    'description' => ui_language::translate($rowsettings['so_desc_tx']),
                    'value' => $rowsettings['so_value_tx'],
                    'fieldhtml' => $fieldhtml));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function getLastRunTime()
    {
        $time = ctrl_options::GetSystemOption('daemon_lastrun');
        if ($time != '0') {
            return date(ctrl_options::GetSystemOption('sentora_df'), $time);
        } else {
            return false;
        }
    }

    static function getNextRunTime()
    {
        if (ctrl_options::GetSystemOption('daemon_lastrun') > 0) {
            $new_time = ctrl_options::GetSystemOption('daemon_lastrun') + ctrl_options::GetSystemOption('daemon_run_interval');
            return date(ctrl_options::GetSystemOption('sentora_df'), $new_time);
        } else {
            // The default cron is set to run every 5 minutes on the 5 minute mark!
            return date(ctrl_options::GetSystemOption('sentora_df'), ceil(time() / 300) * 300);
        }
    }

    static function getLastDayRunTime()
    {
        $time = ctrl_options::GetSystemOption('daemon_dayrun');
        if ($time != '0') {
            return date(ctrl_options::GetSystemOption('sentora_df'), $time);
        } else {
            return false;
        }
    }

    static function getLastWeekRunTime()
    {
        $time = ctrl_options::GetSystemOption('daemon_weekrun');
        if ($time != '0') {
            return date(ctrl_options::GetSystemOption('sentora_df'), $time);
        } else {
            return false;
        }
    }

    static function getLastMonthRunTime()
    {
        $time = ctrl_options::GetSystemOption('daemon_monthrun');
        if ($time != '0') {
            return date(ctrl_options::GetSystemOption('sentora_df'), $time);
        } else {
            return false;
        }
    }

    static function doUpdateConfig()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $sql = "SELECT * FROM x_settings WHERE so_module_vc=:module AND so_usereditable_en = 'true'";
        $module = ui_module::GetModuleName();
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':module', $module);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':module', $module);
            $sql->execute();
            while ($row = $sql->fetch()) {
                if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['so_name_vc']))) {
                    $updatesql = $zdbh->prepare("UPDATE x_settings SET so_value_tx = :value WHERE so_name_vc = :so_name_vc");
                    $value = $controller->GetControllerRequest('FORM', $row['so_name_vc']);
                    $updatesql->bindParam(':value', $value);
                    $updatesql->bindParam(':so_name_vc', $row['so_name_vc']);
                    $updatesql->execute();
                }
            }
            self::SetWriteApacheConfigTrue(); #sentora apache port changed require rewrite of vhosts
        }
        self::$ok = true;
    }

    static function SetWriteApacheConfigTrue()
    {
        global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx='true' WHERE so_name_vc='apache_changed'");
        $sql->execute();
    }

    static function doForceDaemon()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inForceFull'])) {
            $sql = $zdbh->prepare("UPDATE x_settings set so_value_tx = '0' WHERE so_name_vc = 'daemon_lastrun'");
            $sql->execute();
            $sql = $zdbh->prepare("UPDATE x_settings set so_value_tx = '0' WHERE so_name_vc = 'daemon_dayrun'");
            $sql->execute();
            $sql = $zdbh->prepare("UPDATE x_settings set so_value_tx = '0' WHERE so_name_vc = 'daemon_weekrun'");
            $sql->execute();
            $sql = $zdbh->prepare("UPDATE x_settings set so_value_tx = '0' WHERE so_name_vc = 'daemon_monthrun'");
            $sql->execute();
        }
        self::$ok = true;
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your settings have been saved successfully!"));
        }
        return;
    }

}