<?php

namespace WorkFrame\Processors;

class Validate_passwords extends Processor {
	static function server_side($fields, $values) {
		if (strlen($values[$fields[0]]) < 8 || !preg_match('/[^\w]/i', $values[$fields[0]])) {
			return [
				'field_name' => $fields[0],
				'is_error' => TRUE,
				'error_message' => 'Invalid password',
				'error_details' => 'Your password must be at least 8 characters long and contain at least one special character (which isn\'t a number or letter)',
			];
		}

		if ($values[$fields[0]] != $values[$fields[1]]) {
			return [
				'field_name' => $fields[1],
				'is_error' => TRUE,
				'error_message' => 'Your passwords do not match',
			];
		}
		return TRUE;
	}

	static function client_side($form_id, $field_name) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name[0]);
		$field2_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name[1]);
		return '
		var result = true;
		var value = $("#' . $field_id . '").val();
		var value2 = $("#' . $field2_id . '").val();
		if(!_wf_validate_password(value)) {
			result = {
				field_name : "' . $field_name[0] . '",
				is_error : true,
				error_message : "Invalid password",
				error_details : "Your password must be at least 8 characters long and contain at least one special character (which isn\'t a number or letter)"
			};
		} else if(value != value2) {
			result = {
				field_name : "' . $field_name[1] . '",
				is_error : true,
				error_message : "Your passwords do not match"
			};
		}

		';
	}

}
