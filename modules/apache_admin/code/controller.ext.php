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
    static $showvhost;

    static function getApacheConfig() {
        if (!fs_director::CheckForEmptyValue(self::$showvhost)) {
            $display = self::DisplayApacheVhost();
        } else {
            $display = self::DisplayApacheConfig();
        }
        return $display;
    }

    static function getVhostConfig() {
        $display = self::DisplayVhostConfig();
        return $display;
    }

    static function DisplayApacheConfig() {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Configure your Apache Settings") . "</h2>";
        $line .= "<form action=\"./?module=apache_admin&action=UpdateApacheConfig\" method=\"post\">";
        $line .= "<table class=\"zgrid\">";
        $count = 0;
        $sql = "SELECT COUNT(*) FROM x_vhosts_settings WHERE vhs_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_vhosts_settings WHERE vhs_usereditable_en = 'true'");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $count++;
                    $line .= "<tr valign=\"top\"><th nowrap=\"nowrap\">" . $row['vhs_cleanname_vc'] . "</th><td><textarea cols=\"30\" rows=\"1\" name=\"" . $row['vhs_name_vc'] . "\">" . $row['vhs_value_tx'] . "</textarea></td><td>" . $row['vhs_desc_tx'] . "</td></tr>";
                }
                $line .= "<tr><th colspan=\"3\"><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSaveSystem\">Save Changes</button></th><td></td><td></td></tr>";
            }
        }
        $line .= "</table>";
        $line .= "</form>";
        return $line;
    }

    static function DisplayVhostConfig() {
        global $zdbh;
        $line = "<h2>" . ui_language::translate("Override a Virtual Host Setting") . "</h2>";
        $line .= ui_language::translate("Select a Virtual Host below.");
        $line .= "<br><br>";
        $line .= "<form action=\"./?module=apache_admin&action=DisplayVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr><td>";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSelectVhost\">" . ui_language::translate("Select Vhost") . "</button>";
        $line .= "</td><td>";
        $line .= "<select name=\"inVhost\" id=\"inVhost\">";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
                $sql->execute();

                while ($row = $sql->fetch()) {
                    $line .= "<option value=\"" . $row['vh_name_vc'] . "\">" . $row['vh_name_vc'] . "</option>";
                }
            }
        }
        $line .= "</select>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= "</form>";
        return $line;
    }

    static function DisplayApacheVhost() {
        global $zdbh;
        global $controller;
        $line = "<h2>" . ui_language::translate("Virtual Host Override") . "</h2>";
        $line .= ui_language::translate("Set options for virtual host") . ": <b>" . $controller->GetControllerRequest('FORM', 'inVhost') . "</b>";
        $line .= "<br><br>";
        $line .= "<form action=\"./?module=apache_admin&action=SaveVhost\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_name_vc='" . $controller->GetControllerRequest('FORM', 'inVhost') . "' AND vh_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {

                $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_name_vc='" . $controller->GetControllerRequest('FORM', 'inVhost') . "' AND vh_deleted_ts IS NULL");
                $sql->execute();
                $row = $sql->fetch();

                $line .= "<tr><th>" . ui_language::translate("Domain Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_enabled_in\" id=\"vh_enabled_in\" value=\"1\" " . fs_director::IsChecked($row['vh_enabled_in']) . "/></td></tr>";
                $line .= "<tr><th>" . ui_language::translate("Suhosin Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_suhosin_in\" id=\"vh_suhosin_in\" value=\"1\" " . fs_director::IsChecked($row['vh_suhosin_in']) . "/></td></tr>";
                $line .= "<tr><th>" . ui_language::translate("OpenBase Enabled") . ":</th><td><input type=\"checkbox\" name=\"vh_obasedir_in\" id=\"vh_obasedir_in\" value=\"1\" " . fs_director::IsChecked($row['vh_obasedir_in']) . "/></td></tr>";
                $line .= "<tr valign=\"top\"><th>" . ui_language::translate("Custom Entry") . ":</th><td><textarea cols=\"60\" rows=\"10\" name=\"vh_custom_tx\">" . $row['vh_custom_tx'] . "</textarea></td></tr>";
            }
        }
        $line .= "<tr><td>";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"vh_id_pk\" value=\"" . $row['vh_id_pk'] . "\">" . ui_language::translate("Save Vhost") . "</button>";
        $line .= "</td></tr>";
        $line .= "</table>";
        $line .= "</form>";
        return $line;
    }

    static function doDisplayVhost() {
        global $zdbh;
        global $controller;
        self::$showvhost = TRUE;
    }

    static function doUpdateApacheConfig() {
        global $zdbh;
        global $controller;
        $sql = "SELECT COUNT(*) FROM x_vhosts_settings WHERE vhs_usereditable_en = 'true'";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_vhosts_settings WHERE vhs_usereditable_en = 'true'");
                $sql->execute();
                while ($row = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', $row['vhs_name_vc']))) {
                        $updatesql = $zdbh->prepare("UPDATE x_vhosts_settings SET vhs_value_tx = '" . $controller->GetControllerRequest('FORM', $row['vhs_name_vc']) . "' WHERE vhs_name_vc = '" . $row['vhs_name_vc'] . "'");
                        $updatesql->execute();
                    }
                }
            }
        }
    }

    static function doSaveVhost() {
        global $zdbh;
        global $controller;
        $sql = $zdbh->prepare("UPDATE x_vhosts SET 
			vh_enabled_in  = " . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_enabled_in')) . ",
			vh_suhosin_in  = " . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_suhosin_in')) . ",
			vh_obasedir_in = " . fs_director::GetCheckboxValue($controller->GetControllerRequest('FORM', 'vh_obasedir_in')) . ",
			vh_custom_tx   = '" . $controller->GetControllerRequest('FORM', 'vh_custom_tx') . "'
			WHERE
			vh_id_pk = " . $controller->GetControllerRequest('FORM', 'vh_id_pk') . "
			AND vh_deleted_ts IS NULL");
        $sql->execute();
        if (self::CheckSyntax() == TRUE) {
            self::$ok = TRUE;
        }
    }

    static function CheckSyntax() {
        self::WriteTempVhostConfigFile();
        $vhconfigfile = self::GetVHOption('temp_dir') . "vhost.tmp";
        if (is_file($vhconfigfile)) {
            if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
                if (strstr(exec("apachectl -S"), "OK")) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {

                  //$service='C:/xampp/apache/bin/httpd -t';
                  
                $fp=popen("C:/xampp/apache/bin/httpd -t","r"); 
while (!feof($fp)) { 
    $buffer = fgets($fp, 4096); 
    $croninf .= '<tr><td>' . $buffer . '</td></tr>' . "\n"; 
} 
                  echo popen('C:/xampp/apache/bin/httpd -t', 'r');
                    $output = shell_exec($service);
                    echo "SSOUT = ".$output;
                  //print_r($ssout);
                  //foreach ($ssout as $outs => $done){
                  //echo $done.'<br>';
                  //}
                /**$cmd = "C:/xampp/apache/bin/httpd.exe";
                ob_start();
                system($cmd . ' -S',$retval);
                //print_r($retval);
                $retval = ob_get_contents();
                ob_clean();
                die($retval); */
                if (strstr($retval, "SyntaxK")) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

    static function WriteTempVhostConfigFile() {
        global $zdbh;
        global $controller;

        $line = "################################################################" . fs_filehandler::NewLine();
        $line .= "# Apache VHOST configuration file                               " . fs_filehandler::NewLine();
        $line .= "# Automatically generated by ZPanel " . sys_versions::ShowZpanelVersion() . "                           " . fs_filehandler::NewLine();
        $line .= "################################################################" . fs_filehandler::NewLine();
        $line .= "" . fs_filehandler::NewLine();

        // ZPanel default virtual host container
        $line .= "NameVirtualHost *:" . self::GetVHOption('apache_port') . "" . fs_filehandler::NewLine();
        $line .= "" . fs_filehandler::NewLine();
        $line .= "# Configuration for ZPanel control panel." . fs_filehandler::NewLine();
        $line .= "<VirtualHost localhost:" . self::GetVHOption('apache_port') . ">" . fs_filehandler::NewLine();
        $line .= "ServerAdmin zadmin@ztest.com" . fs_filehandler::NewLine();
        $line .= "DocumentRoot \"" . ctrl_options::GetOption('zpanel_root') . "\"" . fs_filehandler::NewLine();
        $line .= "ServerName " . ctrl_options::GetOption('zpanel_domain') . "" . fs_filehandler::NewLine();
        $line .= "ServerAlias *." . ctrl_options::GetOption('zpanel_domain') . "" . fs_filehandler::NewLine();
        $line .= "<Location /server-status>" . fs_filehandler::NewLine();
        $line .= "	SetHandler server-status" . fs_filehandler::NewLine();
        $line .= "	Order Deny,Allow" . fs_filehandler::NewLine();
        $line .= "	Allow from all" . fs_filehandler::NewLine();
        $line .= "</Location>" . fs_filehandler::NewLine();
        $line .= "AddType application/x-httpd-php .php" . fs_filehandler::NewLine();
        $line .= "<Directory \"" . ctrl_options::GetOption('zpanel_root') . "\">" . fs_filehandler::NewLine();
        $line .= "Options FollowSymLinks" . fs_filehandler::NewLine();
        $line .= "	AllowOverride All" . fs_filehandler::NewLine();
        $line .= "	Order allow,deny" . fs_filehandler::NewLine();
        $line .= "	Allow from all" . fs_filehandler::NewLine();
        $line .= "</Directory>" . fs_filehandler::NewLine();
        $line .= "" . fs_filehandler::NewLine();
        $line .= "# Custom settings are loaded below this line (if any exist)" . fs_filehandler::NewLine();

        // Global custom zpanel entry
        $line .= self::GetVHOption('global_zpcustom');

        $line .= "</VirtualHost>" . fs_filehandler::NewLine();

        $line .= "" . fs_filehandler::NewLine();
        $line .= "################################################################" . fs_filehandler::NewLine();
        $line .= "# ZPanel generated VHOST configurations below.....      " . fs_filehandler::NewLine();
        $line .= "################################################################" . fs_filehandler::NewLine();
        $line .= "" . fs_filehandler::NewLine();

        // Zpanel virtual host container configuration
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL");
        $sql->execute();
        while ($rowvhost = $sql->fetch()) {

            // Get account username vhost is create with
            $username = $zdbh->query("SELECT ac_user_vc FROM x_accounts where ac_id_pk=" . $rowvhost['vh_acc_fk'] . "")->fetch();

            $line .= "# DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
            $line .= "<virtualhost *:" . self::GetVHOption('apache_port') . ">" . fs_filehandler::NewLine();

            // Bandwidth Settings
            //$line .= "Include C:/ZPanel/bin/apache/conf/mod_bw/mod_bw/mod_bw_Administration.conf" . fs_filehandler::NewLine();
            // Server name, alias, email settings
            $line .= "ServerName " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
            $line .= "ServerAlias " . $rowvhost['vh_name_vc'] . " www." . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
            $line .= "ServerAdmin postmaster@txt-clan.com" . fs_filehandler::NewLine();

            // Document root
            $line .= "DocumentRoot \"" . self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . "\"" . fs_filehandler::NewLine();

            // Get Package openbasedir and suhosin enabled options
            if (self::GetVHOption('use_openbase') == "true") {
                if ($rowvhost['vh_obasedir_in'] <> 0) {
                    $line .= "php_admin_value open_basedir \"" . self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . self::GetVHOption('openbase_seperator') . self::GetVHOption('openbase_temp') . "\"" . fs_filehandler::NewLine();
                }
            }
            if (self::GetVHOption('use_suhosin') == "true") {
                if ($rowvhost['vh_suhosin_in'] <> 0) {
                    $line .= self::GetVHOption('suhosin_value') . fs_filehandler::NewLine();
                }
            }
            // Logs
            $line .= "ErrorLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-error.log\" " . fs_filehandler::NewLine();
            $line .= "CustomLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log\" " . self::GetVHOption('access_log_format') . fs_filehandler::NewLine();
            $line .= "CustomLog \"" . ctrl_options::GetOption('log_dir') . "domains/" . $username['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-bandwidth.log\" " . self::GetVHOption('bandwidth_log_format') . fs_filehandler::NewLine();

            // Directory options
            $line .= "<Directory />" . fs_filehandler::NewLine();
            $line .= "Options FollowSymLinks Indexes" . fs_filehandler::NewLine();
            $line .= "AllowOverride All" . fs_filehandler::NewLine();
            $line .= "Order Allow,Deny" . fs_filehandler::NewLine();
            $line .= "Allow from all" . fs_filehandler::NewLine();
            $line .= "</Directory>" . fs_filehandler::NewLine();

            // Get Package php and cgi enabled options
            $rows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $rowvhost['vh_acc_fk'] . " AND pk_deleted_ts IS NULL");
            $rows->execute();
            $dbvals = $rows->fetch();
            if ($dbvals['pk_enablephp_in'] <> 0) {
                $line .= self::GetVHOption('php_handler') . fs_filehandler::NewLine();
            }
            if ($dbvals['pk_enablecgi_in'] <> 0) {
                $line .= self::GetVHOption('cgi_handler') . fs_filehandler::NewLine();
            }

            // Error documents:- Error pages are added automatically if they are found in the _errorpages directory
            // and if they are a valid error code, and saved in the proper format, i.e. <error_number>.html
            $errorpages = self::GetVHOption('hosted_dir') . $username['ac_user_vc'] . $rowvhost['vh_directory_vc'] . "/_errorpages";
            if (is_dir($errorpages)) {
                if ($handle = opendir($errorpages)) {
                    while (($file = readdir($handle)) !== false) {
                        if ($file != "." && $file != "..") {
                            $page = explode(".", $file);
                            if (!fs_director::CheckForEmptyValue(self::CheckErrorDocument($page[0]))) {
                                $line .= "ErrorDocument " . $page[0] . " /_errorpages/" . $page[0] . ".html" . fs_filehandler::NewLine();
                            }
                        }
                    }
                    closedir($handle);
                }
            }

            // Directory indexes
            $line .= self::GetVHOption('dir_index') . fs_filehandler::NewLine();

            // Global custom global vh entry
            $line .= "# Custom Global Settings" . fs_filehandler::NewLine();
            $line .= self::GetVHOption('global_vhcustom') . fs_filehandler::NewLine();

            // Client custom vh entry
            $line .= "# Custom VH settings" . fs_filehandler::NewLine();
            $line .= $rowvhost['vh_custom_tx'] . fs_filehandler::NewLine();

            // End Virtual Host Settings
            $line .= "</virtualhost>" . fs_filehandler::NewLine();
            $line .= "# END DOMAIN: " . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
            $line .= "################################################################" . fs_filehandler::NewLine();
        }

        // write the temp config file
        $vhconfigfile = self::GetVHOption('temp_dir') . "vhost.tmp";
        if (fs_filehandler::UpdateFile($vhconfigfile, 0777, $line)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function CheckErrorDocument($error) {
        $errordocs = array(100,
            101,
            102,
            200,
            201,
            202,
            203,
            204,
            205,
            206,
            207,
            300,
            301,
            302,
            303,
            304,
            305,
            306,
            307,
            400,
            401,
            402,
            403,
            404,
            405,
            406,
            407,
            408,
            409,
            410,
            411,
            412,
            413,
            414,
            415,
            416,
            417,
            418,
            419,
            420,
            421,
            422,
            423,
            424,
            425,
            426,
            500,
            501,
            502,
            503,
            504,
            505,
            506,
            507,
            508,
            509,
            510);
        if (in_array($error, $errordocs)) {
            return true;
        } else {
            return false;
        }
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your VHost settings have been saved successfully!"));
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

    static function GetVHOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT vhs_value_tx FROM x_vhosts_settings WHERE vhs_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['vhs_value_tx'];
        } else {
            return false;
        }
    }

}

?>