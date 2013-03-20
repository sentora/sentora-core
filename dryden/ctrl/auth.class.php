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
     * @global db_driver $zdbh The ZPX database handle.
     * return bool
     */
    static function RequireUser() {
        global $zdbh;
        if (!isset($_SESSION['zpuid'])) {
            if (isset($_COOKIE['zUser'])) {
                self::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true);
            }
            runtime_hook::Execute('OnRequireUserLogin');
            $sqlQuery = "SELECT ac_usertheme_vc, ac_usercss_vc FROM 
                         x_accounts WHERE 
                         ac_user_vc = :zadmin";
            $bindArray = array(':zadmin' => 'zadmin');
            $zdbh->bindQuery($sqlQuery, $bindArray);
            $themeRow = $zdbh->returnRow();
            include 'etc/styles/' . $themeRow['ac_usertheme_vc'] . '/login.ztml';
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
    static function SetUserSession($zpuid = 0, $sessionSecuirty) {
        if (isset($zpuid)) {
            $_SESSION['zpuid'] = $zpuid;
            if($sessionSecuirty){
                //Implamentation of session security 
                runtime_sessionsecurity::setCookie();
                runtime_sessionsecurity::setUserIP();
                runtime_sessionsecurity::setUserAgent();
                runtime_sessionsecurity::setSessionSecurityEnabled(true);
            }else{
                //Implamentation of session security but set it as off 
                runtime_sessionsecurity::setCookie();
                runtime_sessionsecurity::setUserIP();
                runtime_sessionsecurity::setUserAgent();
                runtime_sessionsecurity::setSessionSecurityEnabled(false);
            }
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets the value of a given named session variable, if does not exist will create the session variable too.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $name The name of the session variable to set.
     * @param string $value The value of the session variable to set.
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
     * @global db_driver $zdbh The ZPX database handle.
     * @param string $username The username to use to authenticate with.
     * @param string $password The password to use to authenticate with.
     * @param bool $rememberme Remember the password for 30 days? (true/false)
     * @param bool $checkingcookie The authentication request has come from a set cookie.
     * @return mixed Returns 'false' if the authentication fails otherwise will return the user ID. 
     */
    static function Authenticate($username, $password, $rememberme = false, $iscookie = false, $sessionSecuirty) {
        global $zdbh;
        $sqlString = "SELECT * FROM 
                      x_accounts WHERE 
                      ac_user_vc = :username AND 
                      ac_pass_vc = :password AND 
                      ac_enabled_in = 1 AND 
                      ac_deleted_ts IS NULL";

        $bindArray = array(':username' => $username,
            ':password' => $password
        );

        $zdbh->bindQuery($sqlString, $bindArray);
        $row = $zdbh->returnRow();

        if ($row) {
            //Disabled till zpanel 10.0.3
            //runtime_sessionsecurity::sessionRegen();
            
            ctrl_auth::SetUserSession($row['ac_id_pk'], $sessionSecuirty);
            $log_logon = $zdbh->prepare("UPDATE x_accounts SET ac_lastlogon_ts=" . time() . " WHERE ac_id_pk=" . $row['ac_id_pk'] . "");
            $log_logon->execute();
            if ($rememberme) {
                setcookie("zUser", $username, time() + 60 * 60 * 24 * 30, "/");
                setcookie("zPass", $password, time() + 60 * 60 * 24 * 30, "/");
            }
            
            runtime_hook::Execute('OnGoodUserLogin');
            return $row['ac_id_pk'];
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
        
        unset($_COOKIE['zUserSaltCookie']);
        
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
