<?php
namespace App\Request_handlers;

class Index extends \App\Request_handlers\Site_base {
	function index() {
		echo $this->render_partial_for_action('main.php');
	}
	function author() {
		echo $this->render_partial_for_action('main.php');
	}
}
