<?php

/*
 * A simple example OnUserLogin hook
 * 
 */

$test_logger = new debug_logger();
$test_logger->method = "file"; // This will log something to the plain text file log!
$test_logger->logcode = "001"; // The default ERROR code for just 'informal' text.
$test_logger->detail = "A user just logged in! The user ID is: " .$_SESSION['zpuid']. "";
$test_logger->writeLog();


?>
