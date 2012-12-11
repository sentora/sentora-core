<?php

AddCurrentDatabaseSize();


/*
 * Add the current MySQL database sizes to the user's current disk space amount.
 */

function AddCurrentDatabaseSize() {
    global $zdbh;
    include('cnf/db.php');
    $z_db_user = $user;
    $z_db_pass = $pass;
    $usersql = $zdbh->query("SELECT ac_id_pk,ac_user_vc FROM x_accounts WHERE ac_deleted_ts IS NULL");
    while ($users = $usersql->fetch()) {
        $totalforuser = 0;
        //$userdbssql = $zdbh->query("SELECT my_name_vc, my_usedspace_bi FROM x_mysql_databases WHERE my_acc_fk = " . $users['ac_id_pk'] . " AND my_deleted_ts IS NULL");       
        $userdbssql = $zdbh->prepare("SELECT my_name_vc, my_usedspace_bi FROM x_mysql_databases WHERE my_acc_fk = :ac_id_pk AND my_deleted_ts IS NULL");
        $userdbssql->bindParam(':ac_id_pk', $users['ac_id_pk']);
        $userdbssql->execute();
        
        while ($userdbs = $userdbssql->fetch()) {
            $totalforuser = $totalforuser + $userdbs['my_usedspace_bi'];
            // echo "User: " . $users['ac_user_vc'] . " Database: " . $userdbs['my_name_vc'] . " Size: " . $userdbs['my_usedspace_bi'] . "\n";
        }
        // echo "Total DB storage size: " . $totalforuser . " for user " . $users['ac_user_vc'] . "\n";
        //$zdbh->query("UPDATE x_bandwidth SET bd_diskamount_bi = (bd_diskamount_bi+" . $totalforuser . ") WHERE bd_acc_fk = " . $users['ac_id_pk'] . " AND bd_month_in = " . date('Ym') . "");
        $date = date('Ym');
        $numrows = $zdbh->prepare("UPDATE x_bandwidth SET bd_diskamount_bi = (bd_diskamount_bi+:totalforuser) WHERE bd_acc_fk = :ac_id_pk AND bd_month_in = :date");
        $numrows->bindParam(':totalforuser', $totalforuser);
        $numrows->bindParam(':date', $date);
        $numrows->bindParam(':ac_id_pk', $users['ac_id_pk']);
        $numrows->execute();
    }
}

?>
