<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controllerdebug
 *
 * @author ballen
 */
class ui_controllerdebug extends runtime_controller {

    function Template() {
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
