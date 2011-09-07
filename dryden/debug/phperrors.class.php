<?php

/**
 * Development class enables PHP error reporting for ease of development!
 *
 * @package zpanelx
 * @subpackage dryden -> debug
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */
class debug_phperrors {

    static function SetMode($mode = '') {
        if ($mode == "dev") {
            error_reporting('E_ALL');
            ini_set('error_reporting', E_ALL);
        } elseif ($mode == "prod") {
            error_reporting('E_NONE');
            ini_set('error_reporting', E_NONE);
        } else {
            self::SetMode('prod');
        }
    }

}

?>
