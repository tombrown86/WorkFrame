<?php

namespace ExampleApp\Request_handlers;

class Index extends Site_base {

	function index() {
		echo $this->route_render('main.php');
	}

	function about() {
		echo $this->route_render('main.php');
	}

	function learn_more() {
		echo $this->route_render('main.php');
	}

	function contact() {
		$this->add_view_var('contact_form', $this->SERVICE('Contact_service')->new_form());
		echo $this->route_render('main.php');
	}

	function contact_submit() {
		$contact_form = $this->SERVICE('Contact_service')->submit($_POST);

		if (!$contact_form->is_submitted()) {
			$this->set_action('contact');
		}
		$this->add_view_var('contact_form', $contact_form);
		echo $this->route_render('main.php');
	}

	function sign_up() {
		$user = $this->SERVICE('Users_service')->new_user();
		$this->add_view_var('user', $user);
		$this->_render_sign_up($user);
	}

	function sign_up_submit() {	
		$user = $this->SERVICE('Users_service')->create_user($_POST, 'sign_up');

		if (!$user->get_user_id()) {
			return $this->_render_sign_up($user);
		}
		$this->add_view_var('user', $user);
		echo $this->route_render('main.php');
	}

	private function _render_sign_up($user) {
		$user->set_scenario('sign_up');
		$this->add_view_var('user', $user);
		$this->set_action('sign_up');
		echo $this->route_render('main.php');
		exit;
	}

	function account_not_accessible() {
		echo $this->route_render('main.php');		
	}
}
