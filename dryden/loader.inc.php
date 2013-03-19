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
$mtime = explode(' ', microtime());
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

function __autoload($class_name) {
    $path = 'dryden/' . str_replace('_', '/', $class_name) . '.class.php';
    if (file_exists($path)) {
        require_once $path;
    }

    if (isset($_GET['module'])) {
        $CleanModuleName = fs_protector::SanitiseFolderName($_GET['module']);

        $ControlerPath = 'modules/' . $CleanModuleName . '/code/controller.ext.php';
        if (file_exists($ControlerPath)) {
            require_once $ControlerPath;
        }

        $ModulePath = 'modules/' . $CleanModuleName . '/code/' . $class_name . '.class.php';
        if (file_exists($ModulePath)) {
            require_once $ModulePath;
        }
    }
}

?>
