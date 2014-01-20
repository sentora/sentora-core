<?php

echo fs_filehandler::NewLine() . "START DNS Manager Hook" . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('DNS Config')) {
    echo "DNS Manager module ENABLED..." . fs_filehandler::NewLine();
    if (!fs_director::CheckForEmptyValue(ctrl_options::GetSystemOption('dns_hasupdates'))) {
        echo "DNS Records have changed... Writing new/updated records..." . fs_filehandler::NewLine();
        WriteDNSZoneRecordsHook();
        WriteDNSNamedHook();
        ResetDNSRecordsUpatedHook();
        PurgeOldZoneDNSRecordsHook();
        ReloadBindHook();
    } else {
        echo "DNS Records have not changed...nothing to do." . fs_filehandler::NewLine();
    }
} else {
    echo "DNS Manager module DISABLED...nothing to do." . fs_filehandler::NewLine();
}
echo "END DNS Manager Hook." . fs_filehandler::NewLine();

function WriteDNSZoneRecordsHook()
{
    global $zdbh;
    //Get list of domains id that have rows in the DNS table
    $DomainsNeedingUpdate = explode(",", ctrl_options::GetSystemOption('dns_hasupdates'));
    //Get list of domains id that have rows in the dns table
    $DomainsInDnsTable = array();
    $sql = $zdbh->prepare("SELECT dn_vhost_fk FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
    $sql->execute();
    while ($rowdns = $sql->fetch()) {
        $DomainsInDnsTable[] = $rowdns['dn_vhost_fk'];
    }

    //Get list of domain to update that have rows in the dns table
    $DomainsToUpdate = array_intersect($DomainsNeedingUpdate, $DomainsInDnsTable);

    //Now we have all domain ID's, loop through them and find records for each zone file.
    foreach ($DomainsToUpdate as $domain_id) {
        //Get the domain name and SOA serial
        $domaininfo = $zdbh->prepare('SELECT vh_name_vc, vh_soaserial_vc FROM x_vhosts WHERE vh_id_pk=:domain');
        $domaininfo->bindparam(':domain', $domain_id);
        $domaininfo->execute();
        $domain = $domaininfo->fetch();
        $DomainName = $domain['vh_name_vc'];
        $SoaSerial = $domain['vh_soaserial_vc'];

        // Ensure that the SOA serial is uptodate and unique
        $SoaDate = date("Ymd");
        if (substr($SoaSerial, 0, 8) != $SoaDate) {
            $SoaSerial = $SoaDate . '00';
        } else {
            $SoaRev = 1 + substr($SoaSerial, 8, 2);
            $SoaSerial = $SoaDate . (($SoaRev < 10) ? '0' : '') . $SoaRev;
        }
        $updatesoa = $zdbh->prepare('UPDATE x_vhosts SET vh_soaserial_vc=:serial WHERE vh_id_pk=:domain');
        $updatesoa->bindparam(':serial', $SoaSerial);
        $updatesoa->bindparam(':domain', $domain_id);
        $updatesoa->execute();

        // We'll Create zone directory if it doesnt exists...
        if (!is_dir(ctrl_options::GetSystemOption('zone_dir'))) {
            fs_director::CreateDirectory(ctrl_options::GetSystemOption('zone_dir'));
            fs_director::SetFileSystemPermissions(ctrl_options::GetSystemOption('zone_dir'));
        }
        $zone_file = (ctrl_options::GetSystemOption('zone_dir')) . $DomainName . ".txt";
        $line = "$" . "TTL 10800" . fs_filehandler::NewLine();
        $line .= "@ IN SOA ns1." . $DomainName . ".    postmaster." . $DomainName . ". (" . fs_filehandler::NewLine();
        $line .= "    " . $SoaSerial . "  ;serial" . fs_filehandler::NewLine();
        $line .= "    " . ctrl_options::GetSystemOption('refresh_ttl') . "    ;refresh after 6 hours" . fs_filehandler::NewLine();
        $line .= "    " . ctrl_options::GetSystemOption('retry_ttl') . "    ;retry after 1 hour" . fs_filehandler::NewLine();
        $line .= "    " . ctrl_options::GetSystemOption('expire_ttl') . "   ;expire after 1 week" . fs_filehandler::NewLine();
        $line .= "    " . ctrl_options::GetSystemOption('minimum_ttl') . " )    ;minimum TTL of 1 day" . fs_filehandler::NewLine();

        $sql = $zdbh->prepare('SELECT * FROM x_dns WHERE dn_vhost_fk=:dnsrecord AND dn_deleted_ts IS NULL ORDER BY dn_type_vc');
        $sql->bindParam(':dnsrecord', $domain_id);
        $sql->execute();
        while ($rowdns = $sql->fetch()) {
            switch ($rowdns['dn_type_vc']) {
                case "A" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    A    " . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    break;
                case "AAAA" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    AAAA    " . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    break;
                case "CNAME" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    CNAME   " . $rowdns['dn_target_vc'] . ($rowdns['dn_target_vc'] == '@' ? '' : '.') . fs_filehandler::NewLine();
                    break;
                case "MX" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    MX    " . $rowdns['dn_priority_in'] . "  " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    break;
                case "TXT" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    TXT    \"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    break;
                case "SRV" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    SRV    " . $rowdns['dn_priority_in'] . "  " . $rowdns['dn_weight_in'] . "  " . $rowdns['dn_port_in'] . "  " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    break;
                case "SPF" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    SPF    \"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    TXT    \"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    break;
                case "NS" :
                    $line .= $rowdns['dn_host_vc'] . "    " . $rowdns['dn_ttl_in'] . "    IN    NS    " . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    break;
            }
        }
        echo 'Updating zone record: ' . $DomainName . fs_filehandler::NewLine();
        fs_filehandler::UpdateFile($zone_file, 0777, $line);
    }
}

function WriteDNSNamedHook()
{
    global $zdbh;
    $domains = array();
//Get all the domain ID's we need and put them in an array.
    $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
    if ($numrows = $zdbh->query($sql)) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $domains[] = $rowdns['dn_name_vc'];
            }
        }
    }
    // Create named directory if it doesnt exists...
    if (!is_dir(ctrl_options::GetSystemOption('named_dir'))) {
        fs_director::CreateDirectory(ctrl_options::GetSystemOption('named_dir'));
        fs_director::SetFileSystemPermissions(ctrl_options::GetSystemOption('named_dir'));
    }
    $named_file = ctrl_options::GetSystemOption('named_dir') . ctrl_options::GetSystemOption('named_conf');
    echo "Updating " . $named_file . fs_filehandler::NewLine();
    // Now we have all domain ID's, loop through them and find records for each zone file.
    $line = "";
    foreach ($domains as $domain) {
        echo "CHECKING ZONE FILE: " . ctrl_options::GetSystemOption('zone_dir') . $domain . ".txt..." . fs_filehandler::NewLine();


        $command = ctrl_options::GetSystemOption('named_checkzone');
        $args = array(
            $domain,
            ctrl_options::GetSystemOption('zone_dir') . $domain . ".txt",
        );
        $retval = ctrl_system::systemCommand($command, $args);

        if ($retval == 0) {
            echo "Syntax check passed. Adding zone to " . ctrl_options::GetSystemOption('named_conf') . fs_filehandler::NewLine();
            $line .= "zone \"" . $domain . "\" IN {" . fs_filehandler::NewLine();
            $line .= "	type master;" . fs_filehandler::NewLine();
            $line .= "	file \"" . ctrl_options::GetSystemOption('zone_dir') . $domain . ".txt\";" . fs_filehandler::NewLine();
            $line .= "	allow-transfer { " . ctrl_options::GetSystemOption('allow_xfer') . "; };" . fs_filehandler::NewLine();
            $line .= "};" . fs_filehandler::NewLine();
        } else {
            echo "Syntax ERROR. Skipping zone record." . fs_filehandler::NewLine();
        }
    }
    fs_filehandler::UpdateFile($named_file, 0777, $line);
}

function ResetDNSRecordsUpatedHook()
{
    global $zdbh;
    $sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx=NULL WHERE so_name_vc='dns_hasupdates'");
    $sql->execute();
}

function PurgeOldZoneDNSRecordsHook()
{
    global $zdbh;
    $domains = array();
    $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
    if ($numrows = $zdbh->query($sql)) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_name_vc");
            $sql->execute();
            while ($rowvhost = $sql->fetch()) {
                $domains[] = $rowvhost['dn_name_vc'];
            }
        }
    }
    $zonefiles = scandir(ctrl_options::GetSystemOption('zone_dir'));
    foreach ($zonefiles as $zonefile) {
        if (!in_array(substr($zonefile, 0, -4), $domains) && $zonefile != "." && $zonefile != "..") {
            if (file_exists(ctrl_options::GetSystemOption('zone_dir') . $zonefile)) {
                echo "Purging old zone record from disk: " . substr($zonefile, 0, -4) . fs_filehandler::NewLine();
                unlink(ctrl_options::GetSystemOption('zone_dir') . $zonefile);
            }
        }
    }
}

function ReloadBindHook()
{
    if (sys_versions::ShowOSPlatformVersion() == "Windows") {
        $reload_bind = ctrl_options::GetSystemOption('bind_dir') . "rndc.exe reload";
    } else {
        $reload_bind = ctrl_options::GetSystemOption('zsudo') . " service " . ctrl_options::GetSystemOption('bind_service') . " reload";
    }
    echo "Reloading BIND now..." . fs_filehandler::NewLine();
    pclose(popen($reload_bind, 'r'));
}
?>
