<?php

$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));



if (strstr($uri, '?')) {
	$uri = substr($uri, 0, strpos($uri, '?'));
}
$base_url = '/' . trim($uri, '/');
if (!preg_match('/[\/\w_]+/i', $base_url)) {
	$base_url = '/error/error/error_400';
}

$base_url = trim($base_url, '/');



// Apply any hard set rewrites
foreach (@conf('router')['route_rewrites'] as $path => $default_route) {
	if ($base_url == trim($path, '/')) {
		$base_url = $default_route;
	}
}

$routes = array_filter(explode('/', $base_url));

// Handle any Async_processor calls natively
if(isset($routes[0]) && $routes[0] === '_async_processor') {
	$request_handler_class_path = "\\WorkFrame\\Request_handlers\\Async_processor";
	$action = 'process';
	$routes = [];
} else {
	$class_path = "\\" . APP_NAMESPACE . "\\Request_handlers";
	// Last segment may or may not be the action. Either case, default to index if < 2 segments
	$last_segment = (count($routes) > 1) ? array_pop($routes) : 'index';

	foreach ($routes as $route) {
		$class_path .= "\\" . ucfirst($route);
	}

	if (count($routes) && class_exists($class_path)) {
		$action = $last_segment;
	} else { // no action specified, last segment part of routes
		$class_path .= "\\" . ucfirst($last_segment);
		$routes[] = $last_segment;
		$action = 'index'; // Action not given, default is index
	}

	if (class_exists($class_path) && method_exists($class_path, $action)) {
		$request_handler_class_path = $class_path;
	} else {
		$request_handler_class_path = "\\" . APP_NAMESPACE . "\\Request_handlers\\Error\\Error";
		$action = 'error_404';
		$routes = ['error'];
		header("HTTP/1.0 404 Not Found");
	}
}

handle_route:

// The request may be rewritten at this point (E.g. for 403)
try {
	$request_handler = new $request_handler_class_path();
	// Prepare request handler for execution
	if(!$request_handler instanceof WorkFrame\Request_handler) {
		throw new WorkFrame\Exceptions\Class_is_not_a_request_handler_exception;
	}
	$request_handler->set_routes($routes);
	$request_handler->set_action($action);
	$_workframe->set_request_handler($request_handler);
	$_workframe->pre_action_hook();
	$request_handler->pre_action_hook();
	$request_handler->$action();
	$request_handler->post_action_hook();
	$_workframe->post_action_hook();
} catch (\WorkFrame\Exceptions\Request_handler_rewrite_exception $e) {
	$request_handler_class_path = $e->get_rewrite_request_handler_class();
	$action = $e->get_rewrite_request_handler_action();
	$routes = $e->get_rewrite_request_routes();
	goto handle_route;
}
