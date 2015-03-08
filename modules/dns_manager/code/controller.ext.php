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
 */
class module_controller extends ctrl_module
{

    static $editdomain;
    static $showform;
    static $ResultOk;
    static $ResultErr;

    private function SetError($ErrorText)
    {
        if (empty(self::$ResultErr))
            self::$ResultErr = $ErrorText;
    }

    /* Load DNS CSS and JS files */

    static function getInit()
    {
        global $controller;
        $line = '<link rel="stylesheet" type="text/css" href="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/dns.css">';
        $line .= '<script type="text/javascript" src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/dns.js"></script>';
        return $line;
    }

    /*
     * Determine which DNS page to show
     * Domain List or DNS Records
     */

    static function getRecordAction()
    {
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

            $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL";
            $numrows = $zdbh->prepare($sql);
            $numrows->bindParam(':userid', $currentuser['userid']);
            $numrows->bindParam(':domainID', $domainID);

            if ($numrows->execute()) {
                if ($numrows->fetchColumn() == 0) {
                    $display = self::DisplayDefaultRecords();
                } else {
                    $display = self::DisplayRecords();
                }
            }
        }
        return $display;
    }

    /*
     * Allow user to Create Initial Domain DNS records for the First time
     */

    static function DisplayDefaultRecords()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = "";
        $line .= "<div class=\"zgrid_wrapper\">";


        $line .= "<div id=\"dnsTitle\" class=\"account accountTitle\">";
        $line .= "<div class=\"content\"><h2>" . ui_language::translate("Create Default DNS Records") . "</h2>";
        $line .= "" . ui_language::translate("No records were found for this domain.  Click the button below to set up your domain records for the first time") . "";
        $line .= "<div>";
        $line .= "<div class=\"actions\"><a class=\"back\" href=\"./?module=" . $controller->GetControllerRequest('URL', 'module') . "\">Domain List</a></div>";
        $line .= "</div><br class=\"clear\">";
        $line .= '</div>';
        $line .= '</div>';


        $line .= "<form action=\"./?module=dns_manager&action=CreateDefaultRecords\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<td>";
        $line .= '<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-pencil"></i> ' . ui_language::translate("Create Records") . '</button>';
        $line .= "</td>";
        $line .= "</tr>";
        $line .= "</table>";
        $line .= "<input type=\"hidden\" name=\"inDomain\" value =\"" . $controller->GetControllerRequest('FORM', 'inDomain') . "\" />";
        $line .= "<input type=\"hidden\" name=\"inUserID\" value =\"" . $currentuser['userid'] . "\" />";
        $line .= self::getCSFR_Tag();
        $line .= '</form>';
        $line .= '</div>';
        return $line;
    }

    static function DnsRecordField($type, $ttl, $description, $userID, $domainID)
    {
        global $zdbh;
        global $controller;
        /* Begin DNS records */
        if (self::IsTypeAllowed($type)) {
            if ($type === 'A') {
                $activeCss = 'active';
                if (ctrl_options::GetSystemOption('custom_ip') == 'false') {
                    $custom_ip = "READONLY";
                } else {
                    $custom_ip = NULL;
                }
            } else {
                $activeCss = '';
                $custom_ip = NULL;
            }

            $line = '<!-- ' . $type . ' RECORDS -->';
            $line .= '<div class="tab-pane ' . $activeCss . '" id="type' . $type . '">';
            $line .= '<div class="description">' . $description . '</div>';
            $line .= '<div class="header row">';
            $line .= '<div class="hostName"><label class="enableToolTip">' . ui_language::translate('Host Name') . '</label></div>';
            $line .= '<div class="TTL"><label class="enableToolTip">TTL</label></div>';
            $line .= '<div class="in">&nbsp;</div>';
            $line .= '<div class="type">&nbsp;</div>';
            if ($type === 'MX') {
                $line .= '<div class="priority"><label class="enableToolTip">' . ui_language::translate('Priority') . '</label></div>';
            } elseif ($type === 'SRV') {
                $line .= '<div class="priority"><label class="enableToolTip">' . ui_language::translate('Priority') . '</label></div>';
                $line .= '<div class="weight"><label class="enableToolTip">' . ui_language::translate('Weight') . '</label></div>';
                $line .= '<div class="port"><label class="enableToolTip">' . ui_language::translate('Port') . '</label></div>';
            }
            $line .= '<div class="target"><label class="enableToolTip">' . ui_language::translate('Target') . '</label></div>';
            $line .= '<div class="actions"><label>' . ui_language::translate('Actions') . '</label></div>';
            $line .= '<br>';
            $line .= '</div>';

            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=:userid AND dn_type_vc=:type AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
            $sql->bindParam(':type', $type);
            $sql->bindParam(':userid', $userID);
            $sql->bindParam(':domainID', $domainID);
            $sql->execute();

            while ($rowdns = $sql->fetch()) {
                $line .= '<div class="dnsRecord row">';
                $line .= '<div class="hostName"><span>' . $rowdns['dn_host_vc'] . '</span></div>';
                $line .= '<div class="TTL">';
                $line .= '<input name="ttl[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_ttl_in'] . '" class="input-small" type="text">';
                $line .= '<input name="original_ttl[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_ttl_in'] . '" type="hidden"></div>';
                $line .= '<div class="in">IN</div>';
                $line .= '<div class="type">' . $type . '</div>';

                if ($type === 'MX') {
                    $line .= '<div class="priority"><input name="priority[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_priority_in'] . '" type="text" class="input-small"><input name="original_priority[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_priority_in'] . '" type="hidden"></div>';
                } elseif ($type === 'SRV') {
                    $line .= '<div class="priority"><input name="priority[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_priority_in'] . '" class="input-small" type="text"><input name="original_priority[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_priority_in'] . '" type="hidden"></div>';
                    $line .= '<div class="weight"><input name="weight[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_weight_in'] . '" class="input-small" type="text"><input name="original_weight[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_weight_in'] . '" type="hidden"></div>';
                    $line .= '<div class="port"><input name="port[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_port_in'] . '" type="text" class="input-small"><input name="original_port[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_port_in'] . '" type="hidden"></div>';
                }
                $line .= '<div class="target">';
                $line .= '<input name="target[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_target_vc'] . '" class="input-small" type="text" ' . $custom_ip . '>';
                $line .= '<input name="original_target[' . $rowdns['dn_id_pk'] . ']" value="' . $rowdns['dn_target_vc'] . '" type="hidden">';
                $line .= '</div>';
                $line .= '<button type="button" class="delete btn btn-danger btn-mini">Delete</button>';
                $line .= '<button type="button" class="undo btn btn-success btn-mini">Undo</button>';
                $line .= '<input name="type[' . $rowdns['dn_id_pk'] . ']" value="' . $type . '" type="hidden">';
                $line .= '<input class="delete" name="delete[' . $rowdns['dn_id_pk'] . ']" value="false" type="hidden">';
                $line .= '<br>';
                $line .= '</div>';
            }
            $line .= '<div class="add row"><button type="submit" class="add-row btn btn-primary"><i class="glyphicon glyphicon-pencil"></i> ' . ui_language::translate("Add New Record") . '</button>

           <button type="submit" class="save disabled btn btn-primary btn-default pull-right">' . ui_language::translate("Save") . '</button></div>';

            // New Record Template
            $line .= '<div class="newRecord row" style="display: none">';
            $line .= '<div class="hostName"><input name="proto_hostName" class="input-small" type="text" placeholder="' . ui_language::translate('Host Name') . '"></div>';

            $line .= '<div class="TTL"><input name="proto_ttl" value="' . $ttl . '" class="input-small" type="text" placeholder="' . ui_language::translate('TTL') . '"></div>';
            $line .= '<div class="in">IN</div>';
            $line .= '<div class="type">' . $type . '</div>';
            if ($type === 'MX') {
                $line .= '<div class="priority"><input name="proto_priority" class="input-small" type="text" placeholder="' . ui_language::translate('Priority') . '"></div>';
            } elseif ($type === 'SRV') {
                $line .= '<div class="priority"><input name="proto_priority" class="input-small" type="text" placeholder="' . ui_language::translate('Priority') . '"></div>';
                $line .= '<div class="weight"><input name="proto_weight" class="input-small" type="text" placeholder="' . ui_language::translate('Weight') . '"></div>';
                $line .= '<div class="port"><input name="proto_port" class="input-small" type="text" placeholder="' . ui_language::translate('Port') . '"></div>';
            }

            $line .= '<div class="target"><input name="proto_target" class="input-small" type="text" placeholder="' . ui_language::translate('Target') . '"></div>';
            $line .= '<input class="delete" name="proto_delete" value="false" type="hidden"><button type="button" class="delete btn btn-danger btn-mini">Delete</button><input name="proto_type" value="' . $type . '" type="hidden">';
            $line .= '</div>';
            $line .= '</div> <!-- END ' . $type . ' RECORDS -->';
            return $line;
        }
    }

    /*
     * Build and show DNS Record HTML output
     * TODO: Break into smaller Functions
     */

    static function DisplayRecords()
    {
        //Post Debug
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if (fs_director::CheckForEmptyValue(self::$editdomain)) {
            $domainID = $controller->GetControllerRequest('FORM', 'domainID');
        } else {
            $domainID = self::$editdomain;
        }
        $numrows2 = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_id_pk=:domainID AND vh_type_in !=2 AND vh_deleted_ts IS NULL");
        $numrows2->bindParam(':domainID', $domainID);
        $numrows2->execute();
        $domain = $numrows2->fetch();

        // Check DNS Zone File for Errors
        $zone_message = self::CheckZoneRecord($domainID);
        $zonecheck_file = ctrl_options::GetSystemOption('temp_dir') . $domain['vh_name_vc'] . ".txt";
        $zone_message = str_replace($zonecheck_file, '', $zone_message);
        if (strstr(strtoupper($zone_message), "OK")) {
            if (substr_count($zone_message, ":") >= 2) {
                $zone_error_message = '<font color="orange">' . ui_language::translate('Your DNS zone has been loaded, but with errors. Some features may not work until corrected.') . '</font>';
            } else {
                $zone_error_message = '<font color="green">' . ui_language::translate('Your DNS zone has been loaded without errors.') . '</font>';
            }
            $zone_status = '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/up.png">';
        } else {
            $zone_error_message = '<font color="red">' . ui_language::translate('Errors detected have prevented your DNS zone from being loaded. Please correct the error(s) listed below. Until these errors are fixed, your DNS will not work.') . '</font>';
            $zone_status = '<img src="modules/' . $controller->GetControllerRequest('URL', 'module') . '/assets/down.png">';
        }

        // Top Edit buttons
        $line = '<!-- DNS FORM -->';
        $line .= '<div id="dnsTitle" class="account accountTitle">';
        $line .= '<div class="content"><h4>' . ui_language::translate('DNS records for') . ':  <a href="http://' . $domain['vh_name_vc'] . '" target="_blank">' . $domain['vh_name_vc'] . '</a></h4>';
        $line .= '<div>';
        $line .= '<div class="actions"><a class="undo disabled" rel="popover" data-title="Undo" data-content="Undo all un-saved changes." data-placement="top">' . ui_language::translate('Undo Changes') . '</a><a class="save disabled">' . ui_language::translate('Save Changes') . '</a><a class="back" href="./?module=' . $controller->GetControllerRequest('URL', 'module') . '">' . ui_language::translate('Domain List') . '</a></div>';
        $line .= '</div><br class="clear">';
        $line .= '</div>';
        $line .= '</div>';

        $line .= '<form action="./?module=dns_manager&action=SaveDNS" method="post">';
        $line .= '<input id="domainName" name="domainName" value="' . $domain['vh_name_vc'] . '" type="hidden">';
        $line .= '<input id="domainID" name="domainID" value="' . $domain['vh_id_pk'] . '" type="hidden">';

        $line .= '<!-- TABS -->';
        $line .= '<div id="dnsRecords">';

        $line .= '<ul class="nav nav-tabs">';
        if (self::IsTypeAllowed('A')) {
            $line .= '    <li class="active"><a href="#typeA" data-toggle="tab">A</a></li>';
        }
        if (self::IsTypeAllowed('AAAA')) {
            $line .= '    <li><a href="#typeAAAA" data-toggle="tab">AAAA</a></li>';
        }
        if (self::IsTypeAllowed('CNAME')) {
            $line .= '    <li><a href="#typeCNAME" data-toggle="tab">CNAME</a></li>';
        }
        if (self::IsTypeAllowed('MX')) {
            $line .= '    <li><a href="#typeMX" data-toggle="tab">MX</a></li>';
        }
        if (self::IsTypeAllowed('TXT')) {
            $line .= '    <li><a href="#typeTXT" data-toggle="tab">TXT</a></li>';
        }
        if (self::IsTypeAllowed('SRV')) {
            $line .= '    <li><a href="#typeSRV" data-toggle="tab">SRV</a></li>';
        }
        if (self::IsTypeAllowed('SPF')) {
            $line .= '    <li><a href="#typeSPF" data-toggle="tab">SPF</a></li>';
        }
        if (self::IsTypeAllowed('NS')) {
            $line .= '    <li><a href="#typeNS" data-toggle="tab">NS</a></li>';
        }
        $line .= '</ul>';
        $line .= '<!-- END TABS -->';

        $line .= '<div class="tab-content">';

        $aDescription = ui_language::translate("The A record contains an IPv4 address. Its target is an IPv4 address, e.g. '192.168.1.1'.");
        $aaaaDescription = ui_language::translate("The AAAA record contains an IPv6 address. Its target is an IPv6 address, e.g. '2607:fe90:2::1'.");
        $cnameDescription = ui_language::translate("The CNAME record specifies the canonical name of a record. Its target is a fully qualified domain name, e.g.
'webserver-01.example.com'.");
        $mxDescription = ui_language::translate("The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535.  Its target is a fully qualified domain name, e.g. 'mail.example.com'.");
        $txtDescription = ui_language::translate("The TXT field can be used to attach textual data to a domain.");
        $srvDescription = ui_language::translate("SRV records can be used to encode the location and port of services on a domain name.  Its target is a fully qualified domain name, e.g. 'host.example.com'.");
        $spfDescription = ui_language::translate("SPF records is used to store Sender Policy Framework details.  Its target is a text string, e.g.<br>'v=spf1 a:192.168.1.1 include:example.com mx ptr -all' (Click <a href=\"http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/\" target=\"_blank\">HERE</a> for the Microsoft SPF Wizard.)");
        $nsDescription = ui_language::translate("Nameserver record. Specifies nameservers for a domain. Its target is a fully qualified domain name, e.g.  'ns1.example.com'.  The records should match what the domain name has registered with the internet root servers.");

        $tts = 86400;
        $line .= self::DnsRecordField('A', $tts, $aDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('AAAA', $tts, $aaaaDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('CNAME', $tts, $cnameDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('MX', $tts, $mxDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('TXT', $tts, $txtDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('SRV', $tts, $srvDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('SPF', $tts, $spfDescription, $currentuser['userid'], $domainID);
        $line .= self::DnsRecordField('NS', $tts, $nsDescription, $currentuser['userid'], $domainID);

        $line .= '<input name="newRecords" value="0" type="hidden">';
        $line .= '</div> <!-- END TABS CONTENT -->';
        /* END TABS SECTION */
        $line .= '</div> <!-- END TABS -->';

        // Bottom Edit buttons
        $line .= "<div id=\"dnsTitle\" class=\"account accountTitle\">";
        $line .= "<div class=\"content\">";
        $line .= "<div>";
        $line .= "<div class=\"actions\"><a class=\"undo disabled\">" . ui_language::translate("Undo Changes") . "</a><a class=\"save disabled\">" . ui_language::translate("Save Changes") . "</a><a class=\"back\" href=\"./?module=" . $controller->GetControllerRequest('URL', 'module') . "\">" . ui_language::translate("Domain List") . "</a></div>";
        $line .= "</div><br class=\"clear\">";
        $line .= '</div>';
        $line .= self::getCSFR_Tag();
        $line .= "</form>";
        $line .= "<!-- END DNS FORM -->";
        $line .= "<div class=\"zgrid_wrapper\">";
        $line .= "<h2>DNS Status for domain: " . $domain['vh_name_vc'] . "</h2>";
        $line .= "<table class=\"none\" cellpadding=\"0\" cellspacing=\"0\"><tr valign=\"top\"><td>";
        $line .= $zone_status;
        $line .= "</td><td>" . $zone_error_message . "<br><br>" . ui_language::translate("Please note that changes to your zone records can take up to 24 hours before they become 'live'.") . "<br><br><b>" . ui_language::translate("Output of DNS zone checker:") . "</b><br>";
        $line .= $zone_message;
        $line .= "</td></tr></table>";
        $line .= '</div>';
        $line .= '</div>';


        $line .='
<div id="dns-modal" class="modal fade in" tabindex="-1" role="dialog" style="display: none;">
   <div class="modal-dialog">
       <div class="alert alert-block alert-error fade in">
           <h4 class="alert-heading">Oh snap! You got an error!</h4>
           <p>Change this and that and try again. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum.</p>
           <p>
           <a class="btn btn-danger" href="#" data-dismiss="modal">Ok</a>
           </p>
       </div><!-- /.modal-content -->
   </div><!-- /.modal-dalog -->
</div>';

        return $line;
    }

    /*
     * Show Domain Dropdown list/entrance page
     * If no domains exist it shows an Empty list
     * TODO: Tell them to add a domain if no domains exist
     */

    static function DisplayDomains()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $line = '<div class="zgrid_wrapper">';
        $line .= "<h2>" . ui_language::translate("Manage Domains") . "</h2>";
        $line .= "" . ui_language::translate("Choose fom the list of domains below") . "";
        $line .= "<form name=DisplayDNS action=\"./?module=dns_manager&action=DisplayRecords\" method=\"post\">";
        $line .= "<br><br>";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<td><select name=\"inDomain\" id=\"inDomain\">";
        $line .= "<option value=\"\" selected=\"selected\">-- " . ui_language::translate("Select a domain") . " --</option>";
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=:userid AND vh_type_in !=2 AND vh_deleted_ts IS NULL");
        $sql->bindParam(':userid', $currentuser['userid']);
        $sql->execute();
        while ($rowdomains = $sql->fetch()) {
            $line .= "<option value=\"" . $rowdomains['vh_id_pk'] . "\">" . $rowdomains['vh_name_vc'] . "</option>";
        }
        $line .= "</select></td>";
        $line .= "<td>";
        $line .= '<button type="submit" class="btn btn-large btn-primary" name="inSelect" value="' . $rowdomains['vh_id_pk'] . '"><i class="glyphicon glyphicon-pencil"></i>  ' . ui_language::translate("Edit") . '</button>';
        $line .= '</td>';
        $line .= '</tr>';
        $line .= '</table>';
        $line .= self::getCSFR_Tag();
        $line .= '</form>';
        $line .= '<p>&nbsp;</p>';
        $line .= '</div>';
        return $line;
    }

    static function doEditClient()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk=:userid AND ac_deleted_ts IS NULL");
        $sql->bindParam(':userid', $currentuser['userid']);
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

    static function doDisplayRecords()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        self::$editdomain = $controller->GetControllerRequest('FORM', 'inDomain');
        return;
    }

    static function doSaveDNS()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        if (!fs_director::CheckForEmptyValue(self::CheckForErrors())) {
            self::SaveDNS();
            //self::WriteRecord();
            self::$ResultOk = 'Changes to your DNS have been saved successfully!';
            return;
        }
    }

    /**
     * Creates a new DNS record from an array of key value pairs.
     * @param array $rec Array of record properties (uid, domainName, domainID, type, hostName, ttl, target)
     * @return void
     */
    static function createDNSRecord(array $rec)
    {
        global $zdbh;
        $sql = $zdbh->prepare('INSERT INTO x_dns (dn_acc_fk,
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
                           :userid,
                           :domainName,
                           :domainID,
                           :type_new,
                           :hostName_new,
                           :ttl_new,
                           :target_new,
                           :priority_new,
                           :weight_new,
                           :port_new,
                           :time)'
        );

        $priority_new = array_key_exists('priority', $rec) ? $rec['priority'] : 0;
        $weight_new = array_key_exists('weight', $rec) ? $rec['weight'] : 0;
        $port_new = array_key_exists('port', $rec) ? $rec['port'] : 0;
        $time = array_key_exists('time', $rec) ? $rec['time'] : time();

        $sql->bindParam(':userid', $rec['uid']);
        $sql->bindParam(':domainName', $rec['domainName']);
        $sql->bindParam(':domainID', $rec['domainID']);
        $sql->bindParam(':type_new', $rec['type']);
        $sql->bindParam(':hostName_new', $rec['hostName']);
        $sql->bindParam(':ttl_new', $rec['ttl']);
        $sql->bindParam(':target_new', $rec['target']);
        $sql->bindParam(':priority_new', $priority_new);
        $sql->bindParam(':weight_new', $weight_new);
        $sql->bindParam(':port_new', $port_new);
        $sql->bindParam(':time', $time);
        $sql->execute();

        self::TriggerDNSUpdate($rec['domainID']);
    }

    static function doCreateDefaultRecords()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();

        $domainID = $controller->GetControllerRequest('FORM', 'inDomain');
        $numrows = $zdbh->prepare('SELECT * FROM x_vhosts WHERE vh_id_pk=:domainID AND vh_type_in !=2 AND vh_deleted_ts IS NULL');
        $numrows->bindParam(':domainID', $domainID);
        $numrows->execute();
        $domainName = $numrows->fetch();
        $domainName = $domainName['vh_name_vc'];

        $userID = $controller->GetControllerRequest('FORM', 'inUserID');
        if (!fs_director::CheckForEmptyValue(ctrl_options::GetSystemOption('server_ip'))) {
            $targetIP = ctrl_options::GetSystemOption('server_ip');
        } else {
            $targetIP = $_SERVER["SERVER_ADDR"]; //This needs checking on windows 7 we may need to use LOCAL_ADDR :- Sam Mottley
        }
        //Get list of DNS rows to create
        $RowCount = $zdbh->prepare('SELECT count(*) FROM x_dns_create WHERE dc_acc_fk=:userId');
        $RowCount->bindparam(':userId', $userID);
        $RowCount->execute();
        if ($RowCount->fetchColumn() > 0) {
            //The current user have specifics entries, use them only
            $CreateList = $zdbh->prepare('SELECT * FROM x_dns_create WHERE dc_acc_fk=:userId');
            $CreateList->bindparam(':userId', $userID);
            $CreateList->execute();
        } else {
            //no entry specific to this user is present, use default entries (user number = 0)
            $CreateList = $zdbh->query('SELECT * FROM x_dns_create WHERE dc_acc_fk=0');
        }
        while ($CreateItem = $CreateList->fetch()) {
            $Target = str_replace(':IP:', $targetIP, $CreateItem['dc_target_vc']);
            $Target = str_replace(':DOMAIN:', $domainName, $Target);

            $Row = array(
                'uid' => $userID,
                'domainName' => $domainName,
                'domainID' => $domainID,
                'type' => $CreateItem['dc_type_vc'],
                'hostName' => $CreateItem['dc_host_vc'],
                'ttl' => $CreateItem['dc_ttl_in'],
                'target' => $Target);

            if (!empty($CreateItem['dc_priority_in']))
                $Row['priority'] = $CreateItem['dc_priority_in'];

            if (!empty($CreateItem['dc_weight_in']))
                $Row['weight'] = $CreateItem['dc_weight_in'];

            if (!empty($CreateItem['dc_port_in']))
                $Row['port'] = $CreateItem['dc_port_in'];

            self::createDNSRecord($Row);
        }

        self::$editdomain = $domainID;
        return;
    }

    static function SaveDNS()
    {
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
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->bindParam(':domainID', $domainID);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL");
                $sql->bindParam(':userid', $currentuser['userid']);
                $sql->bindParam(':domainID', $domainID);
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
                $sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=:time WHERE dn_id_pk =:id AND dn_deleted_ts IS NULL");
                $sql->bindParam(':id', $id);
                $time = time();
                $sql->bindParam(':time', $time);
                $sql->execute();
                self::TriggerDNSUpdate($domainID);
            } else {
                //The record needs updating instead.
                //TTL
                if (isset($ttl[$id]) && !fs_director::CheckForEmptyValue($ttl[$id]) && $ttl[$id] != $original_ttl[$id] && is_numeric($ttl[$id])) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_ttl_in=:cleanRecord WHERE dn_id_pk = :id AND dn_deleted_ts IS NULL");
                    $sql->bindParam(':id', $id);
                    $cleanRecord = self::CleanRecord($ttl[$id], $type[$id]);
                    $sql->bindParam(':cleanRecord', $cleanRecord);
                    $sql->execute();
                }
                //TARGET
                if (isset($target[$id]) && !fs_director::CheckForEmptyValue($target[$id]) && $target[$id] != $original_target[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_target_vc=:cleanRecord WHERE dn_id_pk = :id AND dn_deleted_ts IS NULL");
                    $sql->bindParam(':id', $id);
                    $cleanRecord = self::CleanRecord($target[$id], $type[$id]);
                    $sql->bindParam(':cleanRecord', $cleanRecord);
                    $sql->execute();
                }
                //PRIORITY
                if (isset($priority[$id]) && !fs_director::CheckForEmptyValue($priority[$id]) && $priority[$id] != $original_priority[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_priority_in=:cleanRecord WHERE dn_id_pk = :id AND dn_deleted_ts IS NULL");
                    $sql->bindParam(':id', $id);
                    $cleanRecord = self::CleanRecord($priority[$id], $type[$id]);
                    $sql->bindParam(':cleanRecord', $cleanRecord);
                    $sql->execute();
                }
                //WEIGHT
                if (isset($weight[$id]) && !fs_director::CheckForEmptyValue($weight[$id]) && $weight[$id] != $original_weight[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_weight_in=:cleanRecord WHERE dn_id_pk = :id AND dn_deleted_ts IS NULL");
                    $sql->bindParam(':id', $id);
                    $cleanRecord = self::CleanRecord($weight[$id], $type[$id]);
                    $sql->bindParam(':cleanRecord', $cleanRecord);
                    $sql->execute();
                }
                //PORT
                if (isset($port[$id]) && !fs_director::CheckForEmptyValue($port[$id]) && $port[$id] != $original_port[$id]) {
                    $sql = $zdbh->prepare("UPDATE x_dns SET dn_port_in=:cleanRecord WHERE dn_id_pk = :id AND dn_deleted_ts IS NULL");
                    $sql->bindParam(':id', $id);
                    $cleanRecord = self::CleanRecord($port[$id], $type[$id]);
                    $sql->bindParam(':cleanRecord', $cleanRecord);
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
                        if (isset($hostName['new_' . $id])) {
                            $hostName_new = self::CleanRecord($hostName['new_' . $id], $type['new_' . $id]);
                        } else {
                            $hostName_new = "NULL";
                        }
                        if (isset($type['new_' . $id]) && !fs_director::CheckForEmptyValue($type['new_' . $id])) {
                            $type_new = $type['new_' . $id];
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
                                if (ctrl_options::GetSystemOption('custom_ip') == 'false') {
                                    if (!fs_director::CheckForEmptyValue(ctrl_options::GetSystemOption('server_ip'))) {
                                        $target['new_' . $id] = ctrl_options::GetSystemOption('server_ip');
                                    } else {
                                        $target['new_' . $id] = $_SERVER["SERVER_ADDR"];
                                    }
                                }
                            }
                            $target_new = self::CleanRecord($target['new_' . $id], $type['new_' . $id]);
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
                           :userid,
                           :domainName,
                           :domainID,
                           :type_new,
                           :hostName_new,
                           :ttl_new,
                           :target_new,
                           :priority_new,
                           :weight_new,
                           :port_new,
                           :time)"
                        );
                        $sql->bindParam(':userid', $currentuser['userid']);
                        $sql->bindParam(':domainName', $domainName);
                        $sql->bindParam(':domainID', $domainID);
                        $sql->bindParam(':type_new', $type_new);
                        $sql->bindParam(':hostName_new', $hostName_new);
                        $sql->bindParam(':ttl_new', $ttl_new);
                        $sql->bindParam(':target_new', $target_new);
                        $sql->bindParam(':priority_new', $priority_new);
                        $sql->bindParam(':weight_new', $weight_new);
                        $sql->bindParam(':port_new', $port_new);
                        $time = time();
                        $sql->bindParam(':time', $time);
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
    static function CheckForErrors()
    {
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
        $numrows = $zdbh->prepare('SELECT COUNT(*) FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL');
        $numrows->bindParam(':userid', $currentuser['userid']);
        $numrows->bindParam(':domainID', $domainID);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare('SELECT dn_id_pk FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL');
                $sql->bindParam(':userid', $currentuser['userid']);
                $sql->bindParam(':domainID', $domainID);
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
                        self::SetError('TTL must be a numeric value.');
                        return FALSE;
                    }
                }

                //TARGET
                if (isset($target[$id]) && !fs_director::CheckForEmptyValue($target[$id]) && $target[$id] != $original_target[$id]) {
                    if ($type[$id] == "A") {
                        if (!self::IsValidIPv4($target[$id])) {
                            self::SetError('IP Address is not a valid IPV4 address.');
                            return FALSE;
                        }
                    } elseif ($type[$id] == "AAAA") {
                        if (!self::IsValidIPv6($target[$id])) {
                            self::SetError('IP Address is not a valid IPV6 address');
                            return FALSE;
                        }
                    } elseif ($type[$id] == "TXT") {

                    } elseif ($type[$id] == "SPF") {

                    } else {
                        if (!self::IsValidIP($target[$id])) {
                            if (!self::IsValidDomainName($target[$id])) {
                                self::SetError('An invalid domain name character was entered. Domain names are limited to alphanumeric characters and hyphens.');
                                return FALSE;
                            }
                        }
                        if (!self::IsValidDomainName($target[$id])) {
                            if (!self::IsValidIP($target[$id])) {
                                self::SetError('Target is not a valid IP address');
                                return FALSE;
                            }
                        }
                    }
                }

                //PRIORITY
                if (isset($priority[$id]) && !fs_director::CheckForEmptyValue($priority[$id]) && $priority[$id] != $original_priority[$id]) {
                    if (!is_numeric($priority[$id])) {
                        self::SetError('Priority must be a numeric value.');
                        return FALSE;
                    }
                    if ($priority[$id] < 0 || $priority[$id] > 65535) {
                        self::SetError('The priority of a dns record must be a numeric value between 0 and 65535');
                        return FALSE;
                    }
                }

                //WEIGHT
                if (isset($weight[$id]) && !fs_director::CheckForEmptyValue($weight[$id]) && $weight[$id] != $original_weight[$id]) {
                    if (!is_numeric($weight[$id])) {
                        self::SetError('Weight must be a numeric value.');
                        return FALSE;
                    }
                    if ($weight[$id] < 0 || $weight[$id] > 65535) {
                        self::SetError('The weight of a dns record must be a numeric value between 0 and 65535');
                        return FALSE;
                    }
                }

                //PORT
                if (isset($port[$id]) && !fs_director::CheckForEmptyValue($port[$id]) && $port[$id] != $original_port[$id]) {
                    if (!is_numeric($port[$id])) {
                        self::SetError('PORT must be a numeric value.');
                        return FALSE;
                    }
                    if ($port[$id] < 0 || $port[$id] > 65535) {
                        self::SetError('The port of a dns record must be a numeric value between 0 and 65535');
                        return FALSE;
                    }
                }
            }
        }

        //NEW Records
        //Find all new records in post array
        if (isset($newRecords) && !fs_director::CheckForEmptyValue($newRecords)) {
            $numnew = $newRecords;
            for ($id = 1; $id <= $numnew; $id++) {
                $NewId = 'new_' . $id;
                if (isset($type[$NewId])) {
                    if ($delete[$NewId] == "false" && !fs_director::CheckForEmptyValue($type[$NewId])) {
                        //HOSTNAME
                        if (isset($hostName[$NewId]) && !fs_director::CheckForEmptyValue($hostName[$NewId]) && $hostName[$NewId] != "@") {
                            //Check that hostname does not already exist.
                            $numrows = $zdbh->prepare('SELECT dn_id_pk FROM x_dns WHERE dn_host_vc=:hostName2 AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL AND dn_type_vc=:type');
                            $hostName2 = $hostName[$NewId];
                            $numrows->bindParam(':hostName2', $hostName2);
                            $numrows->bindParam(':domainID', $domainID);
							 $numrows->bindParam(':type', $type[$NewId]);
                            $numrows->execute();
                            if ($numrows->fetch()) {
                                self::SetError('Hostnames must be unique.');
                                return FALSE;
                            }

                            if ($type[$NewId] != "SRV") {
                                if (!($hostName[$NewId] == '*' or self::IsValidTargetName($hostName[$NewId]) )) {
                                    self::SetError('Hostname invalid.');
                                    return FALSE;
                                }
                            }
                        }
                        //TTL
                        if (isset($ttl[$NewId]) && !fs_director::CheckForEmptyValue($ttl[$NewId])) {
                            if (!is_numeric($ttl[$NewId])) {
                                self::SetError('TTL must be a numeric value.');
                                return FALSE;
                            }
                        }
                        //TARGET
                        if (isset($target[$NewId]) && !fs_director::CheckForEmptyValue($target[$NewId])) {
                            if ($type[$NewId] == "A") {
                                if (!self::IsValidIPv4($target[$NewId])) {
                                    self::SetError('IP Address is not a valid IPV4 address.');
                                    return FALSE;
                                }
                            } elseif ($type[$NewId] == "AAAA") {
                                if (!self::IsValidIPv6($target[$NewId])) {
                                    self::SetError('IP Address is not a valid IPV6 address');
                                    return FALSE;
                                }
                            } elseif ($type[$NewId] == "TXT") {

                            } elseif ($type[$NewId] == "SPF") {

                            } elseif ($type[$NewId] == "NS") {

                            } else {
                                if (!self::IsValidIP($target[$NewId])) {
                                    if (!self::IsValidDomainName($target[$NewId])) {
                                        self::SetError('An invalid domain name character was entered. Domain names are limited to alphanumeric characters and hyphens.');
                                        return FALSE;
                                    }
                                }
                                if (!self::IsValidDomainName($target[$NewId])) {
                                    if (!self::IsValidIP($target[$NewId])) {
                                        self::SetError('Target is not a valid IP address');
                                        return FALSE;
                                    }
                                }
                            }
                        }
                        //PRIORITY
                        if (isset($priority[$NewId]) && !fs_director::CheckForEmptyValue($priority[$NewId])) {
                            if (!is_numeric($priority[$NewId])) {
                                self::SetError('Priority must be a numeric value.');
                                return FALSE;
                            }
                            if ($priority[$NewId] < 0 || $priority[$NewId] > 65535) {
                                self::SetError('The priority of a dns record must be a numeric value between 0 and 65535');
                                return FALSE;
                            }
                        }
                        //WEIGHT
                        if (isset($weight[$NewId]) && !fs_director::CheckForEmptyValue($weight[$NewId])) {
                            if (!is_numeric($weight[$NewId])) {
                                self::SetError('Weight must be a numeric value.');
                                return FALSE;
                            }
                            if ($weight[$NewId] < 0 || $weight[$NewId] > 65535) {
                                self::SetError('The weight of a dns record must be a numeric value between 0 and 65535');
                                return FALSE;
                            }
                        }
                        //PORT
                        if (isset($port[$NewId]) && !fs_director::CheckForEmptyValue($port[$NewId])) {
                            if (!is_numeric($port[$NewId])) {
                                self::SetError('PORT must be a numeric value.');
                                return FALSE;
                            }
                            if ($port[$NewId] < 0 || $port[$NewId] > 65535) {
                                self::SetError('The port of a dns record must be a numeric value between 0 and 65535');
                                return FALSE;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    static function IsValidDomainName($a)
    {
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

    static function IsValidTargetName($a)
    {
        if ($a != "@") {
            $part = explode(".", $a);
            foreach ($part as $check) {
                if (!preg_match('/^[a-z\d_][a-z\d-_]{0,62}$/i', $check) || preg_match('/-$/', $check)) {
                    return false;
                }
            }
        }
        return true;
    }

    static function CleanRecord($data, $type)
    {
        $data = trim($data);
        if ($type == 'SPF' || $type == 'TXT') {
            $data = str_replace('"', '', $data);
            $data = str_replace('\'', '', $data);
            $data = addslashes($data);
        } else {
            $data = str_replace(' ', '', $data);
        }
        //Add '@' if hostname is blank on NS and MX records.
        if ($type == 'NS' || $type == 'MX') {
            if ($data == '') {
                $data = "@";
            }
        }

        // We'll leave the content for SPF and TXT records and won't try to make them look better by strtolower'ing them.
        if (!($type == 'SPF' || $type == 'TXT')) {
            $data = strtolower($data);
        }

        return $data;
    }

    static function IsTypeAllowed($type)
    {
        global $zdbh;
        $record_types = ctrl_options::GetSystemOption('allowed_types');
        $record_types = explode(' ', $record_types);
        if (in_array($type, $record_types)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIPv4($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function IsValidIPv6($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$ResultOk)) {
            return ui_sysmessage::shout(ui_language::translate(self::$ResultOk), 'zannouncesuccess', 'SUCCESS DNS SAVED');
        } elseif (!fs_director::CheckForEmptyValue(self::$ResultErr)) {
            return ui_sysmessage::shout(ui_language::translate(self::$ResultErr), 'zannounceerror', 'ERROR DNS NOT SAVED');
        }
        return;
    }

    static function TriggerDNSUpdate($id)
    {
        global $zdbh;
        global $controller;
        $records_list = ctrl_options::GetSystemOption('dns_hasupdates');
        $record_array = explode(',', $records_list);
        if (!in_array($id, $record_array)) {
            if (empty($records_list)) {
                $records_list .= $id;
            } else {
                $records_list .= ',' . $id;
            }
            $sql = "UPDATE x_settings SET so_value_tx=:newlist WHERE so_name_vc='dns_hasupdates'";
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':newlist', $records_list);
            $sql->execute();
            return true;
        }
    }

    static function CheckZoneRecord($domainID)
    {
        global $zdbh;
        $hasrecords = false;
        $sql = 'SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL';
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':domainID', $domainID);

        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $hasrecords = true;
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL ORDER BY dn_type_vc");
                $sql->bindParam(':domainID', $domainID);
                $sql->execute();
                $numrows = $zdbh->prepare("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL");
                $numrows->bindParam(':domainID', $domainID);
                $numrows->execute();
                $domain = $numrows->fetch();

                $zonecheck_file = (ctrl_options::GetSystemOption('temp_dir')) . $domain['dn_name_vc'] . ".txt";
                $checkline = "$" . "TTL 10800" . fs_filehandler::NewLine();
                $checkline .= "@ IN SOA " . $domain['dn_name_vc'] . ". ";
                $checkline .= "postmaster." . $domain['dn_name_vc'] . ". (" . fs_filehandler::NewLine();
                $checkline .= " " . date("Ymdt") . " ;serial" . fs_filehandler::NewLine();
                $checkline .= " " . ctrl_options::GetSystemOption('refresh_ttl') . " ;refresh after 6 hours" . fs_filehandler::NewLine();
                $checkline .= " " . ctrl_options::GetSystemOption('retry_ttl') . " ;retry after 1 hour" . fs_filehandler::NewLine();
                $checkline .= " " . ctrl_options::GetSystemOption('expire_ttl') . " ;expire after 1 week" . fs_filehandler::NewLine();
                $checkline .= " " . ctrl_options::GetSystemOption('minimum_ttl') . " ) ;minimum TTL of 1 day" . fs_filehandler::NewLine();
                while ($rowdns = $sql->fetch()) {
                    if ($rowdns['dn_type_vc'] == "A") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN A " . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "AAAA") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN AAAA " . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "CNAME") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN CNAME " . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "MX") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN MX " . $rowdns['dn_priority_in'] . " " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "TXT") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN TXT \"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "SRV") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN SRV " . $rowdns['dn_priority_in'] . " " . $rowdns['dn_weight_in'] . " " . $rowdns['dn_port_in'] . " " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "SPF") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN SPF \"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "NS") {
                        $checkline .= $rowdns['dn_host_vc'] . " " . $rowdns['dn_ttl_in'] . " IN NS " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                }
                fs_filehandler::UpdateFile($zonecheck_file, 0777, $checkline);
            }
        }
        if ($hasrecords == true) {
            //Check the temp zone record for errors
            if (file_exists($zonecheck_file)) {
                ob_start();

                $command = ctrl_options::GetSystemOption('named_checkzone');
                $args = array(
                    $domain['dn_name_vc'],
                    $zonecheck_file
                );
                $retval = ctrl_system::systemCommand($command, $args);

                $content_grabbed = ob_get_contents();
                ob_end_clean();
                unlink($zonecheck_file);
                if ($retval == 0) {
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
