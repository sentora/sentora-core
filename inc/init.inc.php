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

if (isset($_POST['inForgotPassword'])) {

    /**
     * Add in functionality here once Russell has completed the Forgot password panel.
     */
    $phpmailer = new sys_email();
    $phpmailer->Subject = "Control Panel Password Reset";
    $phpmailer->Body = "I think you might have forgotten your password?";
    $phpmailer->AddAddress('bobbyallen.uk@gmail.com');

    $phpmailer->SendEmail();
}

if (isset($_POST['inUsername'])) {
    if (!isset($_POST['inRemember'])) {
        $rememberdetails = false;
    } else {
        $rememberdetails = true;
    }
    ctrl_auth::Authenticate($_POST['inUsername'], md5($_POST['inPassword']), $rememberdetails, false);
    runtime_hook::Execute('OnUserLogin');
}

if (isset($_COOKIE['zUser'])) {
    ctrl_auth::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true);
    runtime_hook::Execute('OnUserLogin');
}

if (!isset($_SESSION['zpuid'])) {
    ctrl_auth::RequireUser();
}

$controller->Init();
?>
