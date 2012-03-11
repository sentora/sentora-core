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
    static $editdomain;
    static $showform;
    static $ttl_error;
    static $invalidIPv4_error;
    static $invalidIPv6_error;
    static $invalidDomainName_error;
    static $invalidIP_error;
    static $priorityNumeric_error;
    static $priorityRange_error;
    static $weightNumeric_error;
    static $weightRange_error;
    static $portNumeric_error;
    static $portRange_error;
	static $hostname_error;

    static function getInit() {
        global $controller;
        $line = "";
        $line .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/dns.css\"></script>";
        $line .= "<script type=\"text/javascript\" src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/dns.js\"></script>";
        return $line;
    }

    static function getRecordAction() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainID'))) {
            $display = self::DisplayRecords();
        } elseif (fs_director::CheckForEmptyValue(self::$editdomain)) {
            $display = self::DisplayDomains();
        } else {
            //Create default records if no records are found for the domain.
            if (fs_director::CheckForEmptyValue(self::$editdomain)) {
                $domainID = $controller->GetControllerRequest('FORM', 'domainID');
            } else {
                $domainID = self::$editdomain;
            }
            $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL";
            if ($numrows = $zdbh->query($sql)) {
                if ($numrows->fetchColumn() == 0) {
                    $display = self::DisplayDefaultRecords();
                } else {
                    $display = self::DisplayRecords();
                }
            }
        }
        return $display;
    }

    static function DisplayDefaultRecords() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $line .= "<div class=\"zgrid_wrapper\">";


        $line .= "<div id=\"dnsTitle\" class=\"account accountTitle\">";
        $line .= "<div class=\"content\"><h2>" . ui_language::translate("Create Deafult DNS Records") . "</h2>";
        $line .= "" . ui_language::translate("No records were found for this domain.  Click the button below to set up your domain records for the first time") . "";
        $line .= "<div>";
        $line .= "<div class=\"actions\"><a class=\"back\" href=\"/?module=" . $controller->GetControllerRequest('URL', 'module') . "\">Domain List</a></div>";
        $line .= "</div><br class=\"clear\">";
        $line .= "</div>";
        $line .= "</div>";


        $line .= "<form action=\"./?module=dns_manager&action=CreateDefaultRecords\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<td>";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\">" . ui_language::translate("Create Records") . "</button>";
        $line .= "</td>";
        $line .= "</tr>";
        $line .= "</table>";
        $line .= "<input type=\"hidden\" name=\"inDomain\" value =\"" . $controller->GetControllerRequest('FORM', 'inDomain') . "\" />";
        $line .= "<input type=\"hidden\" name=\"inUserID\" value =\"" . $currentuser['userid'] . "\" />";
        $line .= "</form>";
        $line .= "</div>";
        return $line;
    }

    static function DisplayRecords() {
        //print_r($_POST); //Post Debug
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if (fs_director::CheckForEmptyValue(self::$editdomain)) {
            $domainID = $controller->GetControllerRequest('FORM', 'domainID');
        } else {
            $domainID = self::$editdomain;
        }
		$domain = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_id_pk=" . $domainID . " AND vh_type_in !=2 AND vh_deleted_ts IS NULL")->Fetch();
		$zone_message=self::CheckZoneRecord($domainID);
		$zonecheck_file = ctrl_options::GetOption('temp_dir') . $domain['vh_name_vc'] . ".txt";
		$zone_message = str_replace($zonecheck_file, "", $zone_message);
		if (strstr(strtoupper($zone_message), "OK")){
			if (substr_count($zone_message, ":") >= 3){
				$zone_error_message = "<font color=\"orange\">Your DNS zone has been loaded, but with errors. Some features will not work until corrected.</font>";
			} else {
				$zone_error_message = "<font color=\"green\">Your DNS zone has been loaded without errors.</font>";
			}
			$zone_status = "<img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/up.png\" />";
		} else {
			$zone_error_message = "<font color=\"red\">Errors detected have prevented your DNS zone from being loaded. Please correct the error(s) listed below. Until these errors are fixed, your DNS will not work.</font>";
			$zone_status = "<img src=\"modules/".$controller->GetControllerRequest('URL', 'module')."/assets/down.png\" />";
		}
        $line = "";
        $line .= "<!-- DNS FORM -->";
        //$line .= "<div style=\"margin-right:20px;\">";
        $line .= "<div id=\"dnsTitle\" class=\"account accountTitle\">";
        $line .= "<div class=\"content\"><h2>DNS records for:</h2><a href=\"http://" . $domain['vh_name_vc'] . "\" target=\"_blank\">" . $domain['vh_name_vc'] . "</a>";
        $line .= "<div>";
        $line .= "<div class=\"actions\"><a class=\"undo disabled\">Undo Changes</a><a class=\"save disabled\">Save Changes</a><a class=\"back\" href=\"/?module=" . $controller->GetControllerRequest('URL', 'module') . "\">Domain List</a></div>";
        $line .= "</div><br class=\"clear\">";
        $line .= "</div>";
        $line .= "</div>";
        $line .= "<form action=\"./?module=dns_manager&action=SaveDNS\" method=\"post\">";
        $line .= "<input id=\"domainName\" name=\"domainName\" value=\"" . $domain['vh_name_vc'] . "\" type=\"hidden\">";
        $line .= "<input id=\"domainID\" name=\"domainID\" value=\"" . $domain['vh_id_pk'] . "\" type=\"hidden\">";
        $line .= "<!-- TABS -->";
        $line .= "<div class=\"ui-tabs ui-widget ui-widget-content ui-corner-all\" id=\"dnsRecords\">";
        $line .= "<ul class=\"domains ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\">";
        if (self::IsTypeAllowed('A')) {
            $line .= "<li><a href=\"#typeA\">A</a></li>";
        }
        if (self::IsTypeAllowed('AAAA')) {
            $line .= "<li><a href=\"#typeAAAA\">AAAA</a></li>";
        }
        if (self::IsTypeAllowed('CNAME')) {
            $line .= "<li><a href=\"#typeCNAME\">CNAME</a></li>";
        }
        if (self::IsTypeAllowed('MX')) {
            $line .= "<li><a href=\"#typeMX\">MX</a></li>";
        }
        if (self::IsTypeAllowed('TXT')) {
            $line .= "<li><a href=\"#typeTXT\">TXT</a></li>";
        }
        if (self::IsTypeAllowed('SRV')) {
            $line .= "<li><a href=\"#typeSRV\">SRV</a></li>";
        }
        if (self::IsTypeAllowed('SPF')) {
            $line .= "<li><a href=\"#typeSPF\">SPF</a></li>";
        }
        if (self::IsTypeAllowed('NS')) {
            $line .= "<li><a href=\"#typeNS\">NS</a></li>";
        }
        $line .= "</ul>";
        if (self::IsTypeAllowed('A')) {
            $line .= "<!-- A RECORDS -->";
            $line .= "<div class=\"records dnsRecordA ui-tabs-panel ui-widget-content ui-corner-bottom\" id=\"typeA\">";
            $line .= "<div class=\"description\">The A record contains an IPv4 address. It's target is an IPv4 address, e.g. '192.168.1.1'.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div><div class=\"type\">&nbsp;</div><div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //A Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='A' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                if (ctrl_options::GetOption('custom_ip') == strtolower("false")) {
                    $custom_ip = "READONLY";
                } else {
                    $custom_ip = NULL;
                }
                $line .= "<div class=\"dnsRecord row\">\n";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>\n";
                $line .= "<div class=\"TTL\">\n";
                $line .= "<input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\">\n";
                $line .= "<input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\">\n";
                $line .= "</div>\n";
                $line .= "<div class=\"in\">IN</div>\n";
                $line .= "<div class=\"type\">A</div>\n";
                $line .= "<div class=\"target\">\n";
                $line .= "<input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\" " . $custom_ip . ">\n";
                $line .= "<input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\">\n";
                $line .= "</div>\n";
                $line .= "<span class=\"delete enableToolTip\"></span>\n";
                $line .= "<span class=\"undo enableToolTip\"></span>\n";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"A\" type=\"hidden\">\n";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">\n";
                $line .= "<br>\n";
                $line .= "</div>\n";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">A</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span><input name=\"proto_type\" value=\"A\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "</div> <!-- END A RECORDS -->";
        }
        if (self::IsTypeAllowed('AAAA')) {
            $line .= "<!-- AAA RECORDS -->";
            $line .= "<div class=\"records dnsRecordAAAA ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeAAAA\">";
            $line .= "<div class=\"description\">The AAAA record contains an IPv6 address. It's target is an IPv6 address, e.g. '2607:fe90:2::1'.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div>";
            $line .= "<div class=\"type\">&nbsp;</div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //AAAA Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='AAAA' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">\n";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>\n";
                $line .= "<div class=\"TTL\">\n";
                $line .= "<input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\">\n";
                $line .= "<input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\">\n";
                $line .= "</div>\n";
                $line .= "<div class=\"in\">IN</div>\n";
                $line .= "<div class=\"type\">AAAA</div>\n";
                $line .= "<div class=\"target\">\n";
                $line .= "<input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\">\n";
                $line .= "<input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\">\n";
                $line .= "</div>\n";
                $line .= "<span class=\"delete enableToolTip\"></span>\n";
                $line .= "<span class=\"undo enableToolTip\"></span>\n";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"AAAA\" type=\"hidden\">\n";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">\n";
                $line .= "<br>\n";
                $line .= "</div>\n";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div><div class=\"type\">AAAA</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span><input name=\"proto_type\" value=\"AAAA\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "</div> <!-- END AAA RECORDS -->";
        }
        if (self::IsTypeAllowed('CNAME')) {
            $line .= "<!-- CNAME RECORDS -->	";
            $line .= "<div class=\"records dnsRecordCNAME ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeCNAME\">";
            $line .= "<div class=\"description\">The CNAME record specifies the canonical name of a record. It's target is a fully qualified domain name, e.g. 
'webserver-01.example.com'.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div>";
            $line .= "<div class=\"type\">&nbsp;</div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //CNAME Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='CNAME' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\">";
                $line .= "<input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\">";
                $line .= "<input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">CNAME</div>";
                $line .= "<div class=\"target\">";
                $line .= "<input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\">";
                $line .= "<input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\">";
                $line .= "</div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"CNAME\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">CNAME</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span><input name=\"proto_type\" value=\"CNAME\" type=\"hidden\">";
            $line .= "</div>			";
            $line .= "</div> <!-- END CNAME RECORDS -->";
        }
        if (self::IsTypeAllowed('MX')) {
            $line .= "<!-- MX RECORDS -->";
            $line .= "<div class=\"records dnsRecordMX ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeMX\">";
            $line .= "<div class=\"description\">The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535.  It's target is a fully qualified domain name, e.g. 'mail.example.com'.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div>";
            $line .= "<div class=\"type\">&nbsp;</div>";
            $line .= "<div class=\"priority\"><label class=\"enableToolTip\">Priority</label></div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //MX Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='MX' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\"><input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\"><input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">MX</div>";
                $line .= "<div class=\"priority\"><input name=\"priority[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_priority_in'] . "\" type=\"text\"><input name=\"original_priority[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_priority_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"target\"><input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\"><input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\"></div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"MX\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">MX</div>";
            $line .= "<div class=\"priority\"><label>Priority</label><input name=\"proto_priority\" type=\"text\"></div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span>";
            $line .= "<input name=\"proto_type\" value=\"MX\" type=\"hidden\">";
            $line .= "</div>			";
            $line .= "</div> <!-- END MX RECORDS -->";
        }
        if (self::IsTypeAllowed('TXT')) {
            $line .= "<!-- TXT RECORDS -->";
            $line .= "<div class=\"records dnsRecordTXT ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeTXT\">";
            $line .= "<div class=\"description\">The TXT field can be used to attach textual data to a domain.</div>";
            $line .= "<div class=\"header row\"><div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div><div class=\"type\">&nbsp;</div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //TXT Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='TXT' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\"><input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\"><input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">TXT</div>";
                $line .= "<div class=\"target\"><input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\"><input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\"></div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"TXT\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">TXT</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span>";
            $line .= "<input name=\"proto_type\" value=\"TXT\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "</div> <!-- END TXT RECORDS -->";
        }
        if (self::IsTypeAllowed('SRV')) {
            $line .= "<!-- SRV RECORDS -->	";
            $line .= "<div class=\"records dnsRecordSRV ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeSRV\">";
            $line .= "<div class=\"description\">SRV records can be used to encode the location and port of services on a domain name.  It's target is a fully qualified domain name, e.g. 'host.example.com'.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div><div class=\"type\">&nbsp;</div><div class=\"priority\"><label class=\"enableToolTip\">Priority</label></div>";
            $line .= "<div class=\"weight\"><label class=\"enableToolTip\">Weight</label></div>";
            $line .= "<div class=\"port\"><label class=\"enableToolTip\">Port</label></div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //SRV Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='SRV' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\"><input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\"><input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">SRV</div>";
                $line .= "<div class=\"priority\"><input name=\"priority[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_priority_in'] . "\" type=\"text\"><input name=\"original_priority[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_priority_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"weight\"><input name=\"weight[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_weight_in'] . "\" type=\"text\"><input name=\"original_weight[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_weight_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"port\"><input name=\"port[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_port_in'] . "\" type=\"text\"><input name=\"original_port[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_port_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"target\"><input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\"><input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\"></div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"SRV\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">SRV</div>";
            $line .= "<div class=\"priority\"><label>Priority</label><input name=\"proto_priority\" type=\"text\"></div>";
            $line .= "<div class=\"weight\"><label>Weight</label><input name=\"proto_weight\" type=\"text\"></div>";
            $line .= "<div class=\"port\"><label>Port</label><input name=\"proto_port\" type=\"text\"></div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span>";
            $line .= "<input name=\"proto_type\" value=\"SRV\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "</div> <!-- END SRV RECORDS -->	";
        }
        if (self::IsTypeAllowed('SPF')) {
            $line .= "<!-- SPF RECORDS -->";
            $line .= "<div class=\"records dnsRecordSPF ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeSPF\">";
            $line .= "<div class=\"description\">SPF records is used to store Sender Policy Framework details.  It's target is a text string, e.g.<br>'v=spf1 a:192.168.1.1 include:example.com mx ptr -all' (Click <a href=\"http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/\" target=\"_blank\">HERE</a> for the Microsoft SPF Wizard.)</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div>";
            $line .= "<div class=\"type\">&nbsp;</div>";
            $line .= "<div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //SPF Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='SPF' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\"><input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\"><input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">SPF</div>";
                $line .= "<div class=\"target\"><input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\"><input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\"></div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"SPF\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"86400\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">SPF</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\"><span class=\"delete enableToolTip\"></span>";
            $line .= "<input name=\"proto_type\" value=\"SPF\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "</div> <!-- END SPF RECORDS -->";
        }
        if (self::IsTypeAllowed('NS')) {
            $line .= "<!-- NS RECORDS -->";
            $line .= "<div class=\"records dnsRecordNS ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide\" id=\"typeNS\">";
            $line .= "<div class=\"description\">Nameserver record. Specifies nameservers for a domain. It's target is a fully qualified domain name, e.g.  'ns1.example.com'.  The records should match what the domain name has registered with the internet root servers.</div>";
            $line .= "<div class=\"header row\">";
            $line .= "<div class=\"hostName\"><label class=\"enableToolTip\">Host Name</label></div>";
            $line .= "<div class=\"TTL\"><label class=\"enableToolTip\">TTL</label></div>";
            $line .= "<div class=\"in\">&nbsp;</div><div class=\"type\">&nbsp;</div><div class=\"target\"><label class=\"enableToolTip\">Target</label></div>";
            $line .= "<div class=\"actions\"><label>Actions</label></div>";
            $line .= "<br>";
            $line .= "</div>";
            //NS Records
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_type_vc='NS' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $line .= "<div class=\"dnsRecord row\">";
                $line .= "<div class=\"hostName\"><span>" . $rowdns['dn_host_vc'] . "</span></div>";
                $line .= "<div class=\"TTL\"><input name=\"ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"text\"><input name=\"original_ttl[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_ttl_in'] . "\" type=\"hidden\"></div>";
                $line .= "<div class=\"in\">IN</div>";
                $line .= "<div class=\"type\">NS</div>";
                $line .= "<div class=\"target\"><input name=\"target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"text\"><input name=\"original_target[" . $rowdns['dn_id_pk'] . "]\" value=\"" . $rowdns['dn_target_vc'] . "\" type=\"hidden\"></div>";
                $line .= "<span class=\"delete enableToolTip\"></span>";
                $line .= "<span class=\"undo enableToolTip\"></span>";
                $line .= "<input name=\"type[" . $rowdns['dn_id_pk'] . "]\" value=\"NS\" type=\"hidden\">";
                $line .= "<input class=\"delete\" name=\"delete[" . $rowdns['dn_id_pk'] . "]\" value=\"false\" type=\"hidden\">";
                $line .= "<br>";
                $line .= "</div>";
            }
            $line .= "<div class=\"add row\"><span><span><button class=\"fg-button ui-state-default ui-corner-all\" type=\"button\">Add New Record</button></span></span></div>";
            $line .= "<div class=\"newRecord row\" style=\"display: none\">";
            $line .= "<div class=\"hostName\"><label>Host Name</label><input name=\"proto_hostName\" type=\"text\"></div>";
            $line .= "<div class=\"TTL\"><label>TTL</label><input name=\"proto_ttl\" value=\"172800\" type=\"text\"></div>";
            $line .= "<div class=\"in\">IN</div>";
            $line .= "<div class=\"type\">NS</div>";
            $line .= "<div class=\"target\"><label>Target</label><input name=\"proto_target\" type=\"text\"></div>";
            $line .= "<input class=\"delete\" name=\"proto_delete\" value=\"false\" type=\"hidden\">";
            $line .= "<span class=\"delete enableToolTip\"></span>";
            $line .= "<input name=\"proto_type\" value=\"NS\" type=\"hidden\">";
            $line .= "</div>";
            $line .= "<input name=\"newRecords\" value=\"0\" type=\"hidden\">";
            $line .= "</div> <!-- END NS RECORDS -->";
        }
        $line .= "</div> <!-- END TABS -->";
        $line .= "<div id=\"dnsTitle\" class=\"account accountTitle\">";
        $line .= "<div class=\"content\">";
        $line .= "<div>";
        $line .= "<div class=\"actions\"><a class=\"undo disabled\">Undo Changes</a><a class=\"save disabled\">Save Changes</a><a class=\"back\" href=\"/?module=" . $controller->GetControllerRequest('URL', 'module') . "\">Domain List</a></div>";
        $line .= "</div><br class=\"clear\">";
        $line .= "</div>";
        //$line .= "</div>";
        $line .= "</form>";
        //$line .= "</div>";
        $line .= "<!-- END DNS FORM -->";
        $line .= "<div class=\"zgrid_wrapper\">";
		$line .= "<h2>DNS Status for domain: " . $domain['vh_name_vc'] . "</h2>";
		$line .= "<table class=\"none\" cellpadding=\"0\" cellspacing=\"0\"><tr valign=\"top\"><td>";
		$line .= $zone_status;
		$line .= "</td><td>".$zone_error_message."<br><br>Please note that changes to your zone records can take up to 24 hours before they become \"live\".<br><br><b>Output of DNS zone checker:</b><br>";
		$line .= $zone_message;
		$line .= "</td></tr></table>";
		$line .= "</div>";
        return $line;
    }

    static function DisplayDomains() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $line .= "<div class=\"zgrid_wrapper\">";
        $line .= "<h2>" . ui_language::translate("Manage Domains") . "</h2>";
        $line .= "" . ui_language::translate("Choose fom the list of domains below") . "";
        $line .= "<form name=DisplayDNS action=\"./?module=dns_manager&action=DisplayRecords\" method=\"post\">";
        $line .= "<br><br>";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<td><select name=\"inDomain\" id=\"inDomain\">";
        $line .= "<option value=\"\" selected=\"selected\">-- " . ui_language::translate("Select a domain") . " --</option>";
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $currentuser['userid'] . " AND vh_type_in !=2 AND vh_deleted_ts IS NULL");
        $sql->execute();
        while ($rowdomains = $sql->fetch()) {
            $line .= "<option value=\"" . $rowdomains['vh_id_pk'] . "\">" . $rowdomains['vh_name_vc'] . "</option>";
        }
        $line .= "</select></td>";
        $line .= "<td>";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\" name=\"inSelect\" value=\"" . $rowdomains['vh_id_pk'] . "\">" . ui_language::translate("Select") . "</button>";
        $line .= "</td>";
        $line .= "</tr>";
        $line .= "</table>";
        $line .= "</form>";
		$line .= "<p>&nbsp;</p>";
        $line .= "</div>";
        return $line;
    }

    static function doEditClient() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk=" . $currentuser['userid'] . " AND ac_deleted_ts IS NULL");
        $sql->execute();
        while ($rowclients = $sql->fetch()) {
            if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inEdit_' . $rowclients['ac_id_pk'] . ''))) {
                self::$editdomain = TRUE;
                self::$clientid = $rowclients['ac_id_pk'];
                return;
            }
            if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_' . $rowclients['ac_id_pk'] . ''))) {
                self::DeleteClient($rowclients['ac_id_pk']);
                return;
            }
        }
    }

    static function doDisplayRecords() {
        global $zdbh;
        global $controller;
        self::$editdomain = $controller->GetControllerRequest('FORM', 'inDomain');
        return;
    }

    static function doSaveDNS() {
        global $zdbh;
        global $controller;
        if (!fs_director::CheckForEmptyValue(self::CheckForErrors())) {
            self::SaveDNS();
            //self::WriteRecord();
            self::$ok = TRUE;
            return;
        }
    }

    static function doCreateDefaultRecords() {
        global $zdbh;
        global $controller;
        $domainID = $controller->GetControllerRequest('FORM', 'inDomain');
        $domainName = $domain = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_id_pk=" . $domainID . " AND vh_type_in !=2 AND vh_deleted_ts IS NULL")->Fetch();
        $userID = $controller->GetControllerRequest('FORM', 'inUserID');
        if (!fs_director::CheckForEmptyValue(ctrl_options::GetOption('server_ip'))) {
            $target = ctrl_options::GetOption('server_ip');
        } else {
            $target = $_SERVER["SERVER_ADDR"];
        }
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'A',
															'@',
															3600,
															'" . $target . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'CNAME',
															'www',
															3600,
															'@',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'CNAME',
															'ftp',
															3600,
															'@',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'A',
															'mail',
															86400,
															'" . $target . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'MX',
															'@',
															86400,
															'mail." . $domainName['vh_name_vc'] . "',
															10,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'A',
															'ns1',
															172800,
															'" . $target . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'A',
															'ns2',
															172800,
															'" . $target . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'NS',
															'@',
															172800,
															'ns1." . $domainName['vh_name_vc'] . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $userID . ",
															'" . $domainName['vh_name_vc'] . "',
															" . $domainID . ",
															'NS',
															'@',
															172800,
															'ns2." . $domainName['vh_name_vc'] . "',
															NULL,
															NULL,
															NULL,
															" . time() . ")");
        $sql->execute();
		self::TriggerDNSUpdate($domainID);
        self::$editdomain = $domainID;
        return;
    }

    static function SaveDNS() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $dnsrecords = array();
        //Grab form inputs in array and assign them to variables
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainName'))) {
            $domainName = $controller->GetControllerRequest('FORM', 'domainName');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainID'))) {
            $domainID = $controller->GetControllerRequest('FORM', 'domainID');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'ttl'))) {
            $ttl = $controller->GetControllerRequest('FORM', 'ttl');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_ttl'))) {
            $original_ttl = $controller->GetControllerRequest('FORM', 'original_ttl');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'target'))) {
            $target = $controller->GetControllerRequest('FORM', 'target');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_target'))) {
            $original_target = $controller->GetControllerRequest('FORM', 'original_target');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'type'))) {
            $type = $controller->GetControllerRequest('FORM', 'type');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'delete'))) {
            $delete = $controller->GetControllerRequest('FORM', 'delete');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'hostName'))) {
            $hostName = $controller->GetControllerRequest('FORM', 'hostName');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'priority'))) {
            $priority = $controller->GetControllerRequest('FORM', 'priority');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_priority'))) {
            $original_priority = $controller->GetControllerRequest('FORM', 'original_priority');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'weight'))) {
            $weight = $controller->GetControllerRequest('FORM', 'weight');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_weight'))) {
            $original_weight = $controller->GetControllerRequest('FORM', 'original_weight');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'port'))) {
            $port = $controller->GetControllerRequest('FORM', 'port');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_port'))) {
            $original_port = $controller->GetControllerRequest('FORM', 'original_port');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'newRecords'))) {
            $newRecords = $controller->GetControllerRequest('FORM', 'newRecords');
        }
        //Get all existing records for domain and add the id's to an array
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL");
                $sql->execute();
                while ($rowdns = $sql->fetch()) {
                    $dnsrecords[] = $rowdns['dn_id_pk'];
                }
            }
        }
        //Existing Records
        //Sort through the dns record array by id and update as needed
        foreach ($dnsrecords as $id) {
            if ($delete[$id] == "true") {
                //The record has been marked for deletion, so lets delete it!
                $sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=" . time() . " WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                $sql->execute();
				self::TriggerDNSUpdate($domainID);
                //If deleting an A recod, also delete cnames pointing to it.
                if ($type[$id] == "A") {
                    //$sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=" . time() . " WHERE dn_type_vc='CNAME' AND dn_vhost_fk=".$domainID." AND dn_target_vc='".$target[$id]."' AND dn_deleted_ts IS NULL");
                    //$sql->execute();						
                }
            } else {
                //The record needs updating instead.
                //TTL
                if (isset($ttl[$id]) && !fs_director::CheckForEmptyValue($ttl[$id]) && $ttl[$id] != $original_ttl[$id] && is_numeric($ttl[$id])) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_ttl_in=" . self::CleanRecord($ttl[$id], $type[$id]) . " WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                    $sql->execute();
                }
                //TARGET
                if (isset($target[$id]) && !fs_director::CheckForEmptyValue($target[$id]) && $target[$id] != $original_target[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_target_vc='" . self::CleanRecord($target[$id], $type[$id]) . "' WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                    $sql->execute();
                }
                //PRIORITY
                if (isset($priority[$id]) && !fs_director::CheckForEmptyValue($priority[$id]) && $priority[$id] != $original_priority[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_priority_in=" . self::CleanRecord($priority[$id], $type[$id]) . " WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                    $sql->execute();
                }
                //WEIGHT
                if (isset($weight[$id]) && !fs_director::CheckForEmptyValue($weight[$id]) && $weight[$id] != $original_weight[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_weight_in=" . self::CleanRecord($weight[$id], $type[$id]) . " WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                    $sql->execute();
                }
                //PORT
                if (isset($port[$id]) && !fs_director::CheckForEmptyValue($port[$id]) && $port[$id] != $original_port[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_port_in=" . self::CleanRecord($port[$id], $type[$id]) . " WHERE dn_id_pk = " . $id . " AND dn_deleted_ts IS NULL");
                    $sql->execute();
                }
				//Flag the record for needing updating on next daemon run...
				self::TriggerDNSUpdate($domainID);
            }
        }
        //NEW Records
        //Find all new records in post array
        if (isset($newRecords) && !fs_director::CheckForEmptyValue($newRecords)) {
            $numnew = $newRecords;
            $id = 1;
            while ($numnew >= $id) {
                if (isset($type['new_' . $id]) && !fs_director::CheckForEmptyValue($target['new_' . $id])) {
                    if ($delete['new_' . $id] != "true" && !fs_director::CheckForEmptyValue($type['new_' . $id])) {
                        if (isset($hostName['new_' . $id]) && !fs_director::CheckForEmptyValue($hostName['new_' . $id])) {
                            $hostName_new = "'" . self::CleanRecord($hostName['new_' . $id], $type['new_' . $id]) . "'";
                        } else {
                            $hostName_new = "NULL";
                        }
                        if (isset($type['new_' . $id]) && !fs_director::CheckForEmptyValue($type['new_' . $id])) {
                            $type_new = "'" . $type['new_' . $id] . "'";
                        } else {
                            $type_new = "NULL";
                        }
                        if (isset($ttl['new_' . $id]) && !fs_director::CheckForEmptyValue($ttl['new_' . $id])) {
                            $ttl_new = self::CleanRecord($ttl['new_' . $id], $type['new_' . $id]);
                        } else {
                            $ttl_new = "NULL";
                        }
                        if (isset($target['new_' . $id]) && !fs_director::CheckForEmptyValue($target['new_' . $id])) {
                            //If Custom IP addresses are not allowed.
                            if ($type['new_' . $id] == 'A') {
                                if (ctrl_options::GetOption('custom_ip') == strtolower("false")) {
                                    if (!fs_director::CheckForEmptyValue(ctrl_options::GetOption('server_ip'))) {
                                        $target['new_' . $id] = ctrl_options::GetOption('server_ip');
                                    } else {
                                        $target['new_' . $id] = $_SERVER["SERVER_ADDR"];
                                    }
                                }
                            }
                            $target_new = "'" . self::CleanRecord($target['new_' . $id], $type['new_' . $id]) . "'";
                        } else {
                            $target_new = "NULL";
                        }
                        if (isset($priority['new_' . $id]) && !fs_director::CheckForEmptyValue($priority['new_' . $id])) {
                            $priority_new = self::CleanRecord($priority['new_' . $id], $type['new_' . $id]);
                        } else {
                            $priority_new = "NULL";
                        }
                        if (isset($weight['new_' . $id]) && !fs_director::CheckForEmptyValue($weight['new_' . $id])) {
                            $weight_new = self::CleanRecord($weight['new_' . $id], $type['new_' . $id]);
                        } else {
                            $weight_new = "NULL";
                        }
                        if (isset($port['new_' . $id]) && !fs_director::CheckForEmptyValue($port['new_' . $id])) {
                            $port_new = self::CleanRecord($port['new_' . $id], $type['new_' . $id]);
                        } else {
                            $port_new = "NULL";
                        }
                        $sql = $zdbh->prepare("INSERT INTO x_dns (dn_acc_fk,
															dn_name_vc,
															dn_vhost_fk,
															dn_type_vc,
															dn_host_vc,
															dn_ttl_in,
															dn_target_vc,
															dn_priority_in,
															dn_weight_in,
															dn_port_in,
															dn_created_ts) VALUES (
															" . $currentuser['userid'] . ",
															'" . $domainName . "',
															" . $domainID . ",
															" . $type_new . ",
															" . $hostName_new . ",
															" . $ttl_new . ",
															" . $target_new . ",
															" . $priority_new . ",
															" . $weight_new . ",
															" . $port_new . ",
															" . time() . ")");
                        $sql->execute();
						//Flag the record for needing updating on next daemon run...
						self::TriggerDNSUpdate($domainID);
                    }
                }
                $id++;
            }
        }
        return;
    }

    //Use the same method as above and check for input errors doSaveDNS() uses before continuing.
    static function CheckForErrors() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $dnsrecords = array();
        //Grab form inputs in array and assign them to variables
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainName'))) {
            $domainName = $controller->GetControllerRequest('FORM', 'domainName');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'domainID'))) {
            $domainID = $controller->GetControllerRequest('FORM', 'domainID');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'ttl'))) {
            $ttl = $controller->GetControllerRequest('FORM', 'ttl');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_ttl'))) {
            $original_ttl = $controller->GetControllerRequest('FORM', 'original_ttl');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'target'))) {
            $target = $controller->GetControllerRequest('FORM', 'target');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_target'))) {
            $original_target = $controller->GetControllerRequest('FORM', 'original_target');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'type'))) {
            $type = $controller->GetControllerRequest('FORM', 'type');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'delete'))) {
            $delete = $controller->GetControllerRequest('FORM', 'delete');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'hostName'))) {
            $hostName = $controller->GetControllerRequest('FORM', 'hostName');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'priority'))) {
            $priority = $controller->GetControllerRequest('FORM', 'priority');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_priority'))) {
            $original_priority = $controller->GetControllerRequest('FORM', 'original_priority');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'weight'))) {
            $weight = $controller->GetControllerRequest('FORM', 'weight');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_weight'))) {
            $original_weight = $controller->GetControllerRequest('FORM', 'original_weight');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'port'))) {
            $port = $controller->GetControllerRequest('FORM', 'port');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'original_port'))) {
            $original_port = $controller->GetControllerRequest('FORM', 'original_port');
        }
        if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'newRecords'))) {
            $newRecords = $controller->GetControllerRequest('FORM', 'newRecords');
        }
        //Get all existing records for domain and add the id's to an array
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=" . $currentuser['userid'] . " AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL");
                $sql->execute();
                while ($rowdns = $sql->fetch()) {
                    $dnsrecords[] = $rowdns['dn_id_pk'];
                }
            }
        }
        //Existing Records
        //Sort through the dns record array by id and update as needed
        foreach ($dnsrecords as $id) {
            if ($delete[$id] == "false") {
                //TTL
                if (isset($ttl[$id]) && !fs_director::CheckForEmptyValue($ttl[$id]) && $ttl[$id] != $original_ttl[$id]) {
                    if (!is_numeric($ttl[$id])) {
                        self::$ttl_error = TRUE;
                        return FALSE;
                    }
                }
                //TARGET
                if (isset($target[$id]) && !fs_director::CheckForEmptyValue($target[$id]) && $target[$id] != $original_target[$id]) {
                    if ($type[$id] == "A") {
                        if (!self::IsValidIPv4($target[$id])) {
                            self::$invalidIPv4_error = TRUE;
                            return FALSE;
                        }
                    } elseif ($type[$id] == "AAAA") {
                        if (!self::IsValidIPv6($target[$id])) {
                            self::$invalidIPv6_error = TRUE;
                            return FALSE;
                        }
                    } elseif ($type[$id] == "TXT") {
                        
                    } elseif ($type[$id] == "SPF") {
                        
                    } else {
                        if (!self::IsValidIP($target[$id])) {
                            if (!self::IsValidDomainName($target[$id])) {
                                self::$invalidDomainName_error = TRUE;
                                return FALSE;
                            }
                        }
                        if (!self::IsValidDomainName($target[$id])) {
                            if (!self::IsValidIP($target[$id])) {
                                self::$invalidIP_error = TRUE;
                                return FALSE;
                            }
                        }
                    }
                }
                //PRIORITY
                if (isset($priority[$id]) && !fs_director::CheckForEmptyValue($priority[$id]) && $priority[$id] != $original_priority[$id]) {
                    if (!is_numeric($priority[$id])) {
                        self::$priorityNumeric_error = TRUE;
                        return FALSE;
                    }
                    if ($priority[$id] < 0 || $priority[$id] > 65535) {
                        self::$priorityRange_error = TRUE;
                        return FALSE;
                    }
                }
                //WEIGHT
                if (isset($weight[$id]) && !fs_director::CheckForEmptyValue($weight[$id]) && $weight[$id] != $original_weight[$id]) {
                    if (!is_numeric($weight[$id])) {
                        self::$weightNumeric_error = TRUE;
                        return FALSE;
                    }
                    if ($weight[$id] < 0 || $weight[$id] > 65535) {
                        self::$weightRange_error = TRUE;
                        return FALSE;
                    }
                }
                //PORT
                if (isset($port[$id]) && !fs_director::CheckForEmptyValue($port[$id]) && $port[$id] != $original_port[$id]) {
                    if (!is_numeric($port[$id])) {
                        self::$portNumeric_error = TRUE;
                        return FALSE;
                    }
                    if ($port[$id] < 0 || $port[$id] > 65535) {
                        self::$portRange_error = TRUE;
                        return FALSE;
                    }
                }
            }
        }
        //NEW Records
        //Find all new records in post array
        if (isset($newRecords) && !fs_director::CheckForEmptyValue($newRecords)) {
            $numnew = $newRecords;
            $id = 1;
            while ($numnew >= $id) {
                if (isset($type['new_' . $id])) {
                    if ($delete['new_' . $id] == "false" && !fs_director::CheckForEmptyValue($type['new_' . $id])) {
                        //HOSTNAME
                        if (isset($hostName['new_' . $id]) && !fs_director::CheckForEmptyValue($hostName['new_' . $id]) && $hostName['new_' . $id] != "@") {
							
							//Check that hostname does not already exist.
							$hostname = $zdbh->query("SELECT * FROM x_dns WHERE dn_host_vc='" . $hostName['new_' . $id] . "' AND dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL")->Fetch();
							if ($hostname){
								self::$hostname_error = TRUE;
								return FALSE;
							}
							
                            if ($type['new_' . $id] != "SRV") {
                                if (!self::IsValidDomainName($hostName['new_' . $id])) {
                                    return FALSE;
                                }
                            }
                        }
                        //TTL
                        if (isset($ttl['new_' . $id]) && !fs_director::CheckForEmptyValue($ttl['new_' . $id])) {
                            if (!is_numeric($ttl['new_' . $id])) {
                                self::$ttl_error = TRUE;
                                return FALSE;
                            }
                        }
                        //TARGET
                        if (isset($target['new_' . $id]) && !fs_director::CheckForEmptyValue($target['new_' . $id])) {
                            if ($type['new_' . $id] == "A") {
                                if (!self::IsValidIPv4($target['new_' . $id])) {
                                    self::$invalidIPv4_error = TRUE;
                                    return FALSE;
                                }
                            } elseif ($type['new_' . $id] == "AAAA") {
                                if (!self::IsValidIPv6($target['new_' . $id])) {
                                    self::$invalidIPv6_error = TRUE;
                                    return FALSE;
                                }
                            } elseif ($type['new_' . $id] == "TXT") {
                                
                            } elseif ($type['new_' . $id] == "SPF") {
                                
                            } else {
                                if (!self::IsValidIP($target['new_' . $id])) {
                                    if (!self::IsValidDomainName($target['new_' . $id])) {
                                        self::$invalidDomainName_error = TRUE;
                                        return FALSE;
                                    }
                                }
                                if (!self::IsValidDomainName($target['new_' . $id])) {
                                    if (!self::IsValidIP($target['new_' . $id])) {
                                        self::$invalidIP_error = TRUE;
                                        return FALSE;
                                    }
                                }
                            }
                        }
                        //PRIORITY			
                        if (isset($priority['new_' . $id]) && !fs_director::CheckForEmptyValue($priority['new_' . $id])) {
                            if (!is_numeric($priority['new_' . $id])) {
                                self::$priorityNumeric_error = TRUE;
                                return FALSE;
                            }
                            if ($priority['new_' . $id] < 0 || $priority['new_' . $id] > 65535) {
                                self::$priorityRange_error = TRUE;
                                return FALSE;
                            }
                        }
                        //WEIGHT
                        if (isset($weight['new_' . $id]) && !fs_director::CheckForEmptyValue($weight['new_' . $id])) {
                            if (!is_numeric($weight['new_' . $id])) {
                                self::$weightNumeric_error = TRUE;
                                return FALSE;
                            }
                            if ($weight['new_' . $id] < 0 || $weight['new_' . $id] > 65535) {
                                self::$weightRange_error = TRUE;
                                return FALSE;
                            }
                        }
                        //PORT
                        if (isset($port['new_' . $id]) && !fs_director::CheckForEmptyValue($port['new_' . $id])) {
                            if (!is_numeric($port['new_' . $id])) {
                                self::$portNumeric_error = TRUE;
                                return FALSE;
                            }
                            if ($port['new_' . $id] < 0 || $port['new_' . $id] > 65535) {
                                self::$portRange_error = TRUE;
                                return FALSE;
                            }
                        }
                    }
                }
                $id++;
            }
        }
        return true;
    }

    static function IsValidDomainName($a) {
        if ($a != "@") {
            $part = explode(".", $a);
            foreach ($part as $check) {
                if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $check) || preg_match('/-$/', $check)) {
                    return false;
                }
            }
        }
        return true;
    }

    static function CleanRecord($data, $type) {
        $data = trim($data);
        if ($type == 'SPF' || $type == 'TXT') {
            $data = str_replace('"', '', $data);
            $data = str_replace('\'', '', $data);
            $data = addslashes($data);
        } else {
            $data = str_replace(' ', '', $data);
        }
        $data = strtolower($data);
        return $data;
    }

    static function IsTypeAllowed($type) {
        global $zdbh;
        $record_types = ctrl_options::GetOption('allowed_types');
        $record_types = explode(" ", $record_types);
        if (in_array($type, $record_types)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIP($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIPv4($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIPv6($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ttl_error)) {
            return ui_sysmessage::shout(ui_language::translate("TTL must be a numeric value."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$hostname_error)) {
            return ui_sysmessage::shout(ui_language::translate("Hostnames must be unique."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$invalidIPv4_error)) {
            return ui_sysmessage::shout(ui_language::translate("IP Address is not a valid IPV4 address."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$invalidIPv6_error)) {
            return ui_sysmessage::shout(ui_language::translate("IP Address is not a valid IPV6 address"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$invalidDomainName_error)) {
            return ui_sysmessage::shout(ui_language::translate("An invalid domain name character was entered. Domain names are limited to alphanumeric characters and hyphens."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$invalidIP_error)) {
            return ui_sysmessage::shout(ui_language::translate("Target is not a valid IP address"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$priorityNumeric_error)) {
            return ui_sysmessage::shout(ui_language::translate("Priority must be a numeric value."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$priorityRange_error)) {
            return ui_sysmessage::shout(ui_language::translate("The priority of a dns record must be a numeric value between 0 and 65535"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$weightNumeric_error)) {
            return ui_sysmessage::shout(ui_language::translate("Weight must be a numeric value."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$weightRange_error)) {
            return ui_sysmessage::shout(ui_language::translate("The weight of a dns record must be a numeric value between 0 and 65535"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$portNumeric_error)) {
            return ui_sysmessage::shout(ui_language::translate("PORT must be a numeric value."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$portRange_error)) {
            return ui_sysmessage::shout(ui_language::translate("The port of a dns record must be a numeric value between 0 and 65535"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your DNS have been saved successfully!"), "zannounceok");
        }
        return;
    }

    static function getModuleName() {
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    static function getModuleDesc() {
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

    static function TriggerDNSUpdate($id) {
		global $zdbh;
        global $controller;
        $GetRecords = ctrl_options::GetOption('dns_hasupdates');
		$records = explode(",", $GetRecords);
		foreach ($records as $record){
			$RecordArray[] = $record;
		}
		if (!in_array($id, $RecordArray)){	
        	$newlist = $GetRecords . "," . $id;
	        $newlist = str_replace(",,", ",", $newlist);
	        $sql = "UPDATE x_settings SET so_value_tx='" . $newlist . "' WHERE so_name_vc='dns_hasupdates'";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
	        return true;
		}
    }

	static function CheckZoneRecord($domainID){
		global $zdbh;
		$hasrecords=false;
            $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk=" . $domainID . "  AND dn_deleted_ts IS NULL";
            if ($numrows = $zdbh->query($sql)) {
                if ($numrows->fetchColumn() <> 0) {
				$hasrecords=true;
                    $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL ORDER BY dn_type_vc");
                    $sql->execute();
                    $domain = $zdbh->query("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=" . $domainID . " AND dn_deleted_ts IS NULL")->Fetch();
					

                    $zonecheck_file = (ctrl_options::GetOption('temp_dir')) . $domain['dn_name_vc'] . ".txt";
                    $checkline = "$" . "TTL 10800" . fs_filehandler::NewLine();
                    $checkline .= "@ IN SOA " . $domain['dn_name_vc'] . ".    ";
                    $checkline .= "postmaster@" . $domain['dn_name_vc'] . ". (" . fs_filehandler::NewLine();
                    $checkline .= "                       " . time() . "	;serial" . fs_filehandler::NewLine();
                    $checkline .= "                       " . ctrl_options::GetOption('refresh_ttl') . "      ;refresh after 6 hours" . fs_filehandler::NewLine();
                    $checkline .= "                       " . ctrl_options::GetOption('retry_ttl') . "       ;retry after 1 hour" . fs_filehandler::NewLine();
                    $checkline .= "                       " . ctrl_options::GetOption('expire_ttl') . "     ;expire after 1 week" . fs_filehandler::NewLine();
                    $checkline .= "                       " . ctrl_options::GetOption('minimum_ttl') . " )    ;minimum TTL of 1 day" . fs_filehandler::NewLine();
                    while ($rowdns = $sql->fetch()) {
                        if ($rowdns['dn_type_vc'] == "A") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		A		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "AAAA") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		AAAA		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "CNAME") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		CNAME		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "MX") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		MX		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "TXT") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "SRV") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SRV		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_weight_in'] . "	" . $rowdns['dn_port_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "SPF") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SPF		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "NS") {
                            $checkline .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		NS		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                    }
					fs_filehandler::UpdateFile($zonecheck_file, 0777, $checkline);
                }
            }
			if ($hasrecords==true){
				//Check the temp zone record for errors
				if (file_exists($zonecheck_file)){
					ob_start();
		        	system(ctrl_options::GetOption('named_checkzone') . " " . $domain['dn_name_vc'] . " " . $zonecheck_file, $retval);
		        	$content_grabbed = ob_get_contents();
		        	ob_end_clean();
					unlink($zonecheck_file);
					if ($retval == 0){
						//Syntax check passed.
		    			return $content_grabbed;
					} else {
						//Syntax ERROR.
						return $content_grabbed;
					}
				}
			}
	
	}
	


}

?>