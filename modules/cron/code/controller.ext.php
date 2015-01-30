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
 * Change P.Peyremorte:
 * - cleaned WriteCronFile() (removed duplicate parts).
 * - reformated header inserted in crontab file (heading spaces and wrong EOL encoding)
 * - removed daemon task that is handled by independant crontab /etc/cron.d/zdaemon (linux)
 */
class module_controller extends ctrl_module
{

    static $error;
    static $noexists;
    static $cronnoexists;
    static $cronnowrite;
    static $alreadyexists;
    static $blank;
    static $ok;

    static function getCrons()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "<h2>" . ui_language::translate("Current Cron Tasks") . "</h2>";
        $sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);

        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_deleted_ts IS NULL");
                $sql->bindParam(':userid', $currentuser['userid']);
                $sql->execute();
                $line .= "<form action=\"./?module=cron&action=DeleteCron\" method=\"post\">";
                $line .= "<table class=\"table table-striped\">";
                $line .= "<tr>";
                $line .= "<th>" . ui_language::translate("Script") . "</th>";
                $line .= "<th>" . ui_language::translate("Timing") . "</th>";
                $line .= "<th>" . ui_language::translate("Description") . "</th>";
                $line .= "<th></th>";
                $line .= "</tr>";
                while ($rowcrons = $sql->fetch()) {
                    $line .= "<tr>";
                    $line .= "<td>" . $rowcrons['ct_script_vc'] . "</td>";
                    $line .= "<td>" . ui_language::translate(self::TranslateTiming($rowcrons['ct_timing_vc'])) . "</td>";
                    $line .= "<td>" . $rowcrons['ct_description_tx'] . "</td>";
                    $line .= "<td><button class=\"button-loader delete btn btn-danger\" type=\"submit\" name=\"inDelete_" . $rowcrons['ct_id_pk'] . "\" id=\"button\" value=\"inDelete_" . $rowcrons['ct_id_pk'] . "\">" . ui_language::translate("Delete") . "</button></td>";
                    $line .= "</tr>";
                }
                $line .= "</table>";
                $line .= runtime_csfr::Token();
                $line .= "</form>";
            } else {
                $line .= ui_language::translate("You currently do not have any tasks setup.");
            }
            return $line;
        }
    }

    static function getCreateCron()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();

        $line = "<h2>Create a new task</h2>";
        $line .= "<form action=\"./?module=cron&action=CreateCron\" method=\"post\">";
        $line .= "<table class=\"table table-striped\">";
        $line .= "<tr valign=\"top\">";
        $line .= "<th>" . ui_language::translate("Script") . ":</th>";
        $line .= '<td><input name="inScript" type="text" id="inScript" size="50" /><br />'
                . ui_language::translate("example") . ': /folder/task.php<br>'
                . ui_language::translate('Note 1 : Script path is relative to your sentora-user root directory:') . '<br>'
                . ' &nbsp; <b>' . ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . '/public_html/</b><br>'
                . ui_language::translate('Note 2 : Each file access in your script must use absolute directory path as above.')
                . '</td>';
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>" . ui_language::translate("Comment") . ":</th>";
        $line .= "<td><input name=\"inDescription\" type=\"text\" id=\"inDescription\" size=\"50\" maxlength=\"50\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>" . ui_language::translate("Executed") . ":</th>";
        $line .= "<td><select name=\"inTiming\" id=\"inTiming\">";
        $line .= "<option value=\"* * * * *\">" . ui_language::translate("Every 1 minute") . "</option>";
        $line .= "<option value=\"0,5,10,15,20,25,30,35,40,45,50,55 * * * *\">" . ui_language::translate("Every 5 minutes") . "</option>";
        $line .= "<option value=\"0,10,20,30,40,50 * * * *\">" . ui_language::translate("Every 10 minutes") . "</option>";
        $line .= "<option value=\"0,30 * * * *\">" . ui_language::translate("Every 30 minutes") . "</option>";
        $line .= "<option value=\"0 * * * *\">" . ui_language::translate("Every 1 hour") . "</option>";
        $line .= "<option value=\"0 0,2,4,6,8,10,12,14,16,18,20,22 * * *\">" . ui_language::translate("Every 2 hours") . "</option>";
        $line .= "<option value=\"0 0,8,16 * * *\">" . ui_language::translate("Every 8 hours") . "</option>";
        $line .= "<option value=\"0 0,12 * * *\">" . ui_language::translate("Every 12 hours") . "</option>";
        $line .= "<option value=\"0 0 * * *\">" . ui_language::translate("Every 1 day") . "</option>";
        $line .= "<option value=\"0 0 * * 0\">" . ui_language::translate("Every week") . "</option>";
        $line .="<option value=\"0 0 1 * *\">" . ui_language::translate("Every month") . "</option>";
        $line .= "</select></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"inReturn\" value=\"GetFullURL\" />";
        $line .= "<input type=\"hidden\" name=\"inUserID\" value=\"" . $currentuser['userid'] . "\" />";
        $line .= runtime_csfr::Token();
        $line .= "<button class=\"button-loader btn btn-primary\" type=\"submit\" id=\"button\">" . ui_language::translate("Create") . "</button></th>";
        $line .= "</tr>";
        $line .= "</table>";
        $line .= "</form>";

        return $line;
    }

    static function doCreateCron()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        if (fs_director::CheckForEmptyValue(self::CheckCronForErrors())) {
            // If the user submitted a 'new' request then we will simply add the cron task to the database...
            $sql = $zdbh->prepare("INSERT INTO x_cronjobs (ct_acc_fk, ct_script_vc, ct_description_tx, ct_timing_vc, ct_fullpath_vc, ct_created_ts) VALUES (:userid, :script, :desc, :timing, :fullpath, " . time() . ")");
            $sql->bindParam(':userid', $controller->GetControllerRequest('FORM', 'inUserID'));
            $sql->bindParam(':script', $controller->GetControllerRequest('FORM', 'inScript'));
            $sql->bindParam(':desc', $controller->GetControllerRequest('FORM', 'inDescription'));
            $sql->bindParam(':timing', $controller->GetControllerRequest('FORM', 'inTiming'));
            $full_path = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/public_html/" . $controller->GetControllerRequest('FORM', 'inScript');
            $sql->bindParam(':fullpath', $full_path);
            $sql->execute();
            self::WriteCronFile();
            self::$ok = TRUE;
            return;
        }
        self::$error = TRUE;
        return;
    }

    static function doDeleteCron()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_deleted_ts IS NULL");
                $sql->bindParam(':userid', $currentuser['userid']);
                $sql->execute();
                while ($rowcrons = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $rowcrons['ct_id_pk'] . ''))) {
                        $sql2 = $zdbh->prepare("UPDATE x_cronjobs SET ct_deleted_ts=:time WHERE ct_id_pk=:cronid");
                        $sql2->bindParam(':cronid', $rowcrons['ct_id_pk']);
                        $sql2->bindParam(':time', time());
                        $sql2->execute();
                        self::WriteCronFile();
                        self::$ok = TRUE;
                        return;
                    }
                }
            }
        }
        self::$error = TRUE;
        return;
    }

    static function CheckCronForErrors()
    {
        global $zdbh;
        global $controller;
        $retval = FALSE;
        //Try to create the cron file if it doesnt exist...
        if (!file_exists(ctrl_options::GetSystemOption('cron_file'))) {
            fs_filehandler::UpdateFile(ctrl_options::GetSystemOption('cron_file'), 0644, "");
        }
        $currentuser = ctrl_users::GetUserDetail();
        // Check to make sure the cron is not blank before we go any further...
        if ($controller->GetControllerRequest('FORM', 'inScript') == '') {
            self::$blank = TRUE;
            $retval = TRUE;
        }
        // Check to make sure the cron script exists before we go any further...
        if (!is_file(fs_director::RemoveDoubleSlash(fs_director::ConvertSlashes(ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . '/public_html/' . $controller->GetControllerRequest('FORM', 'inScript'))))) {
            self::$noexists = TRUE;
            $retval = TRUE;
        }
        // Check to see if creating system cron file was successful...
        if (!is_file(ctrl_options::GetSystemOption('cron_file'))) {
            self::$cronnoexists = TRUE;
            $retval = TRUE;
        }
        // Check to makesystem cron file is writable...
        if (!is_writable(ctrl_options::GetSystemOption('cron_file'))) {
            self::$cronnowrite = TRUE;
            $retval = TRUE;
        }
        // Check to make sure the cron is not a duplicate...
        $sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_script_vc=:inScript AND ct_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->bindParam(':inScript', $controller->GetControllerRequest('FORM', 'inScript'));
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                self::$alreadyexists = TRUE;
                $retval = TRUE;
            }
        }
        return $retval;
    }

    static function WriteCronFile()
    {
        global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $sql = "SELECT * FROM x_cronjobs WHERE ct_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);

        //common header whatever there are some cron task or not
        if (sys_versions::ShowOSPlatformVersion() != "Windows") {
            $line .= 'SHELL=/bin/bash' . fs_filehandler::NewLine();
            $line .= 'PATH=/sbin:/bin:/usr/sbin:/usr/bin' . fs_filehandler::NewLine();
            $line .= 'HOME=/' . fs_filehandler::NewLine();
            $line .= fs_filehandler::NewLine();
        }
        $restrictinfos = ctrl_options::GetSystemOption('php_exer') . " -d suhosin.executor.func.blacklist=\"passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg, exec\" -d open_basedir=\"" . ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . "/" . ctrl_options::GetSystemOption('openbase_seperator') . ctrl_options::GetSystemOption('openbase_temp') . "\" ";

        $line .= "#################################################################################" . fs_filehandler::NewLine();
        $line .= "# CRONTAB FOR SENTORA CRON MANAGER MODULE                                        " . fs_filehandler::NewLine();
        $line .= "# Module Developed by Bobby Allen, 17/12/2009                                    " . fs_filehandler::NewLine();
        $line .= "# File automatically generated by Sentora " . sys_versions::ShowSentoraVersion() . fs_filehandler::NewLine();
        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
            $line .= "# Cron Debug infomation can be found in file C:\WINDOWS\System32\crontab.txt " . fs_filehandler::NewLine();
            $line .= "#################################################################################" . fs_filehandler::NewLine();
            $line .= "" . ctrl_options::GetSystemOption('daemon_timing') . " " . $restrictinfos . ctrl_options::GetSystemOption('daemon_exer') . fs_filehandler::NewLine();
        }
        $line .= "#################################################################################" . fs_filehandler::NewLine();
        $line .= "# NEVER MANUALLY REMOVE OR EDIT ANY OF THE CRON ENTRIES FROM THIS FILE,          " . fs_filehandler::NewLine();
        $line .= "#  -> USE SENTORA INSTEAD! (Menu -> Advanced -> Cron Manager)                    " . fs_filehandler::NewLine();
        $line .= "#################################################################################" . fs_filehandler::NewLine();

        //Write command lines in crontab, if any
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
            while ($rowcron = $sql->fetch()) {
                $fetchRows = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_id_pk=:userid AND ac_deleted_ts IS NULL");
                $fetchRows->bindParam(':userid', $rowcron['ct_acc_fk']);
                $fetchRows->execute();
                $rowclient = $fetchRows->fetch();
                if ($rowclient && $rowclient['ac_enabled_in'] <> 0) {
                    $line .= "# CRON ID: " . $rowcron['ct_id_pk'] . fs_filehandler::NewLine();
                    $line .= $rowcron['ct_timing_vc'] . " " . $restrictinfos . $rowcron['ct_fullpath_vc'] . fs_filehandler::NewLine();
                    $line .= "# END CRON ID: " . $rowcron['ct_id_pk'] . fs_filehandler::NewLine();
                }
            }
        }
        if (fs_filehandler::UpdateFile(ctrl_options::GetSystemOption('cron_file'), 0644, $line)) {
            if (sys_versions::ShowOSPlatformVersion() != "Windows") {
                $returnValue = ctrl_system::systemCommand(
                                   ctrl_options::GetSystemOption('zsudo'), array(
                                      ctrl_options::GetSystemOption('cron_reload_command'),
                                      ctrl_options::GetSystemOption('cron_reload_flag'),
                                      ctrl_options::GetSystemOption('cron_reload_user'),
                                      ctrl_options::GetSystemOption('cron_reload_path'),
                                   )
                               );
            }
            return true;
        } else {
            return false;
        }
    }

    static function TranslateTiming($timing)
    {
        $timing = trim($timing);
        $retval = NULL;
        if ($timing == "* * * * *") {
            $retval = "Every 1 minute";
        }
        if ($timing == "0,5,10,15,20,25,30,35,40,45,50,55 * * * *") {
            $retval = "Every 5 minutes";
        }
        if ($timing == "0,10,20,30,40,50 * * * *") {
            $retval = "Every 10 minutes";
        }
        if ($timing == "0,30 * * * *") {
            $retval = "Every 30 minutes";
        }
        if ($timing == "0 * * * *") {
            $retval = "Every 1 hour";
        }
        if ($timing == "0 0,2,4,6,8,10,12,14,16,18,20,22 * * *") {
            $retval = "Every 2 hours";
        }
        if ($timing == "0 0,8,16 * * *") {
            $retval = "Every 8 hours";
        }
        if ($timing == "0 0,12 * * *") {
            $retval = "Every 12 hours";
        }
        if ($timing == "0 0 * * *") {
            $retval = "Every day";
        }
        if ($timing == "0 0 * * 0") {
            $retval = "Every week";
        }
        if ($timing == "0 0 1 * *") {
            $retval = "Every month";
        }
        return $retval;
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> You need to specify a valid location for your script."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$noexists)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> Your script does not appear to exist at that location."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$cronnoexists)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> System Cron file could not be created."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$cronnowrite)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> Could not write to the System Cron file."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> You can not add the same cron task more than once."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$error)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Error:</strong> There was an error updating the cron job."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("<strong>Success:</strong> Cron updated successfully."), "zannounceok");
        }
        return;
    }

}
