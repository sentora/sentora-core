<?php

/**
 * Used to retrieve and serve a user's backup for download.
 *
 * @package Sentora
 * @author Jacob Gelling <jacobg830@icloud.com>
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

// Set infinite time limit as download can take a long time
set_time_limit(0);

// Limit memory used
ini_set('memory_limit', '256M');

// Require Dryden configs and components
require('../../../cnf/db.php');
require('../../../dryden/db/driver.class.php');
require('../../../dryden/debug/logger.class.php');
require('../../../dryden/sys/versions.class.php');
require('../../../dryden/ctrl/options.class.php');
require('../../../inc/dbc.inc.php');

// Get database object
try {
    $zdbh = new db_driver("mysql:host=" . $host . ";dbname=" . $dbname . "", $user, $pass);
} catch (PDOException $e) {
    echo '<h1>No database connection<h1>';
    exit;
}

// Ensure user is authenticated
session_start();
if (!$_SESSION['zpuid']) {
    echo '<h1>Unauthenticated<h1>';
    exit;
}

// Ensure request is valid
if (!isset($_GET['file'])) {
    echo '<h1>Invalid request</h1>';
    exit;
}

// Get username
$rows = $zdbh->prepare('SELECT `ac_user_vc` FROM `x_accounts` WHERE `ac_id_pk`=:userid');
$rows->bindParam(':userid', $_SESSION['zpuid']);
$rows->execute();
$result = $rows->fetch();
$username = $result['ac_user_vc'];

// Get backup name, directory and full path
$backupName = basename($_GET['file']);
$backupDir = ctrl_options::GetSystemOption('hosted_dir') . $username . '/backups/';
$backupPath = $backupDir. $backupName;

// Ensure file exists, is prefixed by username, contains "_", has a ".zip" extension and is not "." or ".."
if (   !file_exists($backupPath)
    || substr($backupName, 0, strlen($username)) !== $username
    || !strpos($backupName, "_")
    || substr($backupName, -4) !== '.zip'
    || $backupName === "."
    || $backupName === ".."
) {
    echo '<h1>Invalid file<h1>';
    exit;
}

// Set headers for download
header('Content-Disposition: attachment; filename=' . $backupName);
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($backupPath));

// Serve user the backup
if (sys_versions::ShowOSPlatformVersion() === "Windows") {
    readfile($backupPath);
} else {
    readfile_chunked($backupPath);
}

// Function to serve download in chunks, modified from PHP docs
// http://cn2.php.net/manual/en/function.readfile.php#52598
function readfile_chunked($file) {
    $chunksize = 1048576;
    $handle = fopen($file, 'rb');
    if ($handle === false) {
        return;
    }
    while (!feof($handle)) {
        echo fread($handle, $chunksize);
    }
    fclose($handle);
}
