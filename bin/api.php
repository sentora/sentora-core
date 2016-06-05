<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * The the main ZPanel(X) (M)odular (W)eb (S)ervice controller.
 * @package zpanelx
 * @subpackage core -> api
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
$rawPath = str_replace("\\", "/", dirname(__FILE__));
$rootPath = str_replace("/bin", "/", $rawPath);
chdir($rootPath);

require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';

debug_phperrors::SetMode('dev');

if (file_exists('modules/' . fs_protector::SanitiseFolderName($_GET['m']) . '/code/webservice.ext.php')) {
    include 'modules/' . fs_protector::SanitiseFolderName($_GET['m']) . '/code/controller.ext.php';
    include 'modules/' . fs_protector::SanitiseFolderName($_GET['m']) . '/code/webservice.ext.php';
    $api = new webservice();

    if ($api->wsdataarray['request'] == '') {
        $response_nomethod = new runtime_dataobject;
        $response_nomethod->addItemValue('response', '1106');
        $response_nomethod->addItemValue('content', 'No \'request\' method was recieved');
        $api->SendResponse($response_nomethod->getDataObject());
        die();
    }

    if ($api->CheckServerAPIKey()) {
        if (method_exists($api, $api->wsdataarray['request'])) {
            $api->SendResponse(call_user_func(array($api, '' . $api->wsdataarray['request'] . '')));
        } else {
            $response_nomethod = new runtime_dataobject;
            $response_nomethod->addItemValue('response', '1102');
            $response_nomethod->addItemValue('content', 'Request not found');
            $api->SendResponse($response_nomethod->getDataObject());
        }
    } else {
        $response_nokey = new runtime_dataobject;
        $response_nokey->addItemValue('response', '1103');
        $response_nokey->addItemValue('content', 'Server API key authentication failed');
        $api->SendResponse($response_nokey->getDataObject());
    }
} else {
    echo "No modular web service found using this request URL (" . htmlspecialchars($_SERVER['REQUEST_URI']) . ")";
}
