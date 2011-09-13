<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of modulelist
 *
 * @author ballen
 */
class ui_tpl_modulelist {

    function Template() {
        global $controller;
        if (!$controller->GetControllerRequest('URL', 'module')) {
            return ui_moduleloader::GetModuleCats();
        }
    }

}

?>
