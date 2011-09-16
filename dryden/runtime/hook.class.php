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
        $mod_folder = "modules/*/hooks/{" .$name. ".hook.php}";
        foreach (glob($mod_folder, GLOB_BRACE) as $hook_file) {
            if (file_exists($hook_file)) {
                include $hook_file;
            }
        }
    }

}

?>
