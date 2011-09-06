<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

/**
 * Register a logger object to handle all base logging within the application.
 * placed in with this dbc.inc.php file as it is used when creating the default $zdbh handle.
 */
global $zlo;
$zlo = new debug_logger();

/**
 * Lets register the global database handle we will use throughout the code.
 * $zdbh (Zpanel database handle) is the global database access handle!
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
?>
