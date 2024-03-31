<?php

namespace WorkFrame\Processors;

class Validate_postcode extends Processor {

	static function server_side($field_name, $value) {
		if(!str_contains($value ?? '', ' ')) {
			$value = self::add_space_to_postcode($value ?? '');
		}
		$value = preg_replace('/\s+/', ' ', $value ?? '');
		if (!empty($value)
				&& ($value === "N/A"
						|| preg_match('/^([A-Z]{1,2}[0-9]{1,2}[A-Z]? [0-9][A-Z]{2}){1}$/i', $value))) {
			return ['new_value' => strtoupper($value), 'field_name' => $field_name,];
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
		var value = $("#' . $field_id . '").val().toUpperCase().replace(/\s+/g, " ");
  		if(value.indexOf(" ") === -1) {
    			var add_space_to_postcode = postcode => {var pattern = /([A-Z]{1,2}[0-9]{1,2})([0-9]{1}[A-Z]{2})/; var postcodeWithSpace = postcode.replace(pattern, "$1 $2"); return postcodeWithSpace; }
    			value = add_space_to_postcode(value);
		}
		var result = {field_name : "' . $field_name . '", new_value : value	};
		
		var regPostcode = /^([A-Z]{1,2}[0-9]{1,2}[A-Z]? [0-9][A-Z]{2}){1}$/i;
		if(value !== "N/A" && !regPostcode.test(value)) {
			result = {
				field_name : "' . $field_name . '",
				is_error : true,
				error_message : "Not a valid UK postcode"
			};
		}

		';
	}
	
	static function add_space_to_postcode($postcode) {
	    $pattern = '/^([A-Z]{1,2}[0-9]{1,2})([0-9]{1}[A-Z]{2})$i/';
	    $postcode_with_space = preg_replace($pattern, '$1 $2', trim($postcode));
	    return $postcode_with_space;
	}
}
