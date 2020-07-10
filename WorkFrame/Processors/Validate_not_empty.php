<?php

namespace WorkFrame\Processors;

class Validate_not_empty extends Processor {

	static function server_side($field_name, $value) {
		if (!empty($value)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Must not be empty',
			];
		}
	}

	static function client_side($form_id, $field_name) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name);
		return '
			var result = true;
			var value = $("#' . $field_id . '").val();
			if(value = "" || value == 0) {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Must not be empty"
				};
			}
		';
	}


}
