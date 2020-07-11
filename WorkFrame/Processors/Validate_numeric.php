<?php

namespace WorkFrame\Processors;

class Validate_not_empty extends Processor {

	static function server_side($field_name, $value) {
		if (!is_numeric($value)) {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Must be numeric',
			];
		}
		return TRUE;
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return	'
			var result = true;
			var value = $("#' . $field_id . '").val();
			if(!$.isNumeric(value)) {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Must be numveric"
				};
			}
		';
	}


}
