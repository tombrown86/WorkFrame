<?php
namespace App\Request_handlers\Error;

class Error extends \App\Request_handlers\Site_base {
	function index() {
		// Shouldn't be here
	}
	function error($error_message) {
		$this->set_action('error');
		$this->add_view_var('error_message', $error_message);
		echo $this->render_partial_for_action('main.php');
	}
	function error_400() {
		$this->error('400 Bad request');
	}
	function error_403() {
		$this->error('403 Access forbidden. <a href="/login">Go to login page</a>');
	}
	function error_404() {
		$this->error('404 Not found');
	}
}