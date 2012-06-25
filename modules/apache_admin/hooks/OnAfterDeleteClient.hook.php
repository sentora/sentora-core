<?php
	
	SetWriteApacheConfigTrue();
	DeleteApacheClientFiles();
	
    function SetWriteApacheConfigTrue() {
		global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx='true'
								WHERE so_name_vc='apache_changed'");
        $sql->execute();
    }

	function DeleteApacheClientFiles(){
		global $zdbh;
        $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowdeletedaccounts = $sql->fetch()) {
				if (file_exists(ctrl_options::GetSystemOption('hosted_dir') . $rowdeletedaccounts['ac_user_vc'])) {
        			fs_director::RemoveDirectory(ctrl_options::GetSystemOption('hosted_dir') . $rowdeletedaccounts['ac_user_vc']);
    			}
            }
        }
    }

?>