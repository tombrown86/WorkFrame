<?php

// Use this to perform app wide things, store current user obj, etc

namespace ExampleApp;

class ExampleApp extends \WorkFrame\WorkFrame {
	/* Override any of these:
	  function pre_router_hook() {}
	  function post_router_hook() {}
	  function pre_cli_router_hook() {}
	  function post_cli_router_hook() {}
	  function pre_action_hook() {}
	  function post_action_hook() {}
	  function pre_cli_action_hook() {}
	  function post_cli_action_hook() {} */

	function __construct() {
		parent::__construct();
	}

	function pre_router_hook() {
		// Session
		$this->LIBRARY('Session', 'Session');
		$this->Session->start(FALSE);
		// Establish Users service and current user
		$this->SERVICE('Users_service', 'Users_service')->build_current_user();
	}

	function pre_action_hook() {
		// Check user is authorised for route+action
		$user_type = $this->Current_user->get_user_type();
		$request_routes = $this->request_handler->get_routes();
		$restrictions = conf('route_restrictions');
		foreach ($request_routes as $route_segment) {
			if (!isset($restrictions[$route_segment])) {
				// Nothin defined for this route, allow by default
				break;
			} else if (isset($restrictions[$route_segment]['user types'])) {
				if (!in_array($user_type, $restrictions[$route_segment]['user types'])) {
					throw new \WorkFrame\Exceptions\Request_handler_rewrite_exception('\\ExampleApp\\Request_handlers\\Error\\Error', 'error_403', ['Error']);
				} else {
					// user type in array of allowed user types
					break;
				}
			}
			// Move on to next segment 
			$restrictions = $restrictions[$route_segment];
		}

		if ($user_type == 'practice' && isset($request_routes['practice'])) {
			// Check membership not expired
		}
	}

	//function exception_handler($ex) {
	//}
}
