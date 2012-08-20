<?php

include($_SERVER['DOCUMENT_ROOT'] . '/cnf/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/dryden/db/driver.class.php');
include($_SERVER['DOCUMENT_ROOT'] . '/dryden/debug/logger.class.php');
include($_SERVER['DOCUMENT_ROOT'] . '/inc/dbc.inc.php');
$z_db_user = $user;
$z_db_pass = $pass;
try {
    $zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    exit();
}
$rowsettings = $zdbh->query("SELECT * FROM x_settings WHERE so_name_vc='hosted_dir'")->fetch();
$path = urldecode($_POST['dir']);
if (file_exists($path)) {

    $files = scandir($path);
    natcasesort($files);
    if (count($files) > 2) { /* The 2 accounts for . and .. */
        echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
        // All dirs
        foreach ($files as $file) {
            if (file_exists($path . $file) && $file != '.' && $file != '..' && is_dir($path . $file)) {
                $userreturnpath = trim(substr($path . $file, strlen($rowsettings['so_value_tx']), strlen($path . $file)));
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
            if (file_exists($path . $file) && $file != '.' && $file != '..' && !is_dir($path . $file) && strstr($file, '.htaccess')) {
                $ext = preg_replace('/^.*\./', '', $file);
                $htaccesspath = trim(substr($path, strlen($rowsettings['so_value_tx']), strlen($path . $file)));
                $rowpath = $zdbh->query("SELECT * FROM x_htaccess WHERE ht_dir_vc='" . substr($htaccesspath, 0, -1) . "' AND ht_deleted_ts IS NULL")->fetch();
                if ($rowpath) {
                    echo "<li class=\"file ext_$ext\"><a href=\"./?module=htpasswd&selected=Selected&show=Edit&other=" . $rowpath['ht_id_pk'] . "\" title=\"Edit Users\">" . htmlentities($file) . "</a></li>";
                } else {
                    echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($path . $file) . "\" title=\"Not a known Password File\"><font color=\"red\">" . htmlentities($file) . "</font></a></li>";
                }
            }
        }
        echo "</ul>";
    } else {
        echo "<font color=\"red\">No Directories Found!</font>";
    }
}
?>