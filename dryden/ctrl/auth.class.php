<?php

/**
 * Authentication class handles ZPanel authentication and handles user sessions.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_auth {

    /**
     * Checks that the server has a valid session for the user if not it will redirect to the login screen.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * return bool
     */
    static function RequireUser() {
        if (!isset($_SESSION['zpuid'])) {
            if (isset($_COOKIE['zUser'])) {
                self::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true);
            }
            runtime_hook::Execute('OnRequireUserLogin');
            include 'etc/styles/zpanelx/login.ztml';
            exit;
        }
        return true;
    }

    /**
     * Sets a user session ID.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param int $zpuid The ZPanel user account ID to set the session as.
     * @return bool 
     */
    static function SetUserSession($zpuid = 0) {
        if (isset($zpuid)) {
            $_SESSION['zpuid'] = $zpuid;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets the value of a given named session variable, if does not exist will create the session variable too.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param str $name The name of the session variable to set.
     * @param str $value The value of the session variable to set.
     * @return boolean 
     */
    static function SetSession($name, $value = "") {
        if (isset($name)) {
            $_SESSION['' . $name . ''] = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * The main authentication mechanism, checks username and password against the database and logs the user in on a successful authenitcation request.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global obj $zdbh The ZPX database handle.
     * @param str $username The username to use to authenticate with.
     * @param str $password The password to use to authenticate with.
     * @param bool $rememberme Remember the password for 30 days? (true/false)
     * @param bool $checkingcookie The authentication request has come from a set cookie.
     * @return mixed Returns 'false' if the authentication fails otherwise will return the user ID. 
     */
    static function Authenticate($username, $password, $rememberme = false, $iscookie = false) {
        global $zdbh;
        $rows = $zdbh->query("select * from x_accounts where ac_user_vc = '$username' AND ac_pass_vc = '$password' AND ac_enabled_in = 1 AND ac_deleted_ts IS NULL")->fetch();
        if ($rows) {
            ctrl_auth::SetUserSession($rows['ac_id_pk']);
            $log_logon = $zdbh->prepare("UPDATE x_accounts SET ac_lastlogon_ts=" . time() . " WHERE ac_id_pk=" . $rows['ac_id_pk'] . "");
            $log_logon->execute();
            if ($rememberme) {
                setcookie("zUser", $username, time() + 60 * 60 * 24 * 30, "/");
                setcookie("zPass", $password, time() + 60 * 60 * 24 * 30, "/");
            }
            runtime_hook::Execute('OnGoodUserLogin');
            return $rows['ac_id_pk'];
        } else {
            runtime_hook::Execute('OnBadUserLogin');
            return false;
        }
    }

    /**
     * Destroys a session and ends a user's Zpanel session.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return bool
     */
    static function KillSession() {
        runtime_hook::Execute('OnUserLogout');
        $_SESSION['zpuid'] = null;
        return true;
    }

    /**
     * Deletes the authentication 'rememberme' cookies.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return bool
     */
    static function KillCookies() {
        setcookie("zUser", '', time() - 3600, "/");
        setcookie("zPass", '', time() - 3600, "/");
        unset($_COOKIE['zUser']);
        unset($_COOKIE['zPass']);
        return true;
    }

    /**
     * Returns the UID (User ID) of the current logged in user.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global obj $controller The Zpanel controller object.
     * @return int The current user's session ID. 
     */
    static function CurrentUserID() {
        global $controller;
        return $controller->GetControllerRequest('USER', 'zpuid');
    }

}

?>
