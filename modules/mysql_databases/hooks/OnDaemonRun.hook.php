<?php

global $zdbh;
include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;

/*
 * Calculate the total size of all MySQL database.
 */
$mysqlsql = $zdbh->query("SELECT my_id_pk, my_name_vc FROM x_mysql_databases WHERE my_deleted_ts IS NULL");
while ($database = $mysqlsql->fetch()) {
    $currentdb = new db_driver("mysql:host=$host;dbname=" . $database['my_name_vc'] . "", $z_db_user, $z_db_pass);
    $currentdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbsize = $currentdb->query("SHOW TABLE STATUS");
    $dbgetsize = 0;
    while ($row = $dbsize->fetch()) {
        $dbgetsize = $dbgetsize + ($row['Data_length'] + $row['Index_length']);
    }
    $updatesql = $zdbh->query("UPDATE x_mysql_databases SET my_usedspace_bi = '" . $dbgetsize . "' WHERE my_id_pk =" . $database['my_id_pk'] . "");
#echo "Database found: " . $database['my_name_vc'] . " - " . $dbgetsize . " \n";
}
?>
