<?php

/**
 * Just a temporary play ground for my web service implementation into ZPX.
 */
$raw_path = str_replace("\\", "/", dirname(__FILE__));
$root_path = str_replace("/bin", "/", $raw_path);
chdir($root_path);

// Include some files that we need.
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';


$test_xml ="<?xml version=\"1.0\"?>\n".
        "<xmws-version>3213121321</xmws-version>\n" .
        "<xmws-apikey>sdfdsfsdfsdfsfdsfd--</xmws-apikey>\n" .
        "<xmws-request>StaticDataReturnExample</xmws-request>\n" .
        "<xmws-response>ASDASDASD</xmws-response>\n" .
        "<xmws-authuser>test2</xmws-authuser>\n" .
        "<xmws-authpass>passwordff</xmws-authpass>\n" .
        "<xmws-content>Bobby Allen</xmws-content>";

//echo ws_generic::DoPostRequest('http://127.0.0.1/zpanelx/bin/api.php?m=test', $test_xml);
echo ws_generic::DoPostRequest('http://127.0.0.1/zpanelx/bin/api.php?m=test', $test_xml);
?>
