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
 * @change P.Peyremorte added unlimited, factorization in functions
 */
class module_controller extends ctrl_module
{

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

    static private function check_pChart($display)
    {
        return (file_exists('etc/lib/pChart2/class/pData.class.php')) ? $display : 'pChart Library Not Found.';
    }

    static function getUsage()
    {
        return self::check_pChart(self::DisplayUsagepChart());
    }

    static function getDomainsUsage()
    {
        return self::check_pChart(self::DisplayDomainsUsagepChart());
    }

    static function getSubDomainsUsage()
    {
        return self::check_pChart(self::DisplaySubDomainsUsagepChart());
    }

    static function getParkedDomainsUsage()
    {
        return self::check_pChart(self::DisplayParkedDomainsUsagepChart());
    }

    static function getMysqlUsage()
    {
        return self::check_pChart(self::DisplayMysqlUsagepChart());
    }

    static function getFTPUsage()
    {
        return self::check_pChart(self::DisplayFTPUsagepChart());
    }

    static function getMailboxUsage()
    {
        return self::check_pChart(self::DisplayMailboxUsagepChart());
    }

    static function getForwardersUsage()
    {
        return self::check_pChart(self::DisplayForwardersUsagepChart());
    }

    static function getDistListUsage()
    {
        return self::check_pChart(self::DisplayDistListUsagepChart());
    }

    #Begin Display Methods

    static function empty_as_0($value)
    {
        return (empty($value)) ? 0 : $value;
    }

    static function build_row_usage($name, $used, $quota, $human = false)
    {
        $res = '<tr><th nowrap="nowrap">' . ui_language::translate($name) . ':</th><td nowrap="nowrap">' . (($human) ? fs_director::ShowHumanFileSize($used) : $used);
        if ($quota < 0) {
            $res .= '</td><td style="text-align:center">&#8734; ' . ui_language::translate('Unlimited') . ' &#8734;</td>';
        } else {
            $res .= ' / ' . (($human) ? fs_director::ShowHumanFileSize($quota) : $quota) . '</td><td><img src="etc/lib/pChart2/sentora/zProgress.php?percent=' . (($quota == 0 or $used == $quota) ? 100 : round($used / $quota * 100, 0)) . '"/>';
        }
        return $res . '</td></tr>';
    }

    static function DisplayUsagepChart()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();

        self::$diskquota = $currentuser['diskquota'];
        self::$diskspace = ctrl_users::GetQuotaUsages('diskspace', $currentuser['userid']);

        self::$bandwidthquota = module_controller::empty_as_0($currentuser['bandwidthquota']);
        self::$bandwidth = ctrl_users::GetQuotaUsages('bandwidth', $currentuser['userid']);

        self::$domainsquota = module_controller::empty_as_0($currentuser['domainquota']);
        self::$domains = ctrl_users::GetQuotaUsages('domains', $currentuser['userid']);

        self::$subdomainsquota = module_controller::empty_as_0($currentuser['subdomainquota']);
        self::$subdomains = ctrl_users::GetQuotaUsages('subdomains', $currentuser['userid']);

        self::$parkeddomainsquota = module_controller::empty_as_0($currentuser['parkeddomainquota']);
        self::$parkeddomains = ctrl_users::GetQuotaUsages('parkeddomains', $currentuser['userid']);

        self::$mysqlquota = module_controller::empty_as_0($currentuser['mysqlquota']);
        self::$mysql = ctrl_users::GetQuotaUsages('mysql', $currentuser['userid']);

        self::$ftpaccountsquota = module_controller::empty_as_0($currentuser['ftpaccountsquota']);
        self::$ftpaccounts = ctrl_users::GetQuotaUsages('ftpaccounts', $currentuser['userid']);

        self::$mailboxquota = module_controller::empty_as_0($currentuser['mailboxquota']);
        self::$mailboxes = ctrl_users::GetQuotaUsages('mailboxes', $currentuser['userid']);

        self::$forwardersquota = module_controller::empty_as_0($currentuser['forwardersquota']);
        self::$forwarders = ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']);

        self::$distlistsquota = $currentuser['distlistsquota'];
        self::$distlists = module_controller::empty_as_0(ctrl_users::GetQuotaUsages('distlists', $currentuser['userid']));

        $maximum = self::$diskquota;
        $used = self::$diskspace;
        if ($maximum == 0) {
            if (sys_versions::ShowOSPlatformVersion() != 'Windows') {
                // We'll specify the full path to the hsoted directory to ensure that NFS mounts etc are taken into account.
                $free = disk_free_space(ctrl_options::GetOption('hosted_dir'));
            } else {
                // On Windows we'll check the disk (partition) that is configured for the 'hostdata' directory.
                $free = disk_free_space(substr(ctrl_options::GetOption('hosted_dir'), 0, 2));
            }
            $freeLabel = fs_director::ShowHumanFileSize($free) . ' (' . ui_language::translate('Server disk') . ')';
        } else {
            $free = max($maximum - $used, 0);
            $freeLabel = fs_director::ShowHumanFileSize($free);
        }
        $usedLabel = fs_director::ShowHumanFileSize($used);


        $line = '<table class="none" cellpadding="0" cellspacing="0">' .
                '<tr>' .
                '<td align="left" valign="top" width="350px">' .
                '<h2>' . ui_language::translate('Disk Usage Total') . '</h2>' .
                '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used .
                '&amp;imagesize=350::250&amp;chartsize=150::120&amp;radius=150' .
                '&amp;labels=Free_Space: ' . $freeLabel . '::Used_Space: ' . $usedLabel .
                '&amp;legendfont=verdana&amp;legendfontsize=8&amp;legendsize=10::220"/>' .
                '</td>' .
                '<td align="left" valign="top">' .
                '<h2>' . ui_language::translate('Package Usage Total') . '</h2>' .
                '<table class="table table-striped" border="0" cellspacing="0" cellpadding="0">' .
                module_controller::build_row_usage('Disk space', self::$diskspace, (self::$diskquota == 0) ? -1 : self::$diskquota, true) .
                module_controller::build_row_usage('Bandwidth', self::$bandwidth, (self::$bandwidthquota == 0) ? -1 : self::$bandwidthquota, true) .
                module_controller::build_row_usage('Domains', self::$domains, self::$domainsquota) .
                module_controller::build_row_usage('Sub-domains', self::$subdomains, self::$subdomainsquota) .
                module_controller::build_row_usage('Parked domains', self::$parkeddomains, self::$parkeddomainsquota) .
                module_controller::build_row_usage('FTP accounts', self::$ftpaccounts, self::$ftpaccountsquota) .
                module_controller::build_row_usage('MySQL&reg databases', self::$mysql, self::$mysqlquota) .
                module_controller::build_row_usage('Mailboxes', self::$mailboxes, self::$mailboxquota) .
                module_controller::build_row_usage('Mail forwarders', self::$forwarders, self::$forwardersquota) .
                module_controller::build_row_usage('Distribution lists', self::$distlists, self::$distlistsquota) .
                '</table>' .
                '</td>' .
                '</tr>' .
                '</table>';
        return $line;
    }

    static private function DisplayChart($name, $used, $maximum)
    {
		global $controller;
        if ($maximum < 0) { //-1 = unlimited
            	if (file_exists(ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png')) {
				$res = '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
		}else{
			$res = '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
		}
        } else {
            $free = max($maximum - $used, 0);
            $res = '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&amp;imagesize=240::190&amp;chartsize=120::90&amp;radius=100'
                    . '&amp;labels=Free: ' . $free . '::Used: ' . $used
                    . '&amp;legendfont=verdana&amp;legendfontsize=8&amp;legendsize=10::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
        return '<h2>' . ui_language::translate($name) . '</h2>' . $res;
    }

    static function DisplayDomainsUsagepChart()
    {
        return self::DisplayChart('Domain Usage', self::$domains, self::$domainsquota);
    }

    static function DisplaySubDomainsUsagepChart()
    {
        return self::DisplayChart('Sub-Domain Usage', self::$subdomains, self::$subdomainsquota);
    }

    static function DisplayParkedDomainsUsagepChart()
    {
        return self::DisplayChart('Parked-Domain Usage', self::$parkeddomains, self::$parkeddomainsquota);
    }

    static function DisplayMysqlUsagepChart()
    {
        return self::DisplayChart('MySQL&reg Database Usage', self::$mysql, self::$mysqlquota);
    }

    static function DisplayMailboxUsagepChart()
    {
        return self::DisplayChart('Mailbox Usage', self::$mailboxes, self::$mailboxquota);
    }

    static function DisplayFTPUsagepChart()
    {
        return self::DisplayChart('FTP Usage', self::$ftpaccounts, self::$ftpaccountsquota);
    }

    static function DisplayForwardersUsagepChart()
    {
        return self::DisplayChart('Forwarders Usage', self::$forwarders, self::$forwardersquota);
    }

    static function DisplayDistListUsagepChart()
    {
        return self::DisplayChart('Distribution List Usage', self::$distlists, self::$distlistsquota);
    }

    static function DisplaypBar($total, $quota)
    {
        $currentuser = ctrl_users::GetUserDetail();
        $typequota = $currentuser[$quota];
        $type = ctrl_users::GetQuotaUsages($total, $currentuser['userid']);
        if ($typequota == 0)
            return ''; //Quota are disabled
        if (fs_director::CheckForEmptyValue($type))
            return '<img src="etc/lib/pChart2/sentora/zProgress.php?percent=0"/>';
        if ($type == $typequota)
            return '<img src="etc/lib/pChart2/sentora/zProgress.php?percent=100"/>';
        return '<img src="etc/lib/pChart2/sentora/zProgress.php?percent=' . round($type / $typequota * 100, 0) . '"/>';
    }

}
