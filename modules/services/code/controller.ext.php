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

    function getServices() {
        global $controller;
        $line = "<h2>" . ui_language::translate("Checking status of services...") . "</h2>";
        $line .= "<table>";
        $line .= "<tr>";
        $line .= "<th>HTTP</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(80))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>FTP</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(21))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>SMTP</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(25))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>POP3</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(110))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>IMAP</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(143))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>MySQL</th>";
        $line .= "<td>";

        if (fs_director::CheckForEmptyValue(sys_monitoring::PortStatus(3306))) {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/down.gif\">";
        } else {
            $line .= "<img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/up.gif\">";
        }

        $line .= "</td>";
        $line .= "</tr>";
        $line .= "</table>";
        $line .= "<br><h2>" . ui_language::translate("Server Uptime") . "</h2>";
        $line .= ui_language::translate("Uptime") . ": " . sys_monitoring::ServerUptime();
        return $line;
    }

    static function getModuleName() {
        $module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

    static function getModuleDesc() {
        $message = ui_language::translate("Here you can check the current status of our services and see what services are up and running and which are down and not.");
        return $message;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
    
    static function getIsWebServerUp(){
        return sys_monitoring::PortStatus(80);
    }
    
    static function getIsMySQLUp(){
        return sys_monitoring::PortStatus(3306);
    }
    
    static function getIsFTPUp(){
        return sys_monitoring::PortStatus(21);
    }
    
    static function getIsSMTPUp(){
        return sys_monitoring::PortStatus(25);
    }
    
    static function getIsPOP3Up(){
        return sys_monitoring::PortStatus(110);
    }
    
    static function getIsIMAPUp(){
        return sys_monitoring::PortStatus(143);
    }

    static function getUptime(){
       return sys_monitoring::ServerUptime();
    }
    
    static function getLastRunTime(){
        return date(ctrl_options::GetOption('zpanel_df'),ctrl_options::GetOption('daemon_lastrun'));
    }
    
    static function getNextRunTime(){
        $time_between_daemon_runs = ctrl_options::GetOption('daemon_run_interval');
        $new_time = ctrl_options::GetOption('daemon_lastrun') + ctrl_options::GetOption('daemon_run_interval');
        return date(ctrl_options::GetOption('zpanel_df'),$new_time);
    }
    
}

?>