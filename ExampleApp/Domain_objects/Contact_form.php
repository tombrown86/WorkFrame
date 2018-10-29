<?php

namespace ExampleApp\Domain_objects;

class Contact_form extends \WorkFrame\Domain_object {
	use \WorkFrame\Magic_get_set_trait;
	use \WorkFrame\Processable_trait;

	private $name;
	private $email;
	private $telephone;
	private $address1;
	private $address2;
	private $address3;
	private $address4;
	private $subject;
	private $message;
	private $username;
	
	private $submitted = FALSE;
	protected $field_labels = [
		'name' => 'Name',
		'email' => 'Email',
		'telephone' => 'Telephone',
		'subject' => 'Subject',
		'message' => 'Message',
		'address1' => 'Address',
		'address2' => '',
		'address3' => '',
		'address4' => '',
		'username' => 'Username',
	];

	function __construct() {
		
	}

	function prepare_processors() {
		$this->processors = [
			[
				'fields' => ['name', 'email', 'subject', 'message'],
				'processor' => new \WorkFrame\Processors\Validate_required(),
			],
			[
				'fields' => ['email'],
				'processor' => new \WorkFrame\Processors\Validate_email(),
			],
		];
	}
 
	function mark_submitted() {
		$this->submitted = TRUE;
	}

	function is_submitted() {
		return $this->submitted;
	}

}
