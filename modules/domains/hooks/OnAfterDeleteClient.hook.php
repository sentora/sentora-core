<?php
	
	DeleteDomainsForDeletedClient();
	
    function DeleteDomainsForDeletedClient() {
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {
		    $zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {
		
		}
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
		foreach ($deletedclients as $deletedclient){
			$deletedir=false;
			$result = $zdbh->query("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $deletedclient . " AND vh_type_in=1 AND vh_deleted_ts IS NULL")->Fetch();
        	if ($result) {
                $sql = $zdbh->prepare("UPDATE x_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_acc_fk=".$deletedclient." AND vh_type_in=1");
                $sql->execute();
				$deletedir=true;
								
			}
			if ($deletedir == true){
				$currentuser = ctrl_users::GetUserDetail($deletedclient);
				if (is_dir(ctrl_options::GetOption('hosted_dir') . $currentuser['username'])){
					fs_filehandler::RemoveDirectory(ctrl_options::GetOption('hosted_dir') . $currentuser['username']);
				}
			}
		}
    }
?>