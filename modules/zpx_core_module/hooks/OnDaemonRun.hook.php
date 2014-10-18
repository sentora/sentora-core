<?php

global $zdbh;

/*
 * Calculate the home directory size for each 'active' user account on the server.
 */
$userssql = $zdbh->query("SELECT ac_id_pk, ac_user_vc FROM x_accounts WHERE ac_deleted_ts IS NULL");
echo fs_filehandler::NewLine() . "START Calculating disk Usage for all client accounts.." . fs_filehandler::NewLine();
while ($userdir = $userssql->fetch()) {
    $homedirectory = ctrl_options::GetSystemOption('hosted_dir') . $userdir['ac_user_vc'];
    if (fs_director::CheckFolderExists($homedirectory)) {
        $size = fs_director::GetDirectorySize($homedirectory);
    } else {
        $size = 0;
    }
    $currentuser = ctrl_users::GetUserDetail($userdir['ac_id_pk']);
    $numrows = $zdbh->prepare("SELECT COUNT(*) AS total FROM x_bandwidth WHERE bd_month_in = :date AND bd_acc_fk = :ac_id_pk");
    $date = date("Ym");
    $numrows->bindParam(':date', $date);
    $numrows->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
    $numrows->execute();
    $checksql = $numrows->fetch();
    
    if ($checksql['total'] == 0) {
        $numrows3 = $zdbh->prepare("INSERT INTO x_bandwidth (bd_acc_fk, bd_month_in, bd_transamount_bi, bd_diskamount_bi, bd_diskover_in, bd_diskcheck_in, bd_transover_in, bd_transcheck_in ) VALUES (:ac_id_pk,:date,0,0,0,0,0,0);");
        $date = date("Ym");
        $numrows3->bindParam(':date', $date);
        $numrows3->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
        $numrows3->execute();
    }

    $updatesql = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskamount_bi = :size WHERE bd_acc_fk =:ac_id_pk");
    $updatesql->bindParam(':size', $size);
    $updatesql->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
    $updatesql->execute();
    
    $numrows = $zdbh->prepare("SELECT * FROM x_bandwidth WHERE bd_month_in = :date AND bd_acc_fk = :ac_id_pk");
    $date = date("Ym");
    $numrows->bindParam(':date', $date);
    $numrows->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
    $numrows->execute();
    $checksize = $numrows->fetch();
    
    if ($checksize['bd_diskamount_bi'] > $currentuser['diskquota']) {
        $updatesql = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskover_in = 1 WHERE bd_acc_fk =:ac_id_pk");
        $updatesql->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
        $updatesql->execute();
    } else {
        $updatesql = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskover_in = 0 WHERE bd_acc_fk =:ac_id_pk");
        $updatesql->bindParam(':ac_id_pk', $userdir['ac_id_pk']);
        $updatesql->execute();
    }


    echo "Disk usage for user \"" . $userdir['ac_user_vc'] . "\" is: " . $size . " (" . fs_director::ShowHumanFileSize($size) . ")" . fs_filehandler::NewLine();
}
echo "END Calculating disk usage" . fs_filehandler::NewLine();


/*
 * Calculate the bandwidth used for each user.
 */
$checksql = $zdbh->query("SELECT COUNT(*) AS total FROM x_vhosts WHERE vh_deleted_ts IS NULL")->fetch();
echo fs_filehandler::NewLine() . "START Calculating bandwidth usage for all client accounts.." . fs_filehandler::NewLine();
if ($checksql['total'] > 0) {
    $domainssql = $zdbh->query("SELECT vh_acc_fk, vh_name_vc FROM x_vhosts WHERE vh_deleted_ts IS NULL");
    while ($domain = $domainssql->fetch()) {
        $domainowner = ctrl_users::GetUserDetail($domain['vh_acc_fk']);
        $bandwidthlog = ctrl_options::GetSystemOption('log_dir') . 'domains/' . $domainowner['username'] . '/' . $domain['vh_name_vc'] . '-bandwidth.log';
        $snapshotfile = ctrl_options::GetSystemOption('log_dir') . 'domains/' . $domainowner['username'] . '/' . $domain['vh_name_vc'] . '-snapshot.bw';
        $bandwidth = 0;
        echo "Processing domain \"" . $domain['vh_name_vc'] . "\"" . fs_filehandler::NewLine();
        if (fs_director::CheckFileExists($bandwidthlog)) {
            fs_filehandler::CopyFile($bandwidthlog, $snapshotfile);
            if (fs_director::CheckFileExists($snapshotfile)) {
                fs_filehandler::ResetFile($bandwidthlog);
                echo "Generating bandwidth.. " . fs_filehandler::NewLine();
                $bandwidth = sys_bandwidth::CalculateFromApacheLog($snapshotfile);
                unlink($snapshotfile);
                echo "usage: " . $bandwidth . " (" . fs_director::ShowHumanFileSize($bandwidth) . ")" . fs_filehandler::NewLine();
            }
        }
        if (!fs_director::CheckForEmptyValue($bandwidth)) {
            $numrows = $zdbh->prepare("UPDATE x_bandwidth SET bd_transamount_bi=(bd_transamount_bi+:bandwidth) WHERE bd_acc_fk = :vh_acc_fk AND bd_month_in = :date");
            $numrows->bindParam(':bandwidth', $bandwidth);
            $date = date("Ym");
            $numrows->bindParam(':date', $date);
            $numrows->bindParam(':vh_acc_fk', $domain['vh_acc_fk']);
            $numrows->execute();            
        } else {
            echo "No bandwidth used, skipping!" . fs_filehandler::NewLine();
        }
        $numrows = $zdbh->prepare("SELECT * FROM x_bandwidth WHERE bd_month_in = :date AND bd_acc_fk = :vh_acc_fk");
        $date = date("Ym");
        $numrows->bindParam(':date', $date);
        $numrows->bindParam(':vh_acc_fk', $domain['vh_acc_fk']);
        $numrows->execute();
        $checksize = $numrows->fetch();
        
        if ($checksize['bd_transamount_bi'] > $domainowner['bandwidthquota']) {
            $updatesql = $zdbh->prepare("UPDATE x_bandwidth SET bd_transover_in = 1 WHERE bd_acc_fk = :vh_acc_fk");
            $updatesql->bindParam(':vh_acc_fk', $domain['vh_acc_fk']);
            $updatesql->execute();
        } else {
            $updatesql = $zdbh->prepare("UPDATE x_bandwidth SET bd_transover_in = 0 WHERE bd_acc_fk =:vh_acc_fk");
            $updatesql->bindParam(':vh_acc_fk', $domain['vh_acc_fk']);
            $updatesql->execute();
        }
    }
}
echo "END Calculating bandwidth usage" . fs_filehandler::NewLine();
?>
