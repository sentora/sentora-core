<?php

/**
 * Authentication class handles ZPanel authentication and handles user sessions.
 *
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
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
            include 'etc/styles/zpanelx/login.ztml';
            exit;
        }
        return true;
    }

    /**
     * Sets the users session ID.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param type $zpuid
     * @return bool 
     */
    static function SetUserSession($zpuid=0) {
        if (isset($zpuid)) {
            $_SESSION['zpuid'] = $zpuid;
            return true;
        } else {
            return false;
        }
    }

    /**
     * The main authentication mechanism, checks user and password against the database.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $zdbh
     * @param type $username
     * @param type $password
     * @param type $rememberme
     * @param type $checkingcookie
     * @return type 
     */
    function Authenticate($username, $password, $rememberme = false, $iscookie = false) {
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
            return $rows['ac_id_pk'];
        } else {
            return false;
        }
    }

    /**
     * Ends a user's server session.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return bool
     */
    static function KillSession() {
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
     * For security reasons, this blanks out the current object stored username and password.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return type 
     */
    function ResetCredentials() {
        $this->username = null;
        $this->password = null;
        return true;
    }

    /**
     * Returns the UID (User ID) of the current logged in user.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $controller
     * @return type 
     */
    static function CurrentUserID() {
        global $controller;
        return $controller->GetControllerRequest('USER', 'zpuid');
    }

}

?>
