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

    var $username;
    var $password;

    /**
     * Checks that the server has a valid session for the user if not it will
     * redirect to a given page/URL.
     */
    static function RequireUser($url_redir="") {
        /**
         * @todo Add a default redirect URL if none is specified!
         */
        if (!isset($_SESSION['zpuid'])) {
            header("location: " . $url_redir . "");
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

    function Authenticate() {
        /**
         * Authetnicate against the database with the supplied user credentials.
         */
        global $zdbh;
        $rows = $zdbh->query("select * from x_accounts where ac_user_vc = '$this->username' AND ac_pass_vc = '$this->password'")->fetch();
        if ($rows) {
            $this->SetUserSession($rows['ac_id_pk']);
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
    
    static function CurrentUserID(){
        global $controller;
        return $controller->GetControllerRequest('USER', 'zpuid');
    }

}

?>
