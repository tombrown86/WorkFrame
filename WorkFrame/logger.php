<?php

define('WF_LOG_LEVEL_ERROR', 'WF_ERROR');
define('WF_LOG_LEVEL_WARNING', 'WF_WARNING');
define('WF_LOG_LEVEL_INFO', 'WF_INFO');
define('APP_LOG_LEVEL_ERROR', 'APP_ERROR');
define('APP_LOG_LEVEL_WARNING', 'APP_WARNING');
define('APP_LOG_LEVEL_INFO', 'APP_INFO');

function log_message($level, $message, $debug_silent=FALSE) {
	$level = strtoupper($level);

	if(WORKFRAME_DEBUG 
		&& !$debug_silent
		&& !in_array($level, [APP_LOG_LEVEL_INFO, WF_LOG_LEVEL_INFO])) {
		echo '<hr/><div style="color:red"><strong>'.$level.'</strong> - '.htmlspecialchars($message).'</div><hr/>';
	}
	
	$app_log_path = rtrim(APP_LOG_PATH, '/\\') . DIRECTORY_SEPARATOR;
	file_put_contents($app_log_path.APP_CODENAME.'__'.date('Y-m-d').'__WorkFrame_log', date('Y-m-d H:i:s'). " -- $level -- ".$message . "\n", FILE_APPEND);
}
