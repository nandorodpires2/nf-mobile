<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == "localhost") {
    $application_env = "development";
} elseif ($_SERVER['REMOTE_ADDR'] == '177.157.170.76') {
    $application_env = "testing";
} else {
    $application_env = "production";
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $application_env));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define path to emails layouts
defined('EMAILS_MOBILE')
    || define('EMAILS_MOBILE', APPLICATION_PATH . '/modules/mobile/views/emails');

// Define path to emails cron
defined('EMAILS_CRON')
    || define('EMAILS_CRON', APPLICATION_PATH . '/modules/cron/views/emails');

// Define path to public directory
defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define URL mobile
if ($application_env == "production") {
    defined('MOBILE_URL') || define('MOBILE_URL', "http://mobile.newfinances.com.br/");
} else {
    defined('MOBILE_URL') || define('MOBILE_URL', "http://localhost/nf-mobile");
}

// Define path to public directory
if ($application_env == 'testing') {
    defined('SYSTEM_URL') || define('SYSTEM_URL', 'http://newfinances2.newfinances.com.br/public/');
} elseif ($application_env == 'development') {
    defined('SYSTEM_URL') || define('SYSTEM_URL', 'http://localhost/newfinances2/public/');
} else {
    defined('SYSTEM_URL') || define('SYSTEM_URL', 'http://newfinances.com.br/');
}

if ($application_env == 'testing') {
    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../../../library'),
        get_include_path(),
    )));
} elseif ($application_env == 'development' ) {
    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../../library'),
        get_include_path(),
    )));
} else {
    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../../../library'),
        get_include_path(),
    )));
}

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();