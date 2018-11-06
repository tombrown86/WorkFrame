<?php

// Convenience function to get main workframe obj (constructed below)
function _workframe() {
	return $GLOBALS['_workframe'];
}



if(WORKFRAME_DEBUG) {
	error_reporting(E_ALL);
	ini_set('display_errors','On');
} else {
//	error_reporting(E_ALL);
//	ini_set('display_errors','Off');
}


include(WORKFRAME_PATH . '/namespace_autoload.php');
include(WORKFRAME_PATH . '/logger.php');
include(WORKFRAME_PATH . '/procedural_loader.php');
include(WORKFRAME_PATH . '/WorkFrame.php');







// instantiate global WORKFRAME class (see if exists in app first)
$wfapp_class = '\\'.APP_NAMESPACE.'\\'.APP_NAMESPACE;
if(!class_exists($wfapp_class)) {
	$wfapp_class = '\\WorkFrame\\WorkFrame';
}
$_workframe = new $wfapp_class();
if(!$_workframe instanceof \WorkFrame\WorkFrame) {
	die('Main app class ('.$wfapp_class.') must extent \WorkFrame\WorkFrame');
}




// Route request (hand over to application code)

try {
	if(defined('WORKFRAME_FORCE_INTERFACE')) {
		$interface = WORKFRAME_FORCE_INTERFACE == "cli" ? 'cli' : 'web';
	} else {
		$interface = php_sapi_name() == "cli" ? 'cli' : 'web';
	}
	if ($interface == "cli") {
                // In cli-mode
		$_workframe->pre_cli_router_hook();
		include(WORKFRAME_PATH . '/cli_router.php');
		$_workframe->post_cli_router_hook();
	} else {
		// web request
		$_workframe->pre_router_hook();
		include(WORKFRAME_PATH . '/router.php');
		$_workframe->post_router_hook();
	}
} catch (\WorkFrame\Exceptions\WorkFrame_exception $e) {
	$_workframe->exception_handler($e);
}
