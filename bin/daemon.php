<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * The daemon initiator file.
 * @package zpanelx
 * @subpackage core -> daemon
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 *
 * Changes P.Peyremorte
 * - added timestamp of begin and end of dameon run for logfile
 * - corrected OnDaemonHour that occured on each cron run (5 min,)
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

$dateformat = ctrl_options::GetSystemOption('Sentora_df');

if (!runtime_controller::IsCLI())
    echo "<pre>";
echo "Daemon is now running... (".date($dateformat).")\n";

$daemonLog->detail = "Daemon execution started...";
$daemonLog->writeLog();

runtime_hook::Execute("OnStartDaemonRun");
runtime_hook::Execute("OnDaemonRun");
runtime_hook::Execute("OnEndDaemonRun");

if (time() >= ctrl_options::GetSystemOption('daemon_hourrun') + 3600) {
    ctrl_options::SetSystemOption('daemon_hourrun', time());
    runtime_hook::Execute("OnStartDaemonHour");
    runtime_hook::Execute("OnDaemonHour");
    runtime_hook::Execute("OnEndDaemonHour");
}

if (time() >= ctrl_options::GetSystemOption('daemon_dayrun') + 24*3600) {
    ctrl_options::SetSystemOption('daemon_dayrun', time());
    runtime_hook::Execute("OnStartDaemonDay");
    runtime_hook::Execute("OnDaemonDay");
    runtime_hook::Execute("OnEndDaemonDay");
}

if (time() >= ctrl_options::GetSystemOption('daemon_weekrun') + 7*24*3600) {
    ctrl_options::SetSystemOption('daemon_weekrun', time());
    runtime_hook::Execute("OnStartDaemonWeek");
    runtime_hook::Execute("OnDaemonWeek");
    runtime_hook::Execute("OnEndDaemonWeek");
}

if (time() >= ctrl_options::GetSystemOption('daemon_monthrun') + 30*24*3600) {
    ctrl_options::SetSystemOption('daemon_monthrun', time());
    runtime_hook::Execute("OnStartDaemonMonth");
    runtime_hook::Execute("OnDaemonMonth");
    runtime_hook::Execute("OnEndDaemonMonth");
}
echo "\nDaemon run complete! (" . date($dateformat) . ")\n";

ctrl_options::SetSystemOption('daemon_lastrun', time());

$daemonLog->detail = "Daemon execution completed!";
$daemonLog->writeLog();

if (!runtime_controller::IsCLI())
    echo "</pre>";
exit;
