<?php

/**
 * Bobbys test API server.
 */
$raw_path = str_replace("\\", "/", dirname(__FILE__));
$root_path = str_replace("/bin", "/", $raw_path);
chdir($root_path);

// Include some files that we need.
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';

/**
 * Now we load the modules webservice extentsion class.
 */
require_once 'modules/' . $_GET['m'] . '/code/webservice.ext.php';
$test = new webservice();

/**
 * We will automate this soon based on the content of the XML request but for now we are calling it statically.
 */
if (!$test->CheckServerAPIKey()) {
    $test->StaticDataReturnExample();
} else {
    $response_nokey = new runtime_dataobject;
    $response_nokey->addItemValue('responsecode', '1103');
    $response_nokey->addItemValue('content', '');
    $test->SendResponse($response_nokey->getDataObject());
}
?>
