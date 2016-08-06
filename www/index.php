<?php
//////// START WORKFRAME STANDARD CONFIG (if entire domain is used for this WorkFrame app) ///////
define('WORKFRAME_PATH', __DIR__.'/../WorkFrame');
define('WORKFRAME_DEBUG', TRUE);
define('WORKFRAME_ENVIRONMENT', 'DEV'); // DEV or LIVE

define('WWW_ROOT_PATH', ''); // No trailing forward slash
define('WWW_PUBLIC_PATH', '/public');

define('APP_PATH', __DIR__.'/../ExampleApp');
define('APP_NAME', 'Example app');
define('APP_CODENAME', 'example_app'); // Must be valid part of filename (for log)
define('APP_NAMESPACE', 'ExampleApp'); // Must match app dirname
define('APP_PUBLIC_PATH', __DIR__.'/public/');

define('APP_BUILD', '1.0'); 
////// END WORKFRAME STANDARD CONFIG ///////

include(WORKFRAME_PATH . '/init.php');


////// START WORKFRAME STANDARD CONFIG (if workframe runs in a subdir) ///////
//define('WORKFRAME_PATH', __DIR__.'/../../WorkFrame');
//define('WORKFRAME_DEBUG', TRUE);
//define('WORKFRAME_ENVIRONMENT', 'DEV'); // DEV or LIVE
//
//define('WWW_ROOT_PATH', '/');
//define('WWW_PUBLIC_PATH', '/public');
//
//define('APP_PATH', __DIR__.'/../../ExampleApp');
//define('APP_NAME', 'Example app');
//define('APP_CODENAME', 'example_app'); // Must be valid part of filename (for log)
//define('APP_NAMESPACE', 'ExampleApp'); // Must match app dirname
//define('APP_PUBLIC_PATH', __DIR__.'/public/');
//
//define('APP_BUILD', '1.0'); 
//////// END WORKFRAME STANDARD CONFIG ///////
//
//include(WORKFRAME_PATH . '/init.php');