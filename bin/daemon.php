<?php

/**
 * @package zpanelx
 * @subpackage bin -> daemon
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

// Lets start running the hooks!
if (!runtime_controller::IsCLI())
    echo "<pre>";
echo "Daemon is now running...";

// Everytime the daemon is run! (10 mins by default!)
runtime_hook::Execute("OnStartDaemonRun");
runtime_hook::Execute("OnDaemonRun");
runtime_hook::Execute("OnEndDaemonRun");

// Every hour
runtime_hook::Execute("OnStartDaemonHour");
runtime_hook::Execute("OnDaemonHour");
runtime_hook::Execute("OnEndDaemonHour");

// Every day
runtime_hook::Execute("OnStartDaemonDay");
runtime_hook::Execute("OnDaemonDay");
runtime_hook::Execute("OnEndDaemonDay");

// Every week
runtime_hook::Execute("OnStartDaemonWeek");
runtime_hook::Execute("OnDaemonWeek");
runtime_hook::Execute("OnEndDaemonWeek");

// Every month
runtime_hook::Execute("OnStartDaemonMonth");
runtime_hook::Execute("OnDaemonMonth");
runtime_hook::Execute("OnEndDaemonMonth");

// All hooks have been run!
echo "\nDaemon run complete!";

if (!runtime_controller::IsCLI())
    echo "</pre>";
exit;
?>
