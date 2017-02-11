<?php
//////// START WORKFRAME STANDARD CONFIG (if entire domain is used for this WorkFrame app) ///////
define('WORKFRAME_PATH', __DIR__.'/../WorkFrame');
define('WORKFRAME_DEBUG', TRUE);
define('WORKFRAME_ENVIRONMENT', 'DEV'); // DEV or LIVE

define('WWW_HOST', 'local.workframe'); 
define('WWW_ROOT_PATH', ''); // No trailing forward slash
define('WWW_URL', 'http://'.WWW_HOST.WWW_ROOT_PATH);
define('WWW_PUBLIC_PATH', '/public');

define('APP_PATH', __DIR__);
define('APP_NAME', 'WorkFrame');
define('APP_CODENAME', 'workframe_example'); // Must be valid part of filename (for log)
define('APP_NAMESPACE', 'ExampleApp'); // Must match app dirname
define('APP_PUBLIC_PATH', __DIR__.'/../www/public/');

define('APP_BUILD', '1.0'); 
////// END WORKFRAME STANDARD CONFIG ///////


include(__DIR__.'/../vendor/autoload.php');

include(WORKFRAME_PATH . '/init.php');
