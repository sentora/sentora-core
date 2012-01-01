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
     * Checks that the server has a valid session for the user if not it will
     * redirect to a given page/URL.
     */
    static function RequireUser($url_redir="") {
        /**
         * @todo Add a default redirect URL if none is specified!
         * @todo Also check that session data hasn't been used to store the account details.
         */
        if (!isset($_SESSION['zpuid'])) {
            include 'etc/styles/zpanelx/login.ztml';
            exit;
        }
        return true;
    }

    static function SetUserSession($zpuid=0) {
        if (isset($zpuid)) {
            $_SESSION['zpuid'] = $zpuid;
            return true;
        } else {
            /**
             * @todo Use the debug_logger class to throw an error here as no ZPUID has been set previously!
             */
            return false;
        }
    }

    function Authenticate($username, $password) {
        /**
         * Authetnicate against the database with the supplied user credentials.
         * @todo Check that the 'remember me' tick box hasn't been ticked.
         */
        global $zdbh;  
        $password = md5($password);
        $rows = $zdbh->query("select * from x_accounts where ac_user_vc = '$username' AND ac_pass_vc = '$password'")->fetch();
        if ($rows) {
            ctrl_auth::SetUserSession($rows['ac_id_pk']);
            runtime_hook::Execute('OnUserLogin');
           return true;
        } else {
            return false;
        }
    }

    static function KillSession() {
        /**
         * Destroy the user's session.
         */
        $_SESSION['zpuid'] = null;
        return true;
    }

    function ResetCredentials() {
        /**
         * Used to clean out the username and password variables for security reasons!
         */
        $this->username = null;
        $this->password = null;
        return true;
    }

    static function CurrentUserID() {
        global $controller;
        return $controller->GetControllerRequest('USER', 'zpuid');
    }

}

?>
