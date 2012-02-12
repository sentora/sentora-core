<?php

/**
 * Module loader script for detecting and displaying the correct module using the Dryden framework, this handles the autolaoding of classes.
 * @package zpanelx
 * @subpackage dryden -> core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
global $starttime;
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

function __autoload($class_name) {
    $path = str_replace("_", "/", $class_name);
    if (file_exists("dryden/" . $path . ".class.php")) {
        require_once "dryden/" . $path . ".class.php";
    }
    if (isset($_GET['module'])) {
        if (file_exists("modules/" . $_GET['module'] . "/code/controller.ext.php")) {
            require_once "modules/" . $_GET['module'] . "/code/controller.ext.php";
        }
        $additional_path = str_replace("_", "/", $class_name);
        if (file_exists("modules/" . $_GET['module'] . "/code/" . $class_name . ".class.php")) {
            require_once "modules/" . $_GET['module'] . "/code/" . $class_name . ".class.php";
        }
    }
}

?>