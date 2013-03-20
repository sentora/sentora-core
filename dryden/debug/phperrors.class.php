<?php

/**
 * Development class enables PHP error reporting for ease of development!
 * @package zpanelx
 * @subpackage dryden -> debug
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class debug_phperrors {

    /**
     * Sets PHP error reporting to ON and displays ALL errors if set to 'dev' otherwise will disable all errors.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param str $mode Either 'dev' or 'prod', is left blank 'prod' is used by default.
     */
    static function SetMode($mode = '') {
        if ($mode == 'dev') {
            error_reporting('E_ALL');
            ini_set('error_reporting', E_ALL);
        } else {
            error_reporting('E_NONE');
            ini_set('error_reporting', E_NONE);
        }
    }

}

?>
