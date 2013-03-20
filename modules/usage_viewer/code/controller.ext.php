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

    static $diskquota;
    static $diskspace;
    static $bandwidth;
    static $bandwidthquota;
    static $domains;
    static $domainsquota;
    static $subdomains;
    static $subdomainsquota;
    static $parkeddomains;
    static $parkeddomainsquota;
    static $mysql;
    static $mysqlquota;
    static $ftpaccounts;
    static $ftpaccountsquota;
    static $mailboxes;
    static $mailboxquota;
    static $forwarders;
    static $forwardersquota;
    static $distlists;
    static $distlistsquota;

    private function check_pChart($display) {
        return (file_exists('etc/lib/pChart2/class/pData.class.php')) ? $display : 'pChart Library Not Found.';
    }

    static function getUsage() {
        return self::check_pChart(self::DisplayUsagepChart());
    }

    static function getDomainsUsage() {
        return self::check_pChart(self::DisplayDomainsUsagepChart());
    }

    static function getSubDomainsUsage() {
        return self::check_pChart(self::DisplaySubDomainsUsagepChart());
    }

    static function getParkedDomainsUsage() {
        return self::check_pChart(self::DisplayParkedDomainsUsagepChart());
    }

    static function getMysqlUsage() {
        return self::check_pChart(self::DisplayMysqlUsagepChart());
    }

    static function getFTPUsage() {
        return self::check_pChart(self::DisplayFTPUsagepChart());
    }

    static function getMailboxUsage() {
        return self::check_pChart(self::DisplayMailboxUsagepChart());
    }

    static function getForwardersUsage() {
        return self::check_pChart(self::DisplayForwardersUsagepChart());
    }

    static function getDistListUsage() {
        return self::check_pChart(self::DisplayDistListUsagepChart());
    }

    #Begin Display Methods

    static function DisplayUsagepChart() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();

        function empty_as_0($value) {
            return (empty($value)) ? 0 : $value;
        }

        self::$diskquota = $currentuser['diskquota'];
        self::$diskspace = ctrl_users::GetQuotaUsages('diskspace', $currentuser['userid']);

        self::$bandwidthquota = empty_as_0($currentuser['bandwidthquota']);
        self::$bandwidth = ctrl_users::GetQuotaUsages('bandwidth', $currentuser['userid']);

        self::$domainsquota = empty_as_0($currentuser['domainquota']);
        self::$domains = ctrl_users::GetQuotaUsages('domains', $currentuser['userid']);

        self::$subdomainsquota = empty_as_0($currentuser['subdomainquota']);
        self::$subdomains = ctrl_users::GetQuotaUsages('subdomains', $currentuser['userid']);

        self::$parkeddomainsquota = empty_as_0($currentuser['parkeddomainquota']);
        self::$parkeddomains = ctrl_users::GetQuotaUsages('parkeddomains', $currentuser['userid']);

        self::$mysqlquota = empty_as_0($currentuser['mysqlquota']);
        self::$mysql = ctrl_users::GetQuotaUsages('mysql', $currentuser['userid']);

        self::$ftpaccountsquota = empty_as_0($currentuser['ftpaccountsquota']);
        self::$ftpaccounts = ctrl_users::GetQuotaUsages('ftpaccounts', $currentuser['userid']);

        self::$mailboxquota = empty_as_0($currentuser['mailboxquota']);
        self::$mailboxes = ctrl_users::GetQuotaUsages('mailboxes', $currentuser['userid']);

        self::$forwardersquota = empty_as_0($currentuser['forwardersquota']);
        self::$forwarders = ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']);

        self::$distlistsquota = $currentuser['distlistsquota'];
        self::$distlists = empty_as_0(ctrl_users::GetQuotaUsages('distlists', $currentuser['userid']));

        $maximum = self::$diskquota;
        $used = self::$diskspace;
        if ($maximum == 0) {
            $free = disk_free_space('/var'); // Need to add support for Windows in here!
            $freeLabel = fs_director::ShowHumanFileSize($free) . ' (' . ui_language::translate('Server disk') . ')';
        } else {
            $free = max($maximum - $used, 0);
            $freeLabel = fs_director::ShowHumanFileSize($free);
        }
        $usedLabel = fs_director::ShowHumanFileSize($used);


        function build_row_usage($name, $used, $quota, $human = false) {
            $res = '<tr><th nowrap="nowrap">' . ui_language::translate($name) . ':</th><td nowrap="nowrap">' . (($human) ? fs_director::ShowHumanFileSize($used) : $used);
            if ($quota < 0) {
                $res .= '</td><td style="text-align:center">&#8734; ' . ui_language::translate('Unlimited') . ' &#8734;</td>';
            } else {
                $res .= ' / ' . (($human) ? fs_director::ShowHumanFileSize($quota) : $quota) . '</td><td><img src="etc/lib/pChart2/zpanel/zProgress.php?percent=' . (($quota == 0 or $used == $quota) ? 100 : round($used / $quota * 100, 0)) . '"/>';
            }
            return $res . '</td></tr>';
        }
        $line =
                '<table class="none" cellpadding="0" cellspacing="0">' .
                '<tr>' .
                '<td align="left" valign="top" width="350px">' .
                '<h2>' . ui_language::translate('Disk Usage Total') . '</h2>' .
                '<img src="etc/lib/pChart2/zpanel/z3DPie.php?score=' . $free . '::' . $used .
                '&amp;imagesize=350::250&amp;chartsize=150::120&amp;radius=150' .
                '&amp;labels=Free_Space: ' . $freeLabel . '::Used_Space: ' . $usedLabel .
                '&amp;legendfont=verdana&amp;legendfontsize=8&amp;legendsize=10::220"/>' .
                '</td>' .
                '<td align="left" valign="top">' .
                '<h2>' . ui_language::translate('Package Usage Total') . '</h2>' .
                '<table class="zgrid" border="0" cellspacing="0" cellpadding="0">' .
                build_row_usage('Disk space', self::$diskspace, (self::$diskquota == 0) ? -1 : self::$diskquota, true) .
                build_row_usage('Bandwidth', self::$bandwidth, (self::$bandwidthquota == 0) ? -1 : self::$bandwidthquota, true) .
                build_row_usage('Domains', self::$domains, self::$domainsquota) .
                build_row_usage('Sub-domains', self::$subdomains, self::$subdomainsquota) .
                build_row_usage('Parked domains', self::$parkeddomains, self::$parkeddomainsquota) .
                build_row_usage('FTP accounts', self::$ftpaccounts, self::$ftpaccountsquota) .
                build_row_usage('MySQL&reg databases', self::$mysql, self::$mysqlquota) .
                build_row_usage('Mailboxes', self::$mailboxes, self::$mailboxquota) .
                build_row_usage('Mail forwarders', self::$forwarders, self::$forwardersquota) .
                build_row_usage('Distribution lists', self::$distlists, self::$distlistsquota) .
                '</table>' .
                '</td>' .
                '</tr>' .
                '</table>';
        return $line;
    }

    private function DisplayChart($name, $used, $maximum) {
        if ($maximum < 0) { //-1 = unlimited
            $res = '<img src="' . ui_tpl_assetfolderpath::Template() . 'images/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
        } else {
            $free = max($maximum - $used, 0);
            $res = '<img src="etc/lib/pChart2/zpanel/z3DPie.php?score=' . $free . '::' . $used
                    . '&amp;imagesize=240::190&amp;chartsize=120::90&amp;radius=100'
                    . '&amp;labels=Free: ' . $free . '::Used: ' . $used
                    . '&amp;legendfont=verdana&amp;legendfontsize=8&amp;legendsize=10::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
        return '<h2>' . ui_language::translate($name) . '</h2>' . $res;
    }

    static function DisplayDomainsUsagepChart() {
        return self::DisplayChart('Domain Usage', self::$domains, self::$domainsquota);
    }

    static function DisplaySubDomainsUsagepChart() {
        return self::DisplayChart('Sub-Domain Usage', self::$subdomains, self::$subdomainsquota);
    }

    static function DisplayParkedDomainsUsagepChart() {
        return self::DisplayChart('Parked-Domain Usage', self::$parkeddomains, self::$parkeddomainsquota);
    }

    static function DisplayMysqlUsagepChart() {
        return self::DisplayChart('MySQL&reg Database Usage', self::$mysql, self::$mysqlquota);
    }

    static function DisplayMailboxUsagepChart() {
        return self::DisplayChart('Mailbox Usage', self::$mailboxes, self::$mailboxquota);
    }

    static function DisplayFTPUsagepChart() {
        return self::DisplayChart('FTP Usage', self::$ftpaccounts, self::$ftpaccountsquota);
    }

    static function DisplayForwardersUsagepChart() {
        return self::DisplayChart('Forwarders Usage', self::$forwarders, self::$forwardersquota);
    }

    static function DisplayDistListUsagepChart() {
        return self::DisplayChart('Distribution List Usage', self::$distlists, self::$distlistsquota);
    }

    static function DisplaypBar($total, $quota) {
        $currentuser = ctrl_users::GetUserDetail();
        $typequota = $currentuser[$quota];
        $type = ctrl_users::GetQuotaUsages($total, $currentuser['userid']);
        if ($typequota == 0)
            return ''; //Quota are disabled
        if (fs_director::CheckForEmptyValue($type))
            return '<img src="etc/lib/pChart2/zpanel/zProgress.php?percent=0"/>';
        if ($type == $typequota)
            return '<img src="etc/lib/pChart2/zpanel/zProgress.php?percent=100"/>';
        return '<img src="etc/lib/pChart2/zpanel/zProgress.php?percent=' . round($type / $typequota * 100, 0) . '"/>';
    }

    static function getModuleDesc() {
        return ui_language::translate(ui_module::GetModuleDescription());
    }

    static function getModuleName() {
        return ui_language::translate(ui_module::GetModuleName());
    }

    static function getModuleIcon() {
        global $controller;
        return 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/icon.png';
    }

}

?>