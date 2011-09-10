<?php

class mymodule {

    static function EchoTest() {
        $string =  "This is a new way in which ZPanel code is executed from within modules!<br><br>Here is a random number for you... <strong>" .rand(0,1000). "</strong><br><br>";
        return strtoupper($string);
    }

}

?>
