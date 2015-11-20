<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

date_default_timezone_set("UTC");
require_once 'config/inc/common.php';
if (IS_TEST) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

putenv('ZF2_PATH=' . ZEND2_DIR);

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/batch.config.php')->run();
