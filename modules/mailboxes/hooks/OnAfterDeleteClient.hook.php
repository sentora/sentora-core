<?php

	DeleteMailboxesForDeletedClient();
		
	
    function DeleteMailboxesForDeletedClient() {
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

		// Include mail server specific file here.
		if (file_exists("modules/mailboxes/hooks/" . ctrl_options::GetOption('mailserver_php') . "")){
			include("modules/mailboxes/hooks/" . ctrl_options::GetOption('mailserver_php') . "");
		}
		
		foreach ($deletedclients as $deletedclient){
			$result = $zdbh->query("SELECT * FROM x_mailboxes WHERE mb_acc_fk=" . $deletedclient . " AND mb_deleted_ts IS NULL")->Fetch();
        	if ($result) {
                $sql = $zdbh->prepare("UPDATE x_mailboxes SET mb_deleted_ts=" . time() . " WHERE mb_acc_fk=".$deletedclient."");
                $sql->execute();			
			}
		}
    }
?>