<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class module_controller {

    /**
     * We use this 'name' variable to store tha name for the 
     * @var type 
     */
    static $name;
    static $isdebug;

    /**
     * This is the 'action' class as prefixed with 'do'
     */
    static function doSubmitName() {
        global $controller;
        self::$name = $controller->GetControllerRequest('FORM', 'MyName');
        if ($controller->GetControllerRequest('URL', 'debug')) {
            self::$isdebug = 1;
        } else {
            self::$isdebug = 0;
        }
    }

    /**
     * The 'return' class as prefixed with 'get' but called within the module without the prefix!
     */
    static function getSubmitName() {
        if (!fs_director::CheckForEmptyValue(self::$name))
            return ui_sysmessage::shout("Your name is: " . self::$name . " and debugging is set to: " . self::$isdebug . "");
        return;
    }

}

?>
