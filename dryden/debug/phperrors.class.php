<?php

/**
 * @copyright 2014-2023 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Development class enables PHP error reporting for ease of development!
 * @package zpanelx
 * @subpackage dryden -> debug
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class debug_phperrors {

    /**
     * Sets PHP error reporting to ON and displays ALL errors if set to 'dev' otherwise will disable all errors.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param str $mode Either 'dev' or 'prod', is left blank 'prod' is used by default.
     */
    static function SetMode($mode = '') {
        if ($mode == 'dev') {
            error_reporting('1');
            ini_set('error_reporting', 1);
        } else {
            error_reporting('0');
            ini_set('error_reporting', 0);
        }
    }

}

?>
