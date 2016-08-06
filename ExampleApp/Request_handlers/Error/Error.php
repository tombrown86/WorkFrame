<?php
namespace ExampleApp\Request_handlers\Error;

class Error extends \ExampleApp\Request_handlers\Site_base {
	function index() {
		// Shouldn't ever get here
	}
	function error($error_message) {
		$this->set_action('error');
		$this->add_view_var('error_message', $error_message);
		echo $this->route_render('main.php');
	}
	function error_400() {
		$this->error('400 Bad request');
	}
	function error_403() {
		$this->error('403 Access forbidden, you may have been logged out due to inactivity');
	}
	function error_404() {
		$this->error('404 Not found');
	}
}
