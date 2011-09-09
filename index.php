<?php

/**
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

session_start();
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';

/**
 * Whilst in development lets turn on PHP error reporting!
 */
debug_phperrors::SetMode('dev');

require_once 'inc/dbc.inc.php';
require_once 'inc/init.inc.php';

ui_templateparser::Generate("etc/styles/zpanel6");

$array = new runtime_dataobject;
$array->addItemValue('version', sys_versions::ShowApacheVersion());
$array->addItemValue('funk', 'Fjasdjklasjdklasjl this sheet boss');
$array->addItemValue('test', 'Fuck this sheet boss');

$placehodder =  $array->getDataObject();
echo $placehodder['version'];
echo $placehodder['test'];


/**
 * Testing adding some test system options.
 */

#ctrl_options::SetSystemOption('install_path', 'E:/ZPanel/beta');
#::SetSystemOption('install_path2', 'C:/ZPanel/beta2');

echo ctrl_options::GetOption('install_path');
?>