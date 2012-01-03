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

if ($zlo->hasInfo()) {

    $zlo->writeLog();
    $zlo->reset();
}

if ((isset($_POST['inUsername'])) && (!isset($_SESSION['zpuid']))) {
    if (!ctrl_auth::Authenticate($_POST['inUsername'], $_POST['inPassword'])) {
        runtime_hook::Execute('OnFailedUserLogin');
        ctrl_auth::RequireUser();
    } else {
        runtime_hook::Execute('OnUserLogin');
    }
} else {
    ctrl_auth::RequireUser();
}

if (isset($_GET['logout'])) {
    ctrl_auth::KillSession();
    runtime_hook::Execute('OnUserLogout');
    include 'etc/styles/zpanelx/login.ztml';
    exit;
}

$controller->Init();
?>
