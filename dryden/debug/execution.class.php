<?php

/**
 * @package zpanelx
 * @subpackage dryden -> debug
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class debug_execution {

    static function ScriptMemoryUsage() {
        $mem_usage = memory_get_usage(true);
        if ($mem_usage < 1024) {
            $retval = $mem_usage . " bytes";
        } elseif ($mem_usage < 1048576) {
            $retval = round($mem_usage / 1024, 2) . " kilobytes";
        } else {
            $retval = round($mem_usage / 1048576, 2) . " megabytes";
        }
        return $retval;
    }

}

?>
