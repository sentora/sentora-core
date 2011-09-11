<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of helloworld
 *
 * @author ballen
 */
class helloworld extends runtime_controller {
    
    static function GetHello(){
        global $controller;
        return "I said 'hello' to: ".$controller->GetControllerRequest('URL', 'helloto');
    }
    
}

?>
