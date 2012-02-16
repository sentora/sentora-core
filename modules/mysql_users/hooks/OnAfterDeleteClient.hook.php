<?php
	
	DeleteClientDatabaseUser();

    function DeleteClientDatabaseUser() {
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
			$zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {

		}
        $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
            while ($rowclient = $sql->fetch()) {
        		$rowusers = "SELECT * FROM x_mysql_users WHERE mu_acc_fk=" . $rowclient['ac_id_pk'] . " AND mu_deleted_ts IS NULL";
		        $numuserrows = $zdbh->query($rowusers);
		        if ($numuserrows->fetchColumn() <> 0) {
		            $rowusers = $zdbh->prepare($rowusers);
		            $rowusers->execute();
		            while ($rowuser = $rowusers->fetch()) {
						
		
		        		$delete = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '" . $rowuser['mu_name_vc'] . "')";
		        		if ($numrows = $zdbh->query($delete)) {
		            		if ($numrows->fetchColumn() <> 0) {
								try {
		                			$delete = $zdbh->prepare("DROP USER `" . $rowuser['mu_name_vc'] . "`@`" . $rowuser['mu_access_vc'] . "`;");
			                		$delete->execute();
			                		$delete = $zdbh->prepare("FLUSH PRIVILEGES");
			                		$delete->execute();
								} catch (PDOException $e) {
								return false;
								}
		            		}
				        }
						try {
				        	$delete = $zdbh->prepare("
								UPDATE x_mysql_users
								SET mu_deleted_ts = '" . time() . "' 
								WHERE mu_id_pk = '" . $rowuser['mu_id_pk'] . "'");
					        $delete->execute();
					        $delete = $zdbh->prepare("
								DELETE FROM x_mysql_dbmap
								WHERE mm_user_fk = '" . $rowuser['mu_id_pk'] . "'");
					        $delete->execute();
						} catch (PDOException $e) {
						return false;
						}
					}
				}	
            }
        }
    }

?>