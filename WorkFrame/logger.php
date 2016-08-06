<?php

define('WF_LOG_LEVEL_ERROR', 'ERROR');
define('WF_LOG_LEVEL_WARNING', 'WARNING');
define('WF_LOG_LEVEL_INFO', 'INFO');

function log_message($level, $message, $debug_silent=FALSE) {
	$level = strtoupper($level);

	if(WORKFRAME_DEBUG 
		&& !$debug_silent
		&& in_array($level, ['BAD REQUEST', 'ERROR', 'WARNING'])) {
		echo '<hr/><div style="color:red"><strong>'.$level.'</strong> - '.htmlspecialchars($message).'</div><hr/>';
	}
	
	file_put_contents(APP_PATH . '/logs/'.APP_CODENAME.'__'.date('Y-m-d').'__wireframe_log', date('Y-m-d H:i:s'). " -- $level -- ".$message . "\n", FILE_APPEND);
}
