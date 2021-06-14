<?php

namespace WorkFrame\Processors;

class Validate_passwords_match extends Processor {
	static function server_side($fields, $values) {
		if ($values[$fields[0]] != $values[$fields[1]]) {
			return [
				'field_name' => $fields[1],
				'is_error' => TRUE,
				'error_message' => 'Your passwords do not match',
			];
		}
		return TRUE;
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name[0], $name_container_array);
		$field2_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name[1], $name_container_array);
		return '
		var result = true;
		var value = $("#' . $field_id . '").val();
		var value2 = $("#' . $field2_id . '").val();
		if(value != value2) {
			result = {
				field_name : "' . $field_name[1] . '",
				is_error : true,
				error_message : "Your passwords do not match"
			};
		}

		';
	}

}
