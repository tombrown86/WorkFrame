<?php

namespace ExampleApp\Request_handlers;

class Mail_link_action extends Site_base {

	function activate_user() {
		functions('cryptography');
		$hash = $_GET['h'];
		unset($_GET['h']);
		if(!check_hash_data($hash, conf('cryptography')['admin_secret'], $_GET)) {
			return $this->invalid_request();
		}
		$user = $this->SERVICE('Users_service')->activate_user($_GET['user_id'], TRUE);
		$this->add_view_var('user', $user);
		
		echo $this->route_render('main.php');
	}
	function confirm_email() {
		functions('cryptography');
		$hash = $_GET['h'];
		unset($_GET['h']);
		if(!check_hash_data($hash, conf('cryptography')['user_secret'], $_GET)) {
			return $this->invalid_request();
		}
		$user = $this->SERVICE('Users_service')->confirm_email($_GET['email'], TRUE);
		$this->add_view_var('user', $user);
		echo $this->route_render('main.php');
	}
	function invalid_request()  {
		log_message('BAD REQUEST', 'Invalid mail link action request... '.print_r($_GET,1));
		redirexit('/error/error_404');
		//$this->set_action('invalid_request');
		//echo $this->route_render('main.php');
	}
}
