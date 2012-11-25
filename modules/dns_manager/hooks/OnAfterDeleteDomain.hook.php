<?php

DeleteDNSRecordsForDeletedDomain();

function DeleteDNSRecordsForDeletedDomain() {
    global $zdbh;
    $deleteddomains = array();
    $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_deleted_ts IS NOT NULL";
    if ($numrows = $zdbh->query($sql)) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NOT NULL");
            $sql->execute();
            while ($rowvhost = $sql->fetch()) {
                $deleteddomains[] = $rowvhost['vh_id_pk'];
            }
        }
    }
    foreach ($deleteddomains as $deleteddomain) {
        //$result = $zdbh->query("SELECT * FROM x_dns WHERE dn_vhost_fk=" . $deleteddomain . " AND dn_deleted_ts IS NULL")->Fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=:deleteddomain AND dn_deleted_ts IS NULL");
        $numrows->bindParam(':deleteddomain', $deleteddomain);
        $numrows->execute();
        $result = $numrows->fetch();
        
        if ($result) {
            $sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=:time WHERE dn_vhost_fk=:deleteddomain");
            $sql->bindParam(':deleteddomain', $deleteddomain);
            $time = time();
            $sql->bindParam(':time', $time);
            $sql->execute();
            TriggerDNSUpdate($result['dn_vhost_fk']);
        }
    }
}

function TriggerDNSUpdate($id) {
    global $zdbh;
    $GetRecords = ctrl_options::GetSystemOption('dns_hasupdates');
    $records = explode(",", $GetRecords);
    foreach ($records as $record) {
        $RecordArray[] = $record;
    }
    if (!in_array($id, $RecordArray)) {
        $newlist = $GetRecords . "," . $id;
        $newlist = str_replace(",,", ",", $newlist);
        $sql = "UPDATE x_settings SET so_value_tx=:newlist WHERE so_name_vc='dns_hasupdates'";
        $sql = $zdbh->prepare($sql);
        $sql->bindParam(':newlist', $newlist);
        $sql->execute();
        return true;
    }
}

?>