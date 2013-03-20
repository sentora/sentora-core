<?php

echo fs_filehandler::NewLine() . "START DNS Manager Hook" . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('Backup Config')) {
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

function WriteDNSZoneRecordsHook() {
    global $zdbh;
    $dnsrecords = array();
    $RecordsNeedingUpdateArray = array();
    //Get all the records needing upadated and put them in an array.
    $GetRecordsNeedingUpdate = ctrl_options::GetSystemOption('dns_hasupdates');
    $RecordsNeedingUpdate = explode(",", $GetRecordsNeedingUpdate);
    foreach ($RecordsNeedingUpdate as $RecordNeedingUpdate) {
        $RecordsNeedingUpdateArray[] = $RecordNeedingUpdate;
    }
    //Get all the domain ID's we need and put them in an array.
    $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
    if ($numrows = $zdbh->query($sql)) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
            $sql->execute();
            while ($rowdns = $sql->fetch()) {
                $dnsrecords[] = $rowdns['dn_vhost_fk'];
            }
        }
    }
    //Now we have all domain ID's, loop through them and find records for each zone file.
    foreach ($dnsrecords as $dnsrecord) {
        //if (in_array($dnsrecord, $RecordsNeedingUpdateArray)){
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk=:dnsrecord AND dn_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':dnsrecord', $dnsrecord);

        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=:dnsrecord AND dn_deleted_ts IS NULL ORDER BY dn_type_vc");
                $sql->bindParam(':dnsrecord', $dnsrecord);
                $sql->execute();
//              $domain = $zdbh->query("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . " AND dn_deleted_ts IS NULL")->Fetch();           
                $numrows = $zdbh->prepare("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=:dnsrecord AND dn_deleted_ts IS NULL");
                $numrows->bindParam(':dnsrecord', $dnsrecord);
                $numrows->execute();
                $domain = $numrows->fetch();

                //Create zone directory if it doesnt exists...
                if (!is_dir(ctrl_options::GetSystemOption('zone_dir'))) {
                    fs_director::CreateDirectory(ctrl_options::GetSystemOption('zone_dir'));
                    fs_director::SetFileSystemPermissions(ctrl_options::GetSystemOption('zone_dir'));
                }
                $zone_file = (ctrl_options::GetSystemOption('zone_dir')) . $domain['dn_name_vc'] . ".txt";
                $line = "$" . "TTL 10800" . fs_filehandler::NewLine();
                $line .= "@ IN SOA " . $domain['dn_name_vc'] . ".    ";
                $line .= "postmaster." . $domain['dn_name_vc'] . ". (" . fs_filehandler::NewLine();
                $line .= "                       " . date("Ymdt") . "	;serial" . fs_filehandler::NewLine();
                $line .= "                       " . ctrl_options::GetSystemOption('refresh_ttl') . "      ;refresh after 6 hours" . fs_filehandler::NewLine();
                $line .= "                       " . ctrl_options::GetSystemOption('retry_ttl') . "       ;retry after 1 hour" . fs_filehandler::NewLine();
                $line .= "                       " . ctrl_options::GetSystemOption('expire_ttl') . "     ;expire after 1 week" . fs_filehandler::NewLine();
                $line .= "                       " . ctrl_options::GetSystemOption('minimum_ttl') . " )    ;minimum TTL of 1 day" . fs_filehandler::NewLine();
                while ($rowdns = $sql->fetch()) {
                    if ($rowdns['dn_type_vc'] == "A") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		A		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "AAAA") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		AAAA		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "CNAME") {
                        $line .= $rowdns['dn_host_vc'] . "              " . $rowdns['dn_ttl_in'] . "            IN              CNAME           " . $rowdns['dn_target_vc'] . ($rowdns['dn_target_vc'] == '@' ? '' : '.') . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "MX") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		MX		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "TXT") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "SRV") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SRV		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_weight_in'] . "	" . $rowdns['dn_port_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "SPF") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SPF		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                    }
                    if ($rowdns['dn_type_vc'] == "NS") {
                        $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		NS		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                    }
                }
                echo "Updating zone record: " . $domain['dn_name_vc'] . fs_filehandler::NewLine();
                fs_filehandler::UpdateFile($zone_file, 0777, $line);
            }
        }
    }
    //}	
}

function WriteDNSNamedHook() {
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
    //Create named directory if it doesnt exists...
    if (!is_dir(ctrl_options::GetSystemOption('named_dir'))) {
        fs_director::CreateDirectory(ctrl_options::GetSystemOption('named_dir'));
        fs_director::SetFileSystemPermissions(ctrl_options::GetSystemOption('named_dir'));
    }
    $named_file = ctrl_options::GetSystemOption('named_dir') . ctrl_options::GetSystemOption('named_conf');
    echo "Updating " . $named_file . fs_filehandler::NewLine();
    //Now we have all domain ID's, loop through them and find records for each zone file.
    $line = "";
    foreach ($domains as $domain) {
        echo "CHECKING ZONE FILE: " . ctrl_options::GetSystemOption('zone_dir') . $domain . ".txt..." . fs_filehandler::NewLine();
        system(ctrl_options::GetSystemOption('named_checkzone') . " " . $domain . " " . ctrl_options::GetSystemOption('zone_dir') . $domain . ".txt", $retval);
        echo $retval . fs_filehandler::NewLine();
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

function ResetDNSRecordsUpatedHook() {
    global $zdbh;
    $sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx=NULL WHERE so_name_vc='dns_hasupdates'");
    $sql->execute();
}

function PurgeOldZoneDNSRecordsHook() {
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

function ReloadBindHook() {
    if (sys_versions::ShowOSPlatformVersion() == "Windows") {
        $reload_bind = ctrl_options::GetSystemOption('bind_dir') . "rndc.exe reload";
    } else {
        $reload_bind = ctrl_options::GetSystemOption('zsudo') . " service " . ctrl_options::GetSystemOption('bind_service') . " reload";
    }
    echo "Reloading BIND now..." . fs_filehandler::NewLine();
    pclose(popen($reload_bind, 'r'));
}

?>
