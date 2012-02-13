<?php

/**
 * ZPPY - The ZPanel Package Manager Tool.
 * @package zpanelx
 * @subpackage core -> daemon
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
$raw_path = str_replace("\\", "/", dirname(__FILE__));
$root_path = str_replace("/bin", "/", $raw_path);
chdir($root_path);

// Include some files that we need.
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';

if (!runtime_controller::IsCLI())
    exit;

if ((isset($argv[1])) && ($argv[1] == 'install')) {
    echo "Module to be installed..\n";
}
if ((isset($argv[1])) && ($argv[1] == 'remove')) {
    echo "Module to be removed!\n";
}
if (($argv[1] == 'version') || ($argv[1] == '-v') || ($argv[1] == '--version')) {
    echo ctrl_options::GetOption('dbversion') . "\n";
}
if ((!isset($argv[1])) || ($argv[1] == 'help') || ($argv[1] == '-h') || ($argv[1] == '--help')) {
    echo "ZPanel Package Manager\n";
    echo "Copyright (c) 2008 - 2012 ZPanel Project\n";
    echo "http://www.zpanelcp.com/\n";
    echo "\n";
    echo "Usage: zppy [action] [modulename]\n";
    echo "Actions:\n";
    echo "  install [modulename]- Install a named module.\n";
    echo "  supgrade [modulename]- Updates a named module.\n";
    echo "  remove [modulename]- Remove a named module.\n";
    echo "  --version - Displays the Zpanel version\n";
    echo "  --help - Displays this text.\n";
}
exit;
?>