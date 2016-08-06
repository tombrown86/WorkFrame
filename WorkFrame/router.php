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
	$processor_class = "\\WorkFrame\\Request_handlers\\Async_processor";
	$request_handler = new $processor_class();
	$action = 'process';
	$routes = [];
} else {
	$class = "\\" . APP_NAMESPACE . "\\Request_handlers";
	// Last segment may or may not be the action. Either case, default to index if < 2 segments
	$last_segment = (count($routes) > 1) ? array_pop($routes) : 'index';

	foreach ($routes as $route) {
		$class .= "\\" . ucfirst($route);
	}

	if (count($routes) && class_exists($class)) {
		$action = $last_segment;
	} else { // no action specified, last segment part of routes
		$class .= "\\" . ucfirst($last_segment);
		$routes[] = $last_segment;
		$action = 'index'; // Action not given, default is index
	}

	if (class_exists($class) && method_exists($class, $action)) {
		$request_handler = new $class();
	} else {
		$class = "\\" . APP_NAMESPACE . "\\Request_handlers\\Error\\Error";
		$request_handler = new $class();
		$action = 'error_404';
		$routes = ['error'];
	}


}




handle_route:

// The request may be rewritten at this point(E.g. for 403)
try {
	// Prepare request handler for execution
	$request_handler->set_routes($routes);
	$request_handler->set_action($action);
	$_workframe->set_request_handler($request_handler);
	$_workframe->pre_action_hook();

	$request_handler->pre_action_hook();
	$request_handler->$action();
	$request_handler->post_action_hook();
	$_workframe->post_action_hook();
} catch (\WorkFrame\Exceptions\Request_handler_rewrite_exception $e) {
	$rewrite_request_handler_class = $e->get_rewrite_request_handler_class();
	$request_handler = new $rewrite_request_handler_class();
	$action = $e->get_rewrite_request_handler_action();
	$routes = $e->get_rewrite_request_routes();
	goto handle_route;
}
