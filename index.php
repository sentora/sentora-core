<?php

/**
 * The Sentora loader and default handler file.
 * @package Sentora
 * @subpackage core
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanel.com/)
 * @link http://www.zpanel.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 * @copyright Sentora Project (http://sentora.org)
 */
session_start();
require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
debug_phperrors::SetMode('dev');
require_once 'inc/dbc.inc.php';
debug_phperrors::SetMode(ctrl_options::GetSystemOption('debug_mode'));
require_once 'inc/init.inc.php';
//This is where we check the session for hi-jacking
if(!runtime_sessionsecurity::antiSessionHijacking()){
    exit(header("location: ./?sessionIssue"));
}
?>