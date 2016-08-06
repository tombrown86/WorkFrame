<?php
namespace ExampleApp\Request_handlers;

class Login extends \ExampleApp\Request_handlers\Site_base {
	function async_login() {
		header('Content-type: application/json');
		$this->SERVICE('Users_service', 'Users');
		$result = $this->Users->login($_REQUEST['username'], $_REQUEST['password']);
		if($result === TRUE) {
			if($this->Current_user->get_user_type() == 'user_type_1') {
				$target = '/welcome';
			}else if($this->Current_user->get_user_type() == 'user_type_2') {
				$target = '/dashboard';
			}
			echo json_encode((object)['target'=>$target]);
		}  else {
			echo json_encode((object)['error'=>$result]);
		}
		exit;
	}
	function async_reset_password() {
		header('Content-type: application/json');
		$this->SERVICE('Users_service', 'Users')->reset_password($_REQUEST['email']);
		echo 1;
		exit;
	}
	function logout() {
		$this->SERVICE('Users_service')->logout_current_user();
		redirexit('/');
	}
	function resend_confirm_email() {
		$this->SERVICE('Users_service', 'Users')->send_confirm_email_emails_for_email($_REQUEST['email']);
		echo $this->route_render('main.php');			
	}
}