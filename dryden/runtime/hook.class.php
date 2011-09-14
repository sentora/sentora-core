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

    function Execute($name) {
        $mod_folder = ctrl_options::GetOption('zpanel_root') . "modules/*/code/{controller.ext.php}";

        foreach (glob($mod_folder, GLOB_BRACE) as $hook_file) {

            // Check that the file exists and the class/method exists before running it!
            if (file_exists($mod_folder)) {
                include($mod_folder);
                call_user_method('hook' . $name, 'module_controller');
            }
        }
    }

}

?>
