<?php

/**
 * System message class, used to display CSS powered messages and warnings to the user through the template layer.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_sysmessage {

    /**
     * Show HTML Alert Messages
     * Jason Davis (jason.davis.fl@gmail.com)
     * @param string $message The message to output to the screen.
     * @param string $class The CSS class name to use on the DIV.
     * @param string $title An Optional Heading/Title Message
     * @param string $closeBtn Optional TRUE or FALSE to show a Close button or not
     *
     * @return string The generated HTML source code.
     */
    static function shout($message,  $class = "zannounce", $title = '', $closeBtn = true) {

        // Convert ZPanel CSS Class to Bootstrap Class
        switch ($class) {
            case 'zannounce':
            case 'zannounceinfo':
            case 'alert-info':
                $class = 'alert-info';
                break;
            case 'zannounceerror':
            case 'alert-error':
                $class = 'alert-danger';
                break;
            case 'zannouncesuccess':
            case 'alert-success':
            case 'zannounceok':
                $class = 'alert-success';
                break;
            case 'zannounceprimary':
            case 'alert-primary':
                $class = 'alert-primary';
                break;
            case 'notice':
                $class = 'alert-info notice-manager-alert hidden';
                break;
            default:
                $class = 'alert-info';
        }

        runtime_hook::Execute('OnBeforeSysMessageShout');
        $line = '<div class="alert alert-block '. $class. '">';
        $heading = $title ? '<h4>'.$title.'</h4>' : '';
        $closeBtn = $closeBtn ? '<button type="button" class="close" data-dismiss="alert">×</button>' : '';
        $line .= $closeBtn . $heading.'<p>' .$message. '</p></div>';
        runtime_hook::Execute('OnAfterSysMessageShout');
        return $line;
    }
}

?>
