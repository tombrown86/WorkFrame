<?php

namespace WorkFrame\Processors;

class Validate_password_regexp extends Processor {
	static function server_side($field_name, $value) {
		if (strlen($value) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#\_\.@£$!%*?&=\(\)\-\]\[])[A-Za-z\d#\_\.@£$!%*?&=\(\)\-\]\[]{8,}$/', $value)) {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Invalid password',
				'error_details' => "For security purposes, your password must be at least 8 characters and contain an uppercase letter, a lowercase letter, a number and a special character #_.@£$!%*?&)(=-][",
			];
		}

		return TRUE;
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return '
		var result = true;
		var value = $("#' . $field_id . '").val();
		if(!_wf_validate_password(value)) {
			result = {
				field_name : "' . $field_name . '",
				is_error : true,
				error_message : "Invalid password",
				error_details : "For security purposes, your password must be at least 8 characters and contain an uppercase letter, a lowercase letter, a number and a special character #_.@£$!%*?&)(=-][",
			};
		}
		';
	}

}
