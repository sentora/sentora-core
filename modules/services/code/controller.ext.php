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
 * @changes P.Peyremorte
 * - added display of ports checked (80 may be not used!)
 * - refactored (replacement of duplicate string constructions by a fucntion)
 */
class module_controller extends ctrl_module
{
    static private function status_port($PortNum, $iconpath)
    {
        $status = sys_monitoring::LocalPortStatus($PortNum);
        return ($status ?  $iconpath.'up.gif">' : $iconpath.'down.gif">') . ' (port ' . $PortNum .' is ' . ($status ? 'open' : 'closed') . ')';
    }

    static public function getServices()
    {
        global $controller;
        if(file_exists(ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/up.gif') && file_exists(ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/down.gif')) {
            $iconpath = '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/';
        }else{
            $iconpath = '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/';    
        }
        $line = "<h2>" . ui_language::translate("Checking status of services...") . "</h2>";
        $line .= "<table>";
        
        $status = fs_director::CheckForEmptyValue(sys_monitoring::PortStatus($PortNum));

        $line .= '<tr><th>HTTP</th><td>'  . module_controller::status_port(80, $iconpath) . '</td></tr>';
        $line .= '<tr><th>FTP</th><td>'   . module_controller::status_port(21, $iconpath) . '</td></tr>';
        $line .= '<tr><th>SMTP</th><td>'  . module_controller::status_port(25, $iconpath) . '</td></tr>';
        $line .= '<tr><th>POP3</th><td>'  . module_controller::status_port(110, $iconpath) . '</td></tr>';
        $line .= '<tr><th>IMAP</th><td>'  . module_controller::status_port(143, $iconpath) . '</td></tr>';
        $line .= '<tr><th>MySQL</th><td>' . module_controller::status_port(3306, $iconpath) . '</td></tr>';
        $line .= '<tr><th>DNS</th><td>'   . module_controller::status_port(53, $iconpath)  . '</td></tr>';
        $line .= '</table>';
        $line .= '<br><h2>' . ui_language::translate('Server Uptime') . '</h2>';
        $line .= ui_language::translate('Uptime') . ": " . sys_monitoring::ServerUptime();
        return $line;
    }

    static function getIsWebServerUp()
    {
        return sys_monitoring::PortStatus(80);
    }

    static function getIsMySQLUp()
    {
        return sys_monitoring::PortStatus(3306);
    }

    static function getIsFTPUp()
    {
        return sys_monitoring::PortStatus(21);
    }

    static function getIsSMTPUp()
    {
        return sys_monitoring::PortStatus(25);
    }

    static function getIsPOP3Up()
    {
        return sys_monitoring::PortStatus(110);
    }

    static function getIsIMAPUp()
    {
        return sys_monitoring::PortStatus(143);
    }

    static function getUptime()
    {
        return sys_monitoring::ServerUptime();
    }

    static function getLastRunTime()
    {
        return date(ctrl_options::GetSystemOption('sentora_df'), ctrl_options::GetSystemOption('daemon_lastrun'));
    }

    static function getNextRunTime()
    {
        $new_time = ctrl_options::GetSystemOption('daemon_lastrun') + ctrl_options::GetSystemOption('daemon_run_interval');
        return date(ctrl_options::GetSystemOption('sentora_df'), $new_time);
    }

}
