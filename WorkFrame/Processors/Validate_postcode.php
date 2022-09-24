<?php

namespace WorkFrame\Processors;

class Validate_postcode extends Processor {

	static function server_side($field_name, $value) {
		if (isset($value)
				&& ($value === "N/A"
						|| preg_match('/^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/', $value))) {
			return ['new_value' => strtoupper($value),'field_name' => $field_name,];
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Not a valid UK postcode',
			];
		}
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return '
		var value = $("#' . $field_id . '").val().toUpperCase();
		var result = {field_name : "' . $field_name . '", new_value : value	};
		
		var regPostcode = /^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/;
		if(value !== "N/A" && !regPostcode.test(value)) {
			result = {
				field_name : "' . $field_name . '",
				is_error : true,
				error_message : "Not a valid UK postcode"
			};
		}

		';
	}


}
