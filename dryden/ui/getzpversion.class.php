<?php

/**
 * Test dynamic template placeholder from a class!
 */

/**
 * Description of getzpversion
 *
 * @author ballen
 */
class ui_getzpversion {
    
    function Template(){
        $version = ctrl_options::GetOption('dbversion');
        $retval = "The version of ZPanel your server is currently running is: " .$version;
        return $retval;
    }

}

?>
