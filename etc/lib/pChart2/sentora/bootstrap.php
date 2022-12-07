<?php

declare(strict_types=1);

if (php_sapi_name() != "cli") {
	chdir("../");
}

spl_autoload_register(function ($class_name) {
	$filename = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
	include $filename;
});

?>