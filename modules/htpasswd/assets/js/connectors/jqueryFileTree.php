<?php

session_start();
if (isset($_SESSION['zpuid'])) {

    include($_SERVER['DOCUMENT_ROOT'] . '/cnf/db.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/dryden/db/driver.class.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/dryden/debug/logger.class.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/inc/dbc.inc.php');


    $z_db_user = $user;
    $z_db_pass = $pass;
    try {
        $zdbh = new db_driver("mysql:host=" . $host . ";dbname=" . $dbname . "", $z_db_user, $z_db_pass);
    } catch (PDOException $e) {
        exit();
    }

//find user name
    $rows = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_id_pk = :uid");
    $rows->bindParam(':uid', $_SESSION['zpuid']);
    $rows->execute();
    $dbvals = $rows->fetch();
    $username = $dbvals['ac_user_vc'] . '/public_html/';

//find zpanel settings
    $rowsettings = $zdbh->query("SELECT * FROM x_settings WHERE so_name_vc='hosted_dir'")->fetch();


    $path = str_replace('//', '/', str_replace('\\', '/', str_replace('\\\\', '//', str_replace('../', '', str_replace('./', '', urldecode($_POST['dir'])))))); //this is total untrusted data so lets secure it

    if (file_exists($rowsettings['so_value_tx'] . $username . $path)) {
        $files = scandir($rowsettings['so_value_tx'] . $username . $path);
        natcasesort($files);
        if (count($files) > 2) { /* The 2 accounts for . and .. */
            echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
            // All dirs
            foreach ($files as $file) {
                if (file_exists($rowsettings['so_value_tx'] . $username . $path . $file) && $file != '.' && $file != '..' && is_dir($rowsettings['so_value_tx'] . $username . $path . $file)) {
                    $userreturnpath = $path . $file;
                    echo "<li class=\"directory collapsed\"><a href=\"#\" 
														   name=\"" . htmlentities($userreturnpath) . "\"
														   id  =\"" . htmlentities($path . $file) . "\"
														   rel =\"" . htmlentities($path . $file) . "/\"
														   onClick=\"appendText('" . $userreturnpath . "', this.id);\"
															>" . htmlentities($file) . "</a></li>";
                }
            }
            // All files
            foreach ($files as $file) {
                if (file_exists($rowsettings['so_value_tx'] . $username . $path . $file) && $file != '.' && $file != '..' && !is_dir($rowsettings['so_value_tx'] . $username . $path . $file) && strstr($file, '.htaccess')) {
                    $ext = preg_replace('/^.*\./', '', $file);
                    $htaccesspath = trim(substr($path, strlen($rowsettings['so_value_tx']), strlen($rowsettings['so_value_tx'] . $username . $path . $file)));
                    //$rowpath = $zdbh->query("SELECT * FROM x_htaccess WHERE ht_dir_vc='" . substr($htaccesspath, 0, -1) . "' AND ht_deleted_ts IS NULL")->fetch();
                    $rowpathQuery = $zdbh->prepare("SELECT * FROM x_htaccess WHERE ht_dir_vc = :htaccesspath AND ht_deleted_ts IS NULL");
                    $bindhtaccesspath = substr($htaccesspath, 0, -1);
                    $rowpathQuery->bindParam(':htaccesspath', $bindhtaccesspath);
                    $rowpathQuery->execute();
                    $rowpath = $rows->fetch();

                    if ($rowpath) {
                        echo "<li class=\"file ext_$ext\"><a href=\"./?module=htpasswd&selected=Selected&show=Edit&other=" . $rowpath['ht_id_pk'] . "\" title=\"Edit Users\">" . htmlentities($file) . "</a></li>";
                    } else {
                        echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($file) . "\" title=\"Not a known Password File\"><font color=\"red\">" . htmlentities($file) . "</font></a></li>";
                    }
                }
            }
            echo "</ul>";
        } else {
            echo "<font color=\"red\">No Directories Found!</font>";
        }
    }
} else {
    echo "<h1>Unathorised request!</h1><p>You must be logged in before you are able to access this file.</p>";
}
?>