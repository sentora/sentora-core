<?php
require_once(realpath(dirname(__FILE__) . "/../code/writecronfile.php"));

DeleteClientCronjobs();
WriteCronFile();

function DeleteClientCronjobs() {
    global $zdbh;
    $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
    $numrows = $zdbh->query($sql);
    if ($numrows->fetchColumn() <> 0) {
        $sql = $zdbh->prepare($sql);
        $sql->execute();
        while ($rowclient = $sql->fetch()) {
            //$rowcron = $zdbh->query("SELECT * FROM x_cronjobs WHERE ct_acc_fk=" . $rowclient['ac_id_pk'] . " AND ct_deleted_ts IS NULL")->fetch();
            $numrows = $zdbh->prepare("SELECT * FROM x_cronjobs WHERE ct_acc_fk=:userid AND ct_deleted_ts IS NULL");
            $numrows->bindParam(':userid', $rowclient['ac_id_pk']);
            $numrows->execute();
            $rowcron = $numrows->fetch();
            
            if ($rowcron) {
                $delete = "UPDATE x_cronjobs SET ct_deleted_ts=:time WHERE ct_acc_fk=:userid";
                $delete = $zdbh->prepare($delete);
                $delete->bindValue(':time', time());
                $delete->bindParam(':userid', $rowclient['ac_id_pk']);
                $delete->execute();
            }
        }
    }
}

?>