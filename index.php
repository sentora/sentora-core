<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';

/**
 * Whilst in development lets turn on PHP error reporting!
 */
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

/**
 * Register a logger object to handle all base logging within the application.
 * @todo Move this block into an include file? - Doesn't need to clutter up the index.
 */
global $zlo;
$zlo = new debug_logger();

/**
 * Lets register the global database handle we will use throughout the code.
 * $zdbh (Zpanel database handle) is the global database access handle!
 * @todo Maybe incorporate this into a seperate include?? - For now though its fine!
 */
global $zdbh;
try {
    $zdbh = new db_driver("mysql:host=$host;dbname=$dbname", $user, $pass);
    $zdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $zlo->method = "text";
    $zlo->logcode = "0100";
    $zlo->detail = "Unable to connect/authenticate against the database supplied!";
    $zlo->mextra = $e;
}

/**
 * There is debug infomation avaliable! - Lets write the info out and then reset the debug object!
 */
if ($zlo->hasInfo()) {

    $zlo->writeLog();
    $zlo->reset();
}
echo "Script finished running!";

# Set the error reporting to use the database...
$zlo->method = "database";


/**
 * Done all that we NEED too, now we just continue on...
 */
?>