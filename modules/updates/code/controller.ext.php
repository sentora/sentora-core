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
 * - packed reused strings
 * - adapted message to not show last stable if version < 1.0.0
 */
class module_controller extends ctrl_module
{
    public static function getSentoraUpdates()
    {   
        $installed = ctrl_options::GetSystemOption('dbversion');
        $lastest = ctrl_options::GetSystemOption('latestzpversion');
        $lastest_tagged = ' (<strong>' . $lastest . '</strong>)';
        
        if ($installed < $lastest ) {
            $msg = ui_language::translate('There are currently new updates for your Sentora installation, please download the latest release') 
                 . $lastest_tagged .' from <a href="http://www.sentora.org/">http://www.sentora.org/</a>.';
        } elseif ($installed == $lastest) {
            $msg = 'Congratulations, You are running the most recent version of Sentora' . $lastest_tagged . '!';
        } else {
            $msg = 'You are running a BETA release (<strong>' . $installed . '</strong>), thank you to report what you observed.<br>'
                  .'<b>Do not use it for production.</b>';
            if ($latest >= '1.0.0')
                $msg .='<br><br>Unless you are testing or developing we recommend you to download and use the latest stable release' . $lastest_tagged . '.';
        }
        return $msg;
    }
}