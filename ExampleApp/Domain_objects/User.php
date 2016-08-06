<?php

namespace ExampleApp\Domain_objects;

class User extends \WorkFrame\Domain_object {

	use \WorkFrame\Processable_trait;

	private $id;
	private $random_identifier;
	private $username;
	private $email;
	private $first_name;
	private $last_name;
	private $user_type;
	private $user_sub_type;
	
	private $change_password = FALSE;
	private $password;
	private $password2;
	private $password_hash;
	private $activated;
	private $email_confirmed;
	private $address1;
	private $address2;
	private $address3;
	private $address4;
	private $postcode;
	private $lon;
	private $lat;
	private $phone;
	private $phone2;
	private $fax;
	private $created_date_time;
	private $last_login_date_time;
	private $last_update_date_time;
	private $profile_image;
	private $message;
	private $how_hear_about_us;
	private $how_hear_about_us_other;

	
	protected $field_labels = [
		'random_identifier' => 'Random identifier',
		'first_name' => 'First name',
		'last_name' => 'Last name',
		'username' => 'Username',
		'email' => 'Email',
		'address1' => 'Address',
		'address2' => 'Address 2',
		'address3' => 'Address 3',
		'address4' => 'Address 4',
		'postcode' => 'Postcode',
		'change_password' => 'Change my password',
		'password' => 'Password',
		'password2' => 'Confirm password',
		'activated' => 'Activated',
		'how_hear_about_us' => 'How did you hear about us?',
		'how_hear_about_us_other' => 'Please let us know how you heard about us',
		'email_confirmed' => 'User confirmed email address',
		'lat' => 'Latitude',
		'lon' => 'Longitude',
		'phone' => 'Phone number',
		'phone2' => 'Alternate number',
		'fax' => 'Fax',
		'last_login_date_time' => 'Last seen',
		'last_update_date_time' => 'Last updated',
		'profile_image' => 'Profile image',
		'message' => 'Profile message',
	];
	
	
	function __construct() {
		
	}

	protected $scenarios = [
		'sign_up' => ['message', 'username', 'email', 'first_name', 'last_name','address1', 'address2', 'address3', 'address4', 'postcode', 'password', 'password2', 'how_hear_about_us', 'how_hear_about_us_other', 'phone', 'phone2', 'fax', 'profile_image'],
		
		'edit_profile' => ['change_password', 'email', 'first_name', 'last_name','address1', 'address2', 'address3', 'address4', 'postcode', 'password', 'password2', 'how_hear_about_us', 'how_hear_about_us_other', 'phone', 'phone2', 'fax', 'profile_image']
	];

	function prepare_processors() {
		$validate_length_allow_empty = new \WorkFrame\Processors\Validate_length(10);
		$validate_length_allow_empty->set_allow_empty(TRUE);
		
		$validate_password = new \WorkFrame\Processors\Validate_passwords();		
		if($this->scenario == 'edit_profile') {
			$validate_password->set_condition_field_names(['change_password']);
		}
		
		$this->processors = [
			[
				'fields' => ['first_name', 'last_name', 'username', 'email', 'address1', 'address2', 'postcode', 'phone'],
				'processor' => new \WorkFrame\Processors\Validate_required(),
				'scenarios' => ['sign_up', 'edit_profile'],
			],
			[
				'fields' => ['how_hear_about_us','password', 'password2'],
				'processor' => new \WorkFrame\Processors\Validate_required(),
				'scenarios' => ['sign_up'],
			],
			[
				'fields' => [['password', 'password2']],
				'processor' => $validate_password,
				'scenarios' => ['sign_up', 'edit_profile'],
			],
			[
				'fields' => ['postcode'],
				'processor' => new \WorkFrame\Processors\Validate_postcode(),
				'scenarios' => ['sign_up', 'edit_profile'],
			],
			[
				'fields' => ['email'],
				'processor' => new \WorkFrame\Processors\Validate_email(),
				'scenarios' => ['sign_up', 'edit_profile'],
			],
			[
				'fields' => ['username'],
				'processor' => new \ExampleApp\Processors\Validate_username(),
				'scenarios' => ['sign_up'],
			],
			[
				'fields' => ['username'],
				'processor' => new \ExampleApp\Processors\Validate_unique_user_field(),
				'scenarios' => ['sign_up'],
			],
			[
				'fields' => ['phone'],
				'processor' => new \WorkFrame\Processors\Validate_length(10),
				'scenarios' => ['sign_up', 'edit_profile'],
			],
			[
				'fields' => ['phone2', 'fax'],
				'processor' => $validate_length_allow_empty,
				'scenarios' => ['sign_up', 'edit_profile'],
			],
		];
	}

	function get_change_password() {
		return $this->change_password;
	}

	function set_change_password($change_password) {
		$this->change_password = $change_password;
	}

	function get_message() {
		return $this->message;
	}

	function set_message($message) {
		$this->message = $message;
	}

	function get_first_name() {
		return $this->first_name;
	}

	function set_first_name($first_name) {
		$this->first_name = $first_name;
	}
	function get_last_name() {
		return $this->last_name;
	}

	function set_last_name($last_name) {
		$this->last_name = $last_name;
	}

	function get_activated() {
		return $this->activated;
	}

	function set_activated($activated) {
		$this->activated = $activated;
	}

	function get_address1() {
		return $this->address1;
	}

	function set_address1($address1) {
		$this->address1 = $address1;
	}

	function get_address2() {
		return $this->address2;
	}

	function set_address2($address2) {
		$this->address2 = $address2;
	}

	function get_address3() {
		return $this->address3;
	}

	function set_address3($address3) {
		$this->address3 = $address3;
	}

	function get_address4() {
		return $this->address4;
	}

	function set_address4($address4) {
		$this->address4 = $address4;
	}

	function get_created_date_time() {
		return $this->created_date_time;
	}

	function set_created_date_time($created_date_time) {
		$this->created_date_time = $created_date_time;
	}


	function get_email() {
		return $this->email;
	}

	function set_email($email) {
		$this->email = $email;
	}

	function get_email_confirmed() {
		return $this->email_confirmed;
	}

	function set_email_confirmed($email_confirmed) {
		$this->email_confirmed = $email_confirmed;
	}

	function get_fax() {
		return $this->fax;
	}

	function set_fax($fax) {
		$this->fax = $fax;
	}

	function get_id() {
		return $this->id;
	}

	function set_id($id) {
		$this->id = $id;
	}

	function get_last_login_date_time() {
		return $this->last_login_date_time;
	}

	function set_last_login_date_time($last_login_date_time) {
		$this->last_login_date_time = $last_login_date_time;
	}

	function get_last_update_date_time() {
		return $this->last_update_date_time;
	}

	function set_last_update_date_time($last_update_date_time) {
		$this->last_update_date_time = $last_update_date_time;
	}

	function get_lat() {
		return $this->lat;
	}

	function set_lat($lat) {
		$this->lat = $lat;
	}

	function get_lon() {
		return $this->lon;
	}

	function set_lon($lon) {
		$this->lon = $lon;
	}

	function get_password_hash() {
		return $this->password_hash;
	}

	function set_password($password) {
		$this->password = $password;
	}

	function set_password2($password2) {
		$this->password2 = $password2;
	}
	function get_password2() {
		return $this->password2;
	}

	function set_password_hash($password_hash) {
		$this->password_hash = $password_hash;
	}

	function get_phone() {
		return $this->phone;
	}

	function set_phone($phone) {
		$this->phone = $phone;
	}

	function get_phone2() {
		return $this->phone2;
	}

	function set_phone2($phone2) {
		$this->phone2 = $phone2;
	}

	function get_postcode() {
		return $this->postcode;
	}

	function set_postcode($postcode) {
		$this->postcode = $postcode;
	}

	function get_profile_image() {
		return $this->profile_image;
	}

	function set_profile_image($profile_image) {
		$this->profile_image = $profile_image;
	}

	function get_random_identifier() {
		return $this->random_identifier;
	}

	function set_random_identifier($random_identifier) {
		$this->random_identifier = $random_identifier;
	}

	function get_user_sub_type() {
		return $this->user_sub_type;
	}

	function set_user_sub_type($user_sub_type) {
		$this->user_sub_type = $user_sub_type;
	}

	function get_user_type() {
		return $this->user_type;
	}

	function set_user_type($user_type) {
		$this->user_type = $user_type;
	}

	function get_username() {
		return $this->username;
	}

	function set_username($username) {
		$this->username = $username;
	}

	function get_how_hear_about_us() {
		return $this->how_hear_about_us;
	}

	function set_how_hear_about_us($how_hear_about_us) {
		$this->how_hear_about_us = $how_hear_about_us;
	}

	function set_how_hear_about_us_other($how_hear_about_us_other) {
		$this->how_hear_about_us_other = $how_hear_about_us_other;
	}

	function get_how_hear_about_us_other() {
		return $this->how_hear_about_us_other;
	}

	function get_password() {
		return $this->password;
	}
}
