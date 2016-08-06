<?php
namespace WorkFrame\Exceptions;

class WorkFrame_exception extends \Exception {
	private $debug_message = '';
	function __construct($message='', $debug_message=null) {
		$this->debug_message = $debug_message;
		parent::__construct($message);
//		if(WORKFRAME_DEBUG) {
//			echo '<br/><hr/><strong>WF Exception: '.htmlspecialchars($message).'</strong><br><strong>Debug</strong>: '.$debug_message.'<hr/>';
//		}
	}
	function get_debug_message() {
		return $this->debug_message;
	}
}
