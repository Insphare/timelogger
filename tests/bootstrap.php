<?php

// sets default include paths and enables autoloader
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
ini_set("memory_limit", "128M");

define('PHPUNIT_TEST_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('PHPUNIT_TEST_DIR_FIXTURES', PHPUNIT_TEST_DIR . 'fixtures' . DIRECTORY_SEPARATOR);

