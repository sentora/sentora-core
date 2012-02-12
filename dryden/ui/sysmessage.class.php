<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_sysmessage {

    /**
     * ui_sysmessage class is used to send preformatted html messages that can be styled in css.
	 * class and id default to zannounce, but can be changed as well.
     */

    static function shout($message, $class="zannounce", $id="zannounce") {
        runtime_hook::Execute('OnBeforeSysMessageShout');
	$line = "<div class=\"".$class."\" id=\"".$id."\">".$message."<a href=\"#\" class=\"zannounce_a\" id=\"zannounce_a\"></a></div>";
        runtime_hook::Execute('OnAfterSysMessageShout');
	return $line;
    }
}

?>
