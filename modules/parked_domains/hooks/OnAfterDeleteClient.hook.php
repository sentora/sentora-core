<?php

DeleteParkedDomainsForDeletedClient();

function DeleteParkedDomainsForDeletedClient() {
    global $zdbh;
    $deletedclients = array();
    $sql = "SELECT COUNT(*) FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
    if ($numrows = $zdbh->query($sql)) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL");
            $sql->execute();
            while ($rowclient = $sql->fetch()) {
                $deletedclients[] = $rowclient['ac_id_pk'];
            }
        }
    }
    foreach ($deletedclients as $deletedclient) {
        $deletedir = false;
        //$result = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $deletedclient . " AND vh_type_in=3 AND vh_deleted_ts IS NULL")->Fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=:deletedclient AND vh_type_in=3 AND vh_deleted_ts IS NULL");
        $numrows->bindParam(':deletedclient', $deletedclient);
        $numrows->execute();
        $result = $numrows->fetch();
        if ($result) {
            $sql = $zdbh->prepare("UPDATE x_vhosts SET vh_deleted_ts=:time WHERE vh_acc_fk=:deletedclient AND vh_type_in=3");
            $time = time();
            $sql->bindParam(':time', $time);
            $sql->bindParam(':deletedclient', $deletedclient);
            $sql->execute();
            $deletedir = true;
        }
        if ($deletedir == true) {
            $currentuser = ctrl_users::GetUserDetail($deletedclient);
            if (is_dir(ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'])) {
                fs_filehandler::RemoveDirectory(ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username']);
            }
        }
    }
}

?>