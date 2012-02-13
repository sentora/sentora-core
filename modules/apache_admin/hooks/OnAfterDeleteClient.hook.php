<?php
	
	SetWriteApacheConfigTrue();
	DeleteApacheClientFiles();
	
    function SetWriteApacheConfigTrue() {
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
			$zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {

		}
        $sql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx='true'
								WHERE so_name_vc='apache_changed'");
        $sql->execute();
    }

	function DeleteApacheClientFiles(){
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
            $res = array();
            $sql->execute();
            while ($rowdeletedaccounts = $sql->fetch()) {
				if (file_exists(ctrl_options::GetOption('hosted_dir') . $rowdeletedaccounts['ac_user_vc'])) {
        			fs_director::RemoveDirectory(ctrl_options::GetOption('hosted_dir') . $rowdeletedaccounts['ac_user_vc']);
    			}
            }
        }
    }

?>