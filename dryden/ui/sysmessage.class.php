<?php

/**
 * System message class, used to display CSS powered messages and warnings to the user through the template layer.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_sysmessage {

    /**
     * Used to send preformatted html messages that can be styled in CSS. The class and id tags for the DIV default to zannounce, but can be changed too for example 'zwarning'.
     * Bobby Allen (ballen@zpanelcp.com)
     * @param string $message The message to output to the screen.
     * @param string $class The CSS class name to use on the DIV.
     * @param string $id The CSS ID name to use on the DIV.
     * @return string The generated HTML source code.
     */
    static function shout($message, $class = "zannounce", $id = "zannounce") {
        runtime_hook::Execute('OnBeforeSysMessageShout');
        $line = "<div class=\"" . $class . "\" id=\"" . $id . "\">" . $message . "<a href=\"#\" class=\"zannounce_a\" id=\"zannounce_a\"></a></div>";
        runtime_hook::Execute('OnAfterSysMessageShout');
        return $line;
    }

}

?>