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
     * Returns the value submitted in the form and returns it to the view.
     */
    static function SubmitName() {
        global $controller;
        echo $controller->GetControllerRequest('FORM','MyName');
    }

}

?>
