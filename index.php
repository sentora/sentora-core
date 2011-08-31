<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
session_start();
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

$modulerunner = new ui_module();

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


/**
 * @todo Set the reporting method as per system configration for now however lets use "database".
 */
$zlo->method = "database";


/**
 * @todo We need to implement an authentication check here to ensure that the user is a valid user otherwise we need to redirect them to the login screen!
 */
$set_it = new ctrl_auth();
$set_it->SetUserSession(445);
ctrl_auth::RequireUser();

echo $_SESSION['zpuid'];

/**
 * Load the module or list all module icons if a module has not been requested to be loaded!
 */
if (ctrl_director::getCurrentModule())
    ui_module::getModule(ctrl_director::getCurrentModule());
#ui_modulelist::getOutput();
?>