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
	static $distrobutionlistsquota;
	
    static function getUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getDomainsUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayDomainsUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getSubDomainsUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplaySubDomainsUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getParkedDomainsUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayParkedDomainsUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getMysqlUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayMysqlUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getFTPUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayFTPUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getMailboxUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayMailboxUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getForwardersUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayForwardersUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

    static function getDistListUsage() {
		if (file_exists('etc/lib/pChart2/class/pData.class.php')){
			$display = self::DisplayDistListUsagepChart();
		} else {
			$display = "pChart Library Not Found.";
		}
		return $display;		
    }

	#Begin Display Methods
    static function DisplayUsagepChart() {
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		self::$diskquota 			  = $currentuser['diskquota'];
		self::$diskspace 			  = fs_director::GetQuotaUsages('diskspace', $currentuser['userid']);
		self::$bandwidthquota 		  = $currentuser['bandwidthquota'];
		self::$bandwidth 			  = fs_director::GetQuotaUsages('bandwidth', $currentuser['userid']);
		self::$domainsquota 		  = $currentuser['domainquota'];
		self::$domains 				  = fs_director::GetQuotaUsages('domains', $currentuser['userid']);
		self::$subdomainsquota  	  = $currentuser['subdomainquota'];
		self::$subdomains 			  = fs_director::GetQuotaUsages('subdomains', $currentuser['userid']);
		self::$parkeddomainsquota 	  = $currentuser['parkeddomainquota'];
		self::$parkeddomains 		  = fs_director::GetQuotaUsages('parkeddomains', $currentuser['userid']);
		self::$mysqlquota 			  = $currentuser['mysqlquota'];
		self::$mysql 				  = fs_director::GetQuotaUsages('mysql', $currentuser['userid']);
		self::$ftpaccountsquota 	  = $currentuser['ftpaccountsquota'];
		self::$ftpaccounts     		  = fs_director::GetQuotaUsages('ftpaccounts', $currentuser['userid']);
		self::$mailboxquota   	      = $currentuser['mailboxquota'];
		self::$mailboxes       		  = fs_director::GetQuotaUsages('mailboxes', $currentuser['userid']);
		self::$forwardersquota 		  = $currentuser['forwardersquota'];
		self::$forwarders      		  = fs_director::GetQuotaUsages('forwarders', $currentuser['userid']);
		self::$distrobutionlistsquota = $currentuser['distrobutionlistsquota'];
		self::$distlists       		  = fs_director::GetQuotaUsages('distlists', $currentuser['userid']);
		
		$total= self::$diskquota;
		$used = self::$diskspace;
		$free = $total - $used;		
		
		$line  = "<table>";
  		$line .= "<tr>";
	    $line .= "<td align=\"left\" valign=\"top\" width=\"350px\">";
		$line .= "<h2>".ui_language::translate("Disk Usage Total")."</h2>";
	
		//$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=40::30::20&labels=test_1::test_2::test_3\"/></td>";
		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free_Space: ".fs_director::ShowHumanFileSize($free)."::Used_Space: ".fs_director::ShowHumanFileSize($used)."&legendfont=verdana&legendfontsize=8&imagesize=350::250&chartsize=150::120&radius=150&legendsize=10::220\"/></td>";

		$line .= "<td align=\"left\" valign=\"top\">";
		$line .= "<h2>".ui_language::translate("Package Usage Total")."</h2>";
		$line .= "<table class=\"zgrid\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Disk space").":</th>";
		$line .= "<td>".fs_director::ShowHumanFileSize(self::$diskspace)." / ".fs_director::ShowHumanFileSize(self::$diskquota)."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Bandwidth").":</th>";
		$line .= "<td>".fs_director::ShowHumanFileSize(self::$bandwidth)." / ".fs_director::ShowHumanFileSize(self::$bandwidthquota)."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Domains").":</th>";
		$line .= "<td>".self::$domains." / ".self::$domainsquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Sub-domains").":</th>";
		$line .= "<td>".self::$subdomains." / ".self::$subdomainsquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Parked domains").":</th>";
		$line .= "<td>".self::$parkeddomains." / ".self::$parkeddomainsquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("FTP accounts").":</th>";
		$line .= "<td>".self::$ftpaccounts." / ".self::$ftpaccountsquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("MySQL&reg databases").":</th>";
		$line .= "<td>".self::$mysql." / ".self::$mysqlquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Mailboxes").":</th>";
		$line .= "<td>".self::$mailboxes." / ".self::$mailboxquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Mail forwarders").":</th>";
		$line .= " <td>".self::$forwarders." / ".self::$forwardersquota."</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th>".ui_language::translate("Distrubution lists").":</th>";
		$line .= "<td>".self::$distlists." / ".self::$distrobutionlistsquota."</td>";
		$line .= "</tr>";
		$line .= "</table>";
		$line .= "</td>";
		$line .= "</tr>";
		$line .= "</table>";
		//$line  = "<img src=\"etc/lib/pChart2/charts/z3DPie.php?used=alot\"/>";
		return $line;	
    }

    static function DisplayDomainsUsagepChart() {
		$line  = "<h2>Domain Usage</h2>";
		$total= self::$domainsquota;
		$used = self::$domains;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplaySubDomainsUsagepChart() {
		$line  = "<h2>Sub-Domain Usage</h2>";
		$total= self::$subdomainsquota;
		$used = self::$subdomains;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayParkedDomainsUsagepChart() {
		$line  = "<h2>Parked-Domain Usage</h2>";
		$total= self::$parkeddomainsquota;
		$used = self::$parkeddomains;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayMysqlUsagepChart() {
		$line  = "<h2>MySQL&reg Database Usage</h2>";
		$total= self::$mysqlquota;
		$used = self::$mysql;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayMailboxUsagepChart() {
		$line  = "<h2>Mailbox Usage</h2>";
		$total= self::$mailboxquota;
		$used = self::$mailboxes;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayFTPUsagepChart() {
		$line  = "<h2>FTP Usage</h2>";
		$total= self::$ftpaccountsquota;
		$used = self::$ftpaccounts;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayForwardersUsagepChart() {
		$line  = "<h2>Forwarders Usage</h2>";
		$total= self::$forwardersquota;
		$used = self::$forwarders;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

    static function DisplayDistListUsagepChart() {
		$line  = "<h2>Distrubution List Usage</h2>";
		$total= self::$distrobutionlistsquota;
		$used = self::$distlists;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=10::160\"/>";		
		return $line;
	}

	static function getModuleDesc() {
		$message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

	static function getModuleName() {
		$module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

}

?>