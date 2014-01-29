<?php

/**
 * The daemon initiator file.
 * @package zpanelx
 * @subpackage core -> daemon
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
set_time_limit(0);

$rawPath = str_replace("\\", "/", dirname(__FILE__));
$rootPath = str_replace("/bin", "/", $rawPath);
chdir($rootPath);

require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';

$daemonLog = new debug_logger();
$daemonLog->method = "file";
$daemonLog->logcode = "001";

if (!runtime_controller::IsCLI())
    echo "<pre>";
echo "Daemon is now running...";

$daemonLog->detail = "Daemon execution started...";
$daemonLog->writeLog();

runtime_hook::Execute("OnStartDaemonRun");
runtime_hook::Execute("OnDaemonRun");
runtime_hook::Execute("OnEndDaemonRun");

if (ctrl_options::GetSystemOption('daemon_hourrun') < (time() + 3599)) {
    runtime_hook::Execute("OnStartDaemonHour");
    runtime_hook::Execute("OnDaemonHour");
    runtime_hook::Execute("OnEndDaemonHour");
    ctrl_options::SetSystemOption('daemon_hourrun', time());
}

if (ctrl_options::GetSystemOption('daemon_dayrun') < (time() - 86399)) {
    runtime_hook::Execute("OnStartDaemonDay");
    runtime_hook::Execute("OnDaemonDay");
    runtime_hook::Execute("OnEndDaemonDay");
    ctrl_options::SetSystemOption('daemon_dayrun', time());
}

if (ctrl_options::GetSystemOption('daemon_weekrun') < (time() - 604799)) {
    runtime_hook::Execute("OnStartDaemonWeek");
    runtime_hook::Execute("OnDaemonWeek");
    runtime_hook::Execute("OnEndDaemonWeek");
    ctrl_options::SetSystemOption('daemon_weekrun', time());
}

if (ctrl_options::GetSystemOption('daemon_monthrun') < (time() - 2419199)) {
    runtime_hook::Execute("OnStartDaemonMonth");
    runtime_hook::Execute("OnDaemonMonth");
    runtime_hook::Execute("OnEndDaemonMonth");
    ctrl_options::SetSystemOption('daemon_monthrun', time());
}

echo "\nDaemon run complete!\n";

ctrl_options::SetSystemOption('daemon_lastrun', time());

$daemonLog->detail = "Daemon execution completed!";
$daemonLog->writeLog();

if (!runtime_controller::IsCLI())
    echo "</pre>";
exit;