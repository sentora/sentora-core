<?php

/**
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_hook {

    static function Execute($name) {
        $mod_folder = "modules/*/code/{hook.ext.php}";
        foreach (glob($mod_folder, GLOB_BRACE) as $hook_file) {
            // Check that the file exists and the class/method exists before running it!
            if (file_exists($hook_file)) {
                $class_file = include($hook_file);
                if (method_exists('module_hooks', 'hook' . $name . '')) {
                    if (call_user_func(array('module_hooks', 'hook' . $name . ''))) {
                        echo "Executed hook$name in file: $hook_file";
                    }
                }
                unset($class_file);
            }
        }
    }

}

?>
