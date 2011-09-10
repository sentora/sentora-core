<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
function __autoload($class_name) {
    $path = str_replace("_", "/", $class_name);
    if (file_exists("dryden/" . $path . ".class.php")) {
        require_once "dryden/" . $path . ".class.php";
    }


    /**
     * If a module has been called and is running we need to include classe's from the module's 'code' folder.
     */
    if (ctrl_director::getCurrentModule()) {
        $additional_path = str_replace("_", "/", $class_name);
        if (file_exists("modules/" . ctrl_director::getCurrentModule() . "/code/" . $class_name . ".class.php")) {
            require_once "modules/" . ctrl_director::getCurrentModule() . "/code/" . $class_name . ".class.php";
        }
    }
}

?>