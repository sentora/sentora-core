<?php
echo "\nAn example output link in a hook file (outputs to the screen) - this is in /modules/test/hooks/OnStartDaemonRun.hook.php";

$test_logger = new debug_logger();
$test_logger->method = "file"; // This will log something to the plain text file log!
$test_logger->logcode = "001";
$test_logger->detail = "Just to show how the hook file in modules/test/hooks/OnStartDaemonRun.hook.php writes to the log file when its run!";
$test_logger->writeLog();

?>