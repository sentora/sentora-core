<?php

global $zdbh;

/*
 * Calculate the home directory size for each 'active' user account on the server.
 */
$userssql = $zdbh->query("SELECT ac_id_pk, ac_user_vc FROM x_accounts WHERE ac_deleted_ts IS NULL");
while ($userdir = $userssql->fetch()) {
    $homedirectory = ctrl_options::GetOption('hosted_dir') . $userdir['ac_user_vc'];
    if (fs_director::CheckFolderExists($homedirectory)) {
        $size = fs_director::GetDirectorySize($homedirectory);
    } else {
        $size = 0;
    }
    $checksql = $zdbh->query("SELECT COUNT(*) AS total FROM x_bandwidth WHERE bd_month_in = " . date("Ym") . " AND bd_acc_fk = " . $userdir['ac_id_pk'] . "")->fetch();
    if ($checksql['total'] == 0) {
        $zdbh->query("INSERT INTO x_bandwidth (bd_acc_fk, bd_month_in, bd_transamount_bi, bd_diskamount_bi) VALUES (" . $userdir['ac_id_pk'] . "," . date("Ym") . ",0,0);");
    }
    $updatesql = $zdbh->query("UPDATE x_bandwidth SET bd_diskamount_bi = '" . $size . "' WHERE bd_acc_fk =" . $userdir['ac_id_pk'] . "");
    $updatesql->execute();
    //echo "Disk usage for user \"" . $userdir['ac_user_vc'] . "\" is: " . $size . "\n";
}
?>
