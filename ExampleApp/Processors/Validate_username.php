<?php

namespace ExampleApp\Processors;

class Validate_username extends \WorkFrame\Processors\Processor {

	function __construct() {
		
	}

	static function server_side($field_name, $value) {
		if (preg_match('/[\w_]{3,20}/i', $value)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Not a valid username.',
				'error_details' => 'Please use a minimum of three letters, numbers or underscores only.',
			];
		}
	}

	function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return	'
			var result = true;
			var value = $("#' . $field_id . '").val();
			if(!/[\w_]{3,20}/i.test(value)) {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Not a valid username.",
					error_details : "Please use a minimum of three letters, numbers or underscores only."
				};
			}
		';
	}

}
