<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Class to display controller debugging in the template layer.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_controllerdebug extends runtime_controller {

    /**
     * Template placeholder to display controller debug infomation.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global obj $controller The controller object.
     * @return string HTML output to display the controller debug infomation in a pretty way
     */
    static function Template() {
        global $controller;
        if ($controller->OutputControllerDebug()) {
            $controllerdebug = $controller->OutputControllerDebug();
            $retval = "<!-- BEGIN DEBUG -->
	<div class=\"zdebug\" id=\"zdebug\">" . $controllerdebug . "</div>
	<!-- END DEBUG -->";
            return $retval;
        }
    }

}

?>
