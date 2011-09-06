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

require_once 'inc/dbc.inc.php';
require_once 'inc/init.inc.php';

ui_templateparser::Generate("tpl/example");

?>