<?php

/**
 * The web gui initiation script.
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
global $controller, $zdbh, $zlo;
$controller = new runtime_controller();

$zlo->method = ctrl_options::GetSystemOption('logmode');
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
        ctrl_auth::SetUserSession($_SESSION['ruid'], runtime_sessionsecurity::getSessionSecurityEnabled());
        $_SESSION['ruid'] = null;
    }
    header("location: ./");
    exit;
}

if (isset($_POST['inForgotPassword'])) {
    runtime_csfr::Protect();
    $randomkey = runtime_randomstring::randomHash();
    $forgotPass = runtime_xss::xssClean($_POST['inForgotPassword']);
    $sth = $zdbh->prepare("SELECT ac_id_pk, ac_user_vc, ac_email_vc  FROM x_accounts WHERE ac_email_vc = :forgotPass");
    $sth->bindParam(':forgotPass', $forgotPass);
    $sth->execute();
    $rows = $sth->fetchAll();
    if ($rows) {
        $result = $rows['0'];
        $zdbh->exec("UPDATE x_accounts SET ac_resethash_tx = '" . $randomkey . "' WHERE ac_id_pk=" . $result['ac_id_pk'] . "");
        if (isset($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $phpmailer = new sys_email();
        $phpmailer->Subject = "Hosting Panel Password Reset";
        $phpmailer->Body = "Hi " . $result['ac_user_vc'] . ",
            
You, or somebody pretending to be you, has requested a password reset link to be sent for your web hosting control panel login.
        
If you wish to proceed with the password reset on your account, please use the link below to be taken to the password reset page.
            
" . $protocol . ctrl_options::GetSystemOption('zpanel_domain') . "/?resetkey=" . $randomkey . "


                ";
        $phpmailer->AddAddress($result['ac_email_vc']);
        $phpmailer->SendEmail();
        runtime_hook::Execute('OnRequestForgotPassword');
    }
}

if (isset($_POST['inConfEmail'])) {
    runtime_csfr::Protect();
    $sql = $zdbh->prepare("SELECT ac_id_pk FROM x_accounts WHERE ac_email_vc = :email AND ac_resethash_tx = :resetkey AND ac_resethash_tx IS NOT NULL");
    $sql->bindParam(':email', $_POST['inConfEmail']);
    $sql->bindParam(':resetkey', $_GET['resetkey']);
    $sql->execute();
    $result = $sql->fetch();

    $crypto = new runtime_hash;
    $crypto->SetPassword($_POST['inNewPass']);
    $randomsalt = $crypto->RandomSalt();
    $crypto->SetSalt($randomsalt);
    $secure_password = $crypto->CryptParts($crypto->Crypt())->Hash;

    if ($result) {
        $sql = $zdbh->prepare("UPDATE x_accounts SET ac_resethash_tx = '', ac_pass_vc = :password, ac_passsalt_vc = :salt WHERE ac_id_pk = :uid");
        $sql->bindParam(':password', $secure_password);
        $sql->bindParam(':salt', $randomsalt);
        $sql->bindParam(':uid', $result['ac_id_pk']);
        $sql->execute();
        runtime_hook::Execute('OnSuccessfulPasswordReset');
    } else {
        runtime_hook::Execute('OnFailedPasswordReset');
    }
    header("location: ./?passwordreset");
    exit();
}

if (isset($_POST['inUsername'])) {
    if (ctrl_options::GetSystemOption('login_csfr') == 'false')
        runtime_csfr::Protect();

    $rememberdetails = isset($_POST['inRemember']);
    $inSessionSecuirty = isset($_POST['inSessionSecuirty']);

    $sql = $zdbh->prepare("SELECT ac_passsalt_vc FROM x_accounts WHERE ac_user_vc = :username AND ac_deleted_ts IS NULL");
    $sql->bindParam(':username', $_POST['inUsername']);
    $sql->execute();
    $result = $sql->fetch();
    $crypto = new runtime_hash;
    $crypto->SetPassword($_POST['inPassword']);
    $crypto->SetSalt($result['ac_passsalt_vc']);
    $secure_password = $crypto->CryptParts($crypto->Crypt())->Hash;

    if (!ctrl_auth::Authenticate($_POST['inUsername'], $secure_password, $rememberdetails, false, $inSessionSecuirty)) {
        header("location: ./?invalidlogin");
        exit();
    }
}

if (isset($_COOKIE['zUser'])) {
    
    if (isset($_COOKIE['zSec'])) {
        if($_COOKIE['zSec'] == false) {
            $secure = false;
        } else {
            $secure = true;
        }
    }else{
        $secure = true;
    }
    
    ctrl_auth::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true, $secure);
}

if (!isset($_SESSION['zpuid'])) {
    ctrl_auth::RequireUser();
}


runtime_hook::Execute('OnBeforeControllerInit');
$controller->Init();
ui_templateparser::Generate("etc/styles/" . ui_template::GetUserTemplate());
?>
