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
         */
        if (!isset($_SESSION['zpuid'])) {
            header("location: " . $url_redir . "");
            exit;
        }
        return true;
    }

    
    function SetUserSession($zpuid=0) {
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

}

?>
