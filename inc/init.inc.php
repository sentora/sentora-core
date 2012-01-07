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

if (isset($_GET['logout'])) {
    ctrl_auth::KillSession();
    ctrl_auth::KillCookies();
    runtime_hook::Execute('OnUserLogout');
    header("location: ./?loggedout");
    exit;
}

if (isset($_POST['inUsername'])) {
    if (!isset($_POST['inRemember'])) {
        $rememberdetails = false;
    } else {
        $rememberdetails = true;
    }
    ctrl_auth::Authenticate($_POST['inUsername'], md5($_POST['inPassword']), $rememberdetails, false);
}

if (isset($_COOKIE['zUser'])) {
    ctrl_auth::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true);
}

if (!isset($_SESSION['zpuid'])) {
    ctrl_auth::RequireUser();
}

$controller->Init();
?>
