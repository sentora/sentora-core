<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

global $controller;
$controller = new runtime_controller();


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
 * at the moment however, we force authentication with a test account!
 */
ctrl_auth::KillSession();

$zpauth = new ctrl_auth;
$zpauth->username = "test2";
$zpauth->password = "password";
$zpauth->Authenticate();


ctrl_auth::RequireUser();

/**
 * Initiate the controller to handle all requests and pass infomation to the reuired places etc.
 */
$controller->Init();
?>
