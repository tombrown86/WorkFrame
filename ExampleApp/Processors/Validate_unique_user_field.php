<?php

namespace ExampleApp\Processors;

class Validate_unique_user_field extends \WorkFrame\Processors\Async_processor {

	function __construct() {
		
	}

	static function server_side($field_name, $value) {
		$dm = static::DATA_MAPPER('User_mapper');
		$do = static::DOMAIN_OBJECT('User');
		if (!in_array($field_name, ['email', 'username'])) {
			throw new Exception('Can only validate uniqueness of email and username');
		}
		$set_func = 'set_'.$field_name;

		$do->$set_func($value);

		if (FALSE === $dm->find($do)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Sorry, this ' . $field_name . ' already exists in our system.',
				'error_detail' => 'If you already have an account, you can login using the login form at the top-right of the page.',
			];
		}
	}
}
