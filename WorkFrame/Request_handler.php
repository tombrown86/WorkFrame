<?php

namespace WorkFrame;

class Request_handler {

	use WorkFrame_component_trait;
	use Renderer_trait;

	private $_use_xss_fitler;

	protected $routes;
	protected $action;

	function __construct() {
		functions('http');
	}

	function pre_action_hook() {
		$security_conf = conf('security');
		$this->_use_xss_fitler = isset($security_conf['use_workframe_security_library'], $security_conf['xss_filter'])
									&& $security_conf['use_workframe_security_library']
									&& $security_conf['xss_filter'];

		// Would usually only get utilised if template rendered
		$workframe_scripts_dir = WORKFRAME_PATH . '/js';
		
		$scripts = [];
		
		$javascript_conf = conf('javascript');
		$use_jquery = isset($javascript_conf['use_jquery']) && $javascript_conf['use_jquery'];
		$use_workframe_functions = isset($javascript_conf['use_workframe_functions']) && $javascript_conf['use_workframe_functions'];
		$use_workframe_processors = isset($javascript_conf['use_workframe_processors']) && $javascript_conf['use_workframe_processors'];
		
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

	/**
	 * Get GET input data.
	 * Returns entire GET array or specific key value if key is given.
	 * If security and XSS filter is enabled, the processed version is 
	 * returned unless get_original param is true.
	 * 
	 * @param string key
	 * @param boolean get_original (uncleaned)
	 * @return mixed array of value or NULL if not set
	 */
	function GET($key=NULL, $get_original=FALSE) {
		return $this->_get_processed_global('_GET', $key, $get_original);
	}
	/**
	 * Get POST input data.
	 * Returns entire POST array or specific key value if key is given.
	 * If security and XSS filter is enabled, the processed version is 
	 * returned unless get_original param is true.
	 * 
	 * @param string key
	 * @param boolean get_original (uncleaned)
	 * @return mixed array of value or NULL if not set
	 */
	function POST($key=NULL, $get_original=FALSE) {
		return $this->_get_processed_global('_POST', $key, $get_original);
	}

	/**
	 * Get REQUEST input data.
	 * Returns entire REQUEST array or specific key value if key is given.
	 * If security and XSS filter is enabled, the processed version is 
	 * returned unless get_original param is true.
	 * 
	 * @param string key
	 * @param boolean get_original (uncleaned)
	 * @return mixed array of value or NULL if not set
	 */
	function REQUEST($key=NULL, $get_original=FALSE) {
		return $this->_get_processed_global('_REQUEST', $key, $get_original);
	}

	private function _get_processed_global($global_arr_name, $key=NULL, $get_original=FALSE) {
		if($this->_use_xss_fitler) {
			if($get_original) {
				$global_arr_name = '_ORIGINAL'.$global_arr_name;
			} else {
				$global_arr_name = '_CLEAN'.$global_arr_name;
			}
		}

		if(isset($key)) {
			return isset($GLOBALS[$global_arr_name][$key]) ? $GLOBALS[$global_arr_name][$key] : NULL;
		} else {
			return isset($GLOBALS[$global_arr_name]) ? $GLOBALS[$global_arr_name] : NULL;
		}
	}
}