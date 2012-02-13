<?php

/**
 * The web gui initiation script.
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
global $controller, $zdbh;
$controller = new runtime_controller();

if ($zlo->hasInfo()) {
    $zlo->writeLog();
    $zlo->reset();
}

if (isset($_GET['logout'])) {
    ctrl_auth::KillSession();
    ctrl_auth::KillCookies();
    header("location: ./?loggedout");
    exit;
}

if (isset($_GET['returnsession'])) {
    if (isset($_SESSION['ruid'])) {
        ctrl_auth::SetUserSession($_SESSION['ruid']);
        $_SESSION['ruid'] = null;
    }
    header("location: ./");
    exit;
}

if (isset($_POST['inForgotPassword'])) {
    $randomkey = sha1(microtime());
    $result = $zdbh->query("SELECT ac_id_pk, ac_user_vc, ac_email_vc  FROM x_accounts WHERE ac_email_vc = '" . $_POST['inForgotPassword'] . "'")->Fetch();
    if ($result) {
        $zdbh->exec("UPDATE x_accounts SET ac_resethash_tx = '" . $randomkey . "' WHERE ac_id_pk=" . $result['ac_id_pk'] . "");

        $phpmailer = new sys_email();
        $phpmailer->Subject = "Control Panel Password Reset";
        $phpmailer->Body = "Hi " . $result['ac_user_vc'] . ",
            
        You or somebody pretending to be you has requested a password reset link to be sent for your web hosting control panel login at: " . ctrl_options::GetOption('cp_url') . "
            
        If you wish to proceed with the password reset on your account please use this link below to be taken to the password reset page.
            
        " . ctrl_options::GetOption('cp_url') . "/?resetkey=" . $randomkey . "
            
        ";
        $phpmailer->AddAddress($result['ac_email_vc']);
        $phpmailer->SendEmail();
        runtime_hook::Execute('OnRequestForgotPassword');
    }
}

if (isset($_POST['inConfEmail'])) {
    $result = $zdbh->query("SELECT ac_id_pk FROM x_accounts WHERE ac_email_vc = '" . $_POST['inConfEmail'] . "' AND ac_resethash_tx = '" . $_GET['resetkey'] . "'")->Fetch();
    if ($result) {
        $zdbh->exec("UPDATE x_accounts SET ac_resethash_tx = '', ac_pass_vc= '" . md5($_POST['inNewPass']) . "' WHERE ac_id_pk=" . $result['ac_id_pk'] . "");
        runtime_hook::Execute('OnSuccessfulPasswordReset');
    } else {
        runtime_hook::Execute('OnFailedPasswordReset');
    }
    header("location: ./?passwordreset");
    exit();
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

runtime_hook::Execute('OnBeforeControllerInit');
$controller->Init();
?>
