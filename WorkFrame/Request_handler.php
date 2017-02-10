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
		
		$scripts = [];
		
		$use_jquery = conf('javascript')['use_jquery'];
		$use_workframe_functions = conf('javascript')['use_workframe_functions'];
		$use_workframe_processors = conf('javascript')['use_workframe_processors'];
		
		if($use_workframe_functions) {
			$scripts[] = $workframe_scripts_dir . '/_workframe_functions.js';
		}
		if($use_workframe_processors) {
			$scripts[] = $workframe_scripts_dir . '/_workframe_processors.js';
		}
		if($use_jquery) {
			array_unshift($scripts, $workframe_scripts_dir . '/jquery-'.conf('javascript')['jquery_version'].'.js');
		}
		if($scripts) {
			$this->add_scripts($scripts, TRUE, '_workframe_standard');
		}
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
	
	function route_render_action($action, $template = null) {
		$partial = '';
		foreach ($this->routes as $route) {
			$partial .= $route . '/';
		}
		$partial .= $action . '.php';
		$partial = strtolower($partial);

		return $this->render($partial, $template);
	}
}