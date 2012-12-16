<?php

DeleteClientDatabases();

function DeleteClientDatabases() {
    global $zdbh;
    $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
    $numrows = $zdbh->query($sql);
    if ($numrows->fetchColumn() <> 0) {
        $sql = $zdbh->prepare($sql);
        $sql->execute();
        while ($rowclient = $sql->fetch()) {
            //$rowdatabase = $zdbh->query("SELECT * FROM x_mysql_databases WHERE my_acc_fk=" . $rowclient['ac_id_pk'] . " AND my_deleted_ts IS NULL")->fetch();
            
            $numrows = $zdbh->prepare("SELECT * FROM x_mysql_databases WHERE my_acc_fk=:ac_id_pk AND my_deleted_ts IS NULL");
            $numrows->bindParam(':ac_id_pk', $rowclient['ac_id_pk']);
            $numrows->execute();
            $rowdatabase = $numrows->fetch();
        
            if ($rowdatabase) {
                try {
                    $my_name_vc = $zdbh->mysqlRealEscapeString($rowdatabase['my_name_vc']);
                    $delete = $zdbh->prepare("DROP DATABASE IF EXISTS `$my_name_vc`;");
                    //$delete->bindParam(':my_name_vc', $rowdatabase['my_name_vc'], PDO::PARAM_STR);
                    $delete->execute();
                    $delete = $zdbh->prepare("FLUSH PRIVILEGES");
                    $delete->execute();
                    $delete = $zdbh->prepare("UPDATE x_mysql_databases 
						SET my_deleted_ts = :time 
						WHERE my_acc_fk = :ac_id_pk");
                    $delete->bindParam(':ac_id_pk', $rowclient['ac_id_pk']);
                    $time = time();
                    $delete->bindParam(':time', $time);
                    $delete->execute();
                    $delete = $zdbh->prepare("DELETE FROM x_mysql_dbmap 
						WHERE mm_database_fk=:my_id_pk");
                    $delete->bindParam(':my_id_pk', $rowdatabase['my_id_pk']);
                    $delete->execute();
                } catch (PDOException $e) {
                    
                }
            }
        }
    }
}

?>