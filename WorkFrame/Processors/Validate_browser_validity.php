<?php

namespace WorkFrame\Processors;

/**
 * This checks native browser validation (E.g. pattern attribute set on field)
 */
class Validate_browser_validity extends Processor {

	static function server_side($field_name, $value) {
		return TRUE;
	}

	static function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return	'
			var result = true;
			var field = document.getElementById("' . $field_id . '");
			if (typeof field.checkValidity == "function"
					&& !field.checkValidity()) {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : field.validationMessage
				};
			}
		';
	}
}
