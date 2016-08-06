<?php
namespace WorkFrame;


/**
 * Extend this in you app
 * Use it to perform app wide things, store app wide things (current user obj, etc)
 */

class WorkFrame {
	use WorkFrame_component_trait;

	public $LOADED = [];
	
	private $request_handler;

	function __construct(){}

	function set_request_handler($request_handler) {
		$this->request_handler = $request_handler;
	}
	
	
	function pre_router_hook() {}
	function post_router_hook() {}
	function pre_cli_router_hook() {}
	function post_cli_router_hook() {}
	function pre_action_hook() {}
	function post_action_hook() {}
	function pre_cli_action_hook() {}
	function post_cli_action_hook() {}

	/**
	 * Last resort WorkFrame_exceptions catcher
	 * 
	 * @param \WorkFrame\Exceptions\Workframe_exception exception
	 */
	function exception_handler($ex) {
		$message = get_class($ex).' - Caught by `last resort` WorkFrame_exception handler in '.__CLASS__."\n\n".'. Exception message: '.$ex->getMessage();
		log_message('ERROR', $message);
		
		echo '<br/><br/><h2>Sorry - an unexpected error has occurred in this application.</h2>';
		
		if(WORKFRAME_DEBUG) {
			echo '<strong>DEBUG INFO:</strong> '.$ex->get_debug_message();
			echo '<pre>';
			print_r(debug_backtrace());
			echo '</pre>';
		}
		die;
	}
}
