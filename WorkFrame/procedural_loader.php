<?php

function conf($conf_name) {
	if(file_exists(APP_PATH . '/config/' . $conf_name . '.php')) {
		return include(APP_PATH . '/config/' . $conf_name . '.php');
	} else {
		return include(WORKFRAME_PATH . '/config/' . $conf_name . '.php');
	}
}

$WF_INCLUDED_FUNCTIONS = [];
function functions($functions_file_name) {
	global $WF_INCLUDED_FUNCTIONS;
	if(!in_array($functions_file_name, $WF_INCLUDED_FUNCTIONS)) {
		if(file_exists(APP_PATH . '/functions/' . $functions_file_name . '.php')) {
			include(APP_PATH . '/functions/' . $functions_file_name . '.php');
		} else {
			include(WORKFRAME_PATH . '/functions/' . $functions_file_name . '.php');
		}
		$WF_INCLUDED_FUNCTIONS[] = $functions_file_name;
	}
}

function constants($constants_file_name) {
	if(file_exists(APP_PATH . '/constants/' . $constants_file_name . '.php')) {
		include(APP_PATH . '/constants/' . $constants_file_name . '.php');
	} else {
		include(WORKFRAME_PATH . '/constants/' . $constants_file_name . '.php');
	}
}
