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
if ((isset($_POST['inUsername'])) && (!isset($_SESSION['zpuid']))) {
    # Log the user in here!
    ctrl_auth::SetUserSession(2);
} else {
    ctrl_auth::RequireUser();
}

/**
 * Kills the current user session (logs the user out if the URL param 'logout' is called).
 */
if ((isset($_GET['logout']))) {
    ctrl_auth::KillSession();
    include 'etc/styles/zpanelx/login.ztml';
    exit;
}

/**
 * Initiate the controller to handle all requests and pass infomation to the reuired places etc.
 */
$controller->Init();
?>
