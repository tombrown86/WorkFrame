<?php

namespace ExampleApp\Services;

class Users_service extends \WorkFrame\Service {

	function build_current_user() {
		$this->DOMAIN_OBJECT('Current_user', 'Current_user');
		$this->DATA_MAPPER('User_mapper', 'User_mapper');

		$user_id = $this->Session->read('user_id');

		if ($user_id) {
			$current_user_user = $this->DOMAIN_OBJECT('User');
			$current_user_user->set_id($user_id);

			if ($current_user_user = $this->User_mapper->find($current_user_user)) {
				$this->Current_user->set_user($current_user_user);
				$this->Current_user->set_is_authenticated(TRUE);
			} else {
				throw new \Exception('Unable to find user by primary key ' . $user_id);
			}
		}
	}

	function login($username, $password) {
		$this->Session->destroy();
		$this->Session->regenerate_session_id();

		$this->DOMAIN_OBJECT('Current_user', 'Current_user');
		$user_mapper = $this->DATA_MAPPER('User_mapper');
		$user = $this->DOMAIN_OBJECT('User');
		$user->set_username($username);

		$returned_user = $user_mapper->find($user);

		if (!$returned_user) {
			$user->set_username(NULL);
			$user->set_email($username);
			$returned_user = $user_mapper->find($user);
		}
		if (!$returned_user || !password_verify($password, $returned_user->get_password_hash())) {
			return 'invalid credentials';
		} else {
			$returned_user->set_last_login_date_time(date('Y-m-d H:i:s'));
			$user_mapper->save($returned_user);
			if (!$returned_user->get_activated() || !$returned_user->get_email_confirmed()) {
				return 'not active';
			} else {
				$this->Session->write('user_id', $returned_user->get_id());
				$this->build_current_user();
				return TRUE;
			}
		}
	}

	function logout_current_user() {
		$this->Session->destroy();
	}

	function reset_password($email) {
		$this->Session->destroy();
		$this->Session->regenerate_session_id();

		$this->DATA_MAPPER('User_mapper', 'User_mapper');
		$user_list = $this->DOMAIN_OBJECT('User_list');
		$user_list->set_email($email);
		$this->User_mapper->find_list($user_list);
		
		foreach($user_list->get_users() as $user) {
			$mailer = $this->LIBRARY('Mailer');
			$new_password = $this->_random_password();
			$user->set_password($new_password);
			$this->update_password_hash($user);
			$this->User_mapper->save($user);
			$mailer->mail_from_template($email, 'reset_password.php', ['new_password' => $new_password, 'username' => $user->get_username()]);
		}
		return TRUE;
	}

	function new_user() {
		/* @var $do \ExampleApp\Domain_objects\User */
		$user = $this->DOMAIN_OBJECT('User', 'New_user');
		$user->set_type('user');
		$user->set_sub_type('editor');
		
		return $user;
	}

	function create_user($data, $scenario = 'sign_up') {
		/* @var $do \ExampleApp\Domain_objects\User */
		$do = $this->new_user();
		$do->set_scenario($scenario);
		$do->from_assoc($data);

		if ($do->process()) {
			$this->update_password_hash($do);
			$this->update_coordinates($do);

			$do->set_created_date_time(date('Y-m-d H:i:s'));
			$do->set_random_identifier(md5(uniqid()));
			$dm = $this->DATA_MAPPER('User_mapper');
			$dm->save($do);
			
			$this->send_activation_email($do);
			$this->send_confirm_email_email($do);
		}

		return $do;
	}

	function update_user($user, $data, $scenario=NULL) {
		/* @var $user \ExampleApp\Domain_objects\User */
		$was_postcode = $user->get_postcode();
		$user->set_scenario($scenario);
		$user->from_assoc($data);

		if ($user->process()) {
			if($user->get_change_password()) {
				$this->update_password_hash($user);
				$user->set_change_password(FALSE);
			}
			if($was_postcode != $user->get_postcode()) {
				$this->update_coordinates($user);
			}

			$user->set_last_update_date_time(date('Y-m-d H:i:s'));
			$dm = $this->DATA_MAPPER('User_mapper');
			$dm->save($user);
			return TRUE;
		}

		return FALSE;
	}

	function get_profile($user_id) {
		$user_mapper = $this->DATA_MAPPER('User_mapper');
		$user = $this->DOMAIN_OBJECT('User');
		$user->set_id($user_id);
		$user_mapper->find($user);
		if($this->Current_user->can_see_user($user)) {
			return $user;
		}
		return FALSE;
	}

	function send_activation_email($user) {
		functions('cryptography');
		$data = ['user_id' => $user->get_id(), 'user_type' => $user->get_user_type(), 'user_sub_type' => $user->get_user_sub_type(), 'rand'=>uniqId()];
		$data['h'] = hash_data(conf('cryptography')['admin_secret'], $data);
		$activate_url = '/mail_link_action/activate_user?' . http_build_query($data);
		$email_vars = [
			'activate_url' => $activate_url,
			'user' => $user,
		];
		
		$mailer = new \WorkFrame\Libraries\Mailer();
		$mailer->mail_from_template([conf('app')['admin_email_address']], 'activation_request.php', $email_vars);
		
	}

	function send_confirm_email_email($user) {
		functions('cryptography');
		$data = ['email' => $user->get_email(), 'rand'=>uniqid()];
		$data['h'] = hash_data(conf('cryptography')['user_secret'], $data);
		$confirm_url = '/mail_link_action/confirm_email?' . http_build_query($data);
		$email_vars = [
			'confirm_url' => $confirm_url,
			'user' => $user,
		];
		
		$mailer = new \WorkFrame\Libraries\Mailer();
		$mailer->mail_from_template([$user->get_email()], 'confirm_email.php', $email_vars);
	}
	function send_confirm_email_emails_for_email($email) {
		$this->DATA_MAPPER('User_mapper', 'User_mapper');
		$user_list = $this->DOMAIN_OBJECT('User_list');
		$user_list->set_email($email);
		$this->User_mapper->find_list($user_list);
		
		foreach($user_list->get_users() as $user) {
			$this->SERVICE('Users_service', 'Users')->send_confirm_email_email($user);
			break; // actually only need to confirm one account for an email with multiple accounts
		}
	}
	function activate_user($user_id, $activated=TRUE){
		$user = $this->DOMAIN_OBJECT('User');
		$user_dm = $this->DATA_MAPPER('User_mapper');
		$user->set_id($user_id);
		$user_dm->find($user);
		$user->set_activated($activated);
		$user_dm->save($user);
		
		$email_vars = [
			'user' => $user,
		];
		$mailer = new \WorkFrame\Libraries\Mailer();
		$mailer->mail_from_template([$user->email], 'activated.php', $email_vars);
		return $user;
	}

	function confirm_email($email, $email_confirmed=TRUE){
		$this->DATA_MAPPER('User_mapper', 'User_mapper');
		$user_list = $this->DOMAIN_OBJECT('User_list');
		$user_dm = $this->DATA_MAPPER('User');
		$user_list->set_email($email);
		$this->User_mapper->find_list($user_list);
		
		foreach($user_list->get_users() as $user) {
			$user->set_email_confirmed($email_confirmed);
			$user_dm->save($user);
		}
		return $user;
	}

	function update_coordinates($user_do) {
//		list($lat, $lon) = \ExampleApp\Libraries\Postcode_anywhere::get_lat_lon_from_postcode($user_do->get_postcode());
		$lat = $lon = 0;
		$user_do->set_lat($lat);
		$user_do->set_lon($lon);
	}

	function update_password_hash($user_do) {
		$user_do->set_password_hash(password_hash($user_do->get_password(), PASSWORD_BCRYPT));
	}

	private function _random_password() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = [];
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

}
