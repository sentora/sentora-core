<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package PhpMyAdmin
 */

/**
 *
 */
require_once 'libraries/common.inc.php';

$response = PMA_Response::getInstance();
$header   = $response->getHeader();
$scripts  = $header->getScripts();
$scripts->addFile('import.js');

/**
 * Does the common work
 */
require 'libraries/server_common.inc.php';

$import_type = 'server';
require 'libraries/display_import.lib.php';

?>

