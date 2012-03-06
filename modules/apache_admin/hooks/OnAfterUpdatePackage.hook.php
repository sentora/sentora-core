<?php
	
	SetWriteApacheConfigTrue();
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

?>