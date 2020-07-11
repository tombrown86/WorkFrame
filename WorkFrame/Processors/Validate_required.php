<?php

namespace WorkFrame\Processors;

class Validate_required extends Processor {

	static function server_side($field_name, $value, $args, $processable) {
		if (!empty($value)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Field is required',
			];
		}
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return '
		var result = true;
		var value = $("#' . $field_id . '").val();
		if(!value.length || ($("#' . $field_id . '").attr("type")=="checkbox" && !$("#' . $field_id . '").is(":checked"))) {
			result = {
				field_name : "' . $field_name . '",
				is_error : true,
				error_message : "Field is required"
			};
		}

		';
	}


}
