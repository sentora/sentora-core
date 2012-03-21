<?php

	DeleteDNSRecordsForDeletedClient();

	
    function DeleteDNSRecordsForDeletedClient() {
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
		foreach ($deletedclients as $deletedclient){
			$result = $zdbh->query("SELECT * FROM x_dns WHERE dn_acc_fk=" . $deletedclient . " AND dn_deleted_ts IS NULL")->Fetch();
        	if ($result) {
                $sql = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=" . time() . " WHERE dn_acc_fk=".$deletedclient."");
                $sql->execute();
				TriggerDNSUpdate($result['dn_vhost_fk']);				
			}
		}
		
		
		
    }
	
	
    function TriggerDNSUpdate($id) {
		global $zdbh;
        $GetRecords = ctrl_options::GetOption('dns_hasupdates');
		$records = explode(",", $GetRecords);
		foreach ($records as $record){
			$RecordArray[] = $record;
		}
		if (!in_array($id, $RecordArray)){
        	$newlist = $GetRecords . "," . $id;
	        $newlist = str_replace(",,", ",", $newlist);
	        $sql = "UPDATE x_settings SET so_value_tx='" . $newlist . "' WHERE so_name_vc='dns_hasupdates'";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
	        return true;
		}
    }

?>