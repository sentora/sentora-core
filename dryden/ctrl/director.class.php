<?php

/**
 * Director class handles the 'framework' POST, GET and SESSION variables and handles and advertises them accordingly.
 *
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */

class ctrl_director {

    /**
     * This is used to check if a module has been requested in the framework.
     * @return type - Returns false if no module has been requested otherwise will return the module name. 
     */
    static function getCurrentModule() {
        if (isset($_GET['module']))
            return $_GET['module'];
        return false;
    }

}

?>
