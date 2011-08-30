<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
*/

class ui_module {
    /**
     * ui_module class is used to load in the module contents into the controller.
     */
    
    /**
     * Lets declare some variables here!
     */
    
    
    function __construct() {
        /**
         * Lets grab the controller properties and then we can use the values to set the class variables which we will then
         * use to build the UI.
         */

    }
    
    static function getModule($module){
        /**
         * This is where it outputs the module code to the view.
         * @var $module (string) is the folder path to the requested module.
         */
        
        echo "Loading module: " .$module. "";
    }
    
    
    
}

?>
