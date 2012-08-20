<?php

DeleteFTPForDeletedClient();

function DeleteFTPForDeletedClient() {
    global $zdbh;
    global $controller;
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

    // Include FTP server specific file here.
    if (file_exists("modules/ftp_management/hooks/" . ctrl_options::GetSystemOption('ftp_php') . "")) {
        include("modules/ftp_management/hooks/" . ctrl_options::GetSystemOption('ftp_php') . "");
    }
}

?>