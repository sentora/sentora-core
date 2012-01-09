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

$daemon_log = new debug_logger();
$daemon_log->method = "file";
$daemon_log->logcode = "001";

// Lets start running the hooks!
if (!runtime_controller::IsCLI())
    echo "<pre>";
echo "Daemon is now running...";

$daemon_log->detail = "Daemon execution started...";
$daemon_log->writeLog();

// Everytime the daemon is run! (10 mins by default!)
runtime_hook::Execute("OnStartDaemonRun");
runtime_hook::Execute("OnDaemonRun");
runtime_hook::Execute("OnEndDaemonRun");

// Every hour
if (ctrl_options::GetOption('daemon_hourrun') < (time() + 3599)) {
    runtime_hook::Execute("OnStartDaemonHour");
    runtime_hook::Execute("OnDaemonHour");
    runtime_hook::Execute("OnEndDaemonHour");
    ctrl_options::SetSystemOption('daemon_hourrun', time());
}

// Every day
if (ctrl_options::GetOption('daemon_dayrun') < (time() - 86399)) {
    runtime_hook::Execute("OnStartDaemonDay");
    runtime_hook::Execute("OnDaemonDay");
    runtime_hook::Execute("OnEndDaemonDay");
    ctrl_options::SetSystemOption('daemon_dayrun', time());
}

// Every week
if (ctrl_options::GetOption('daemon_weekrun') < (time() - 604799)) {
    runtime_hook::Execute("OnStartDaemonWeek");
    runtime_hook::Execute("OnDaemonWeek");
    runtime_hook::Execute("OnEndDaemonWeek");
    ctrl_options::SetSystemOption('daemon_weekrun', time());
}

// Every month
if (ctrl_options::GetOption('daemon_monthrun') < (time() - 2419199)) {
    runtime_hook::Execute("OnStartDaemonMonth");
    runtime_hook::Execute("OnDaemonMonth");
    runtime_hook::Execute("OnEndDaemonMonth");
    ctrl_options::SetSystemOption('daemon_monthrun', time());
}

// All hooks have been run!
echo "\nDaemon run complete!\n";

// Ensure we update the database on when the daemon last run (I won't add this to a hook in zpx_core_module as it needs to be fail safe!)
ctrl_options::SetSystemOption('daemon_lastrun', time());

$daemon_log->detail = "Daemon execution completed!";
$daemon_log->writeLog();

if (!runtime_controller::IsCLI())
    echo "</pre>";
exit;
?>
