<?php
function __autoload($class_name)
{
    $path = str_replace("_", "/", $class_name);
    require_once "dryden/".$path.".class.php";
}
?>