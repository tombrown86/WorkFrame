<?php

namespace WorkFrame;

class Request_handler {

	use WorkFrame_component_trait;
	use Renderer_trait;

	protected $routes;
	protected $action;

	function __construct() {
		functions('http');
	}

	function pre_action_hook() {
		// Would usually only get utilised if template rendered
		$workframe_scripts_dir = WORKFRAME_PATH . '/js';
		$this->add_scripts([$workframe_scripts_dir . '/jquery-3.0.0.js', $workframe_scripts_dir . '/_workframe_functions.js', $workframe_scripts_dir . '/_workframe_processors.js'], TRUE, '_workframe_standard');
	}

	function post_action_hook() {
		
	}

	function set_routes($routes) {
		$this->routes = $routes;
	}

	function get_routes() {
		return $this->routes;
	}

	function set_action($action) {
		$this->action = $action;
	}

	function get_action() {
		return $this->action;
	}

	function route_render($template = null) {
		$partial = '';
		foreach ($this->routes as $route) {
			$partial .= $route . '/';
		}
		$partial .= $this->action . '.php';
		$partial = strtolower($partial);

		return $this->render($partial, $template);
	}
}