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
				$rowdatabase = $zdbh->query("SELECT * FROM x_mysql_databases WHERE my_acc_fk=" . $rowclient['ac_id_pk'] . " AND my_deleted_ts IS NULL")->fetch();
				if ($rowdatabase) {
					try {
						$delete = $zdbh->prepare("DROP DATABASE IF EXISTS `" . $rowdatabase['my_name_vc'] . "`;");
				        $delete->execute();
				        $delete = $zdbh->prepare("FLUSH PRIVILEGES");
				        $delete->execute();
	        			$delete = $zdbh->prepare("UPDATE x_mysql_databases 
						SET my_deleted_ts = '" . time() . "' 
						WHERE my_acc_fk = '" . $rowclient['ac_id_pk'] . "'");
			        $delete->execute();
			        $delete = $zdbh->prepare("DELETE FROM x_mysql_dbmap 
						WHERE mm_database_fk=" . $rowdatabase['my_id_pk'] . "");
			        $delete->execute();
					} catch (PDOException $e) {
				
					}
    			}
            }
        }
    }

?>