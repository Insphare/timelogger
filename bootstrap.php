<?php

$includePath = get_include_path();
$basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR;

$includePathDirectories = array(
	$basePath . 'libs',
);
set_include_path(get_include_path() . ':' . implode(':', $includePathDirectories));
include_once $basePath . 'libs' . DIRECTORY_SEPARATOR . 'Config.php';
include_once $basePath . 'libs' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$dirSetup = array(
	'report',
	'task',
	'cache'
);
foreach ($dirSetup as $dir) {
	if (file_exists($basePath . $dir)) {
		continue;
	}

	mkdir($basePath . $dir, 0777);
}

Config::set(Config::BASE_PATH, $basePath);

$autoLoader = new Autoloader($basePath);
spl_autoload_register(array(
	$autoLoader,
	'loadFileByClassName'
));
