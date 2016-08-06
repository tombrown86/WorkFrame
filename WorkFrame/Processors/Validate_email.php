<?php

namespace WorkFrame\Processors;

class Validate_email extends Processor {

	static function server_side($field_name, $value) {
		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Invalid email address',
			];
		}
	}

	static function client_side($form_id, $field_name) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name);
		return '
			var result = true;
			var value = $("#' . $field_id . '").val();
			if(!_wf_validate_email(value)) {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Invalid email address"
				};
			}
		';
	}


}
