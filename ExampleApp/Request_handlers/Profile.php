<?php

namespace ExampleApp\Request_handlers;

class Profile extends Site_base {

	function manage() {
		$user = $this->Current_user->get_user();
		$user->set_scenario('edit_profile');
		$this->add_view_var('user', $user);
		$this->set_action('manage');
		echo $this->route_render('main.php');
		exit;
	}

	function manage_submit() {
		if (isset($_POST['submitted'])) {
			if ($this->SERVICE('Users_service')->update_user($this->Current_user->get_user(), $_POST, 'edit_profile')) {
				$this->add_view_var('update_success', TRUE);
			}
		}
		return $this->manage();
	}

	function view() {
		$user_id = $_GET['id'];
		if ($user = $this->SERVICE('Users_service')->get_profile($user_id)) {
			$this->add_view_var('user', $user);
			echo $this->route_render('main.php');
		} else {
			redirexit('/error/error_403');
		}
	}

}
