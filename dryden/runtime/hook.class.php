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

    /**
     * Executes a hook file at the called position.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param type $name 
     * @return void
     */
    static function Execute($name) {
        $hook_log = new debug_logger();
        $mod_folder = "modules/*/hooks/{" . $name . ".hook.php}";
        $hook_log->method = ctrl_options::GetOption('logmode');
        $hook_log->logcode = "861";
        foreach (glob($mod_folder, GLOB_BRACE) as $hook_file) {
            if (file_exists($hook_file)) {
                $hook_log->detail = "Hook file executed (" .$hook_file. ")";
                include $hook_file;
                $hook_log->writeLog();
            }
        }
    }

}

?>
