<?php

namespace WorkFrame\Processors\Validators;

class Basic {

	static function validate_email($field_name, $value) {
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

	static function validate_email_client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::form_id($form_id, $field_name, $name_container_array);
		return '
		var result = true;
		var value = $("#' . $field_id . '").val();
		if(!_wf_validate_email(value)) {
			result = [
				field_name : "' . $field_name . '",
				is_error : true,
				error_message : "Invalid email address"
			];
		}
		';
	}

	static function validate_required($field_name, $value) {
		if (strlen($value)) {
			return TRUE;
		} else {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Field is required',
			];
		}
	}

	static function validate_required_client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::form_id($form_id, $field_name, $name_container_array);
		return '
                var result = true;
                var value = $("#' . $field_id . '").val();
                if(!value.length) {
                    result = [
                        field_name : "' . $field_name . '",
                        is_error : true,
                        error_message : "Field is required"
                    ];
                }
                
                ';
	}

	static function validate_passwords($fields, $values) {
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

	static function validate_passwords_client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::form_id($form_id, $field_name[0], $name_container_array);
		$field2_id = \WorkFrame\Html\Form_tools::form_id($form_id, $field_name[1], $name_container_array);
		return '
                var result = true;
                var value = $("#' . $field_id . '").val();
                var value2 = $("#' . $field2_id . '").val();
                if(value.length<8 || !_workframe_validate_password(value)) {
                    result = [
                        field_name : "' . $field_name[0] . '",
                        is_error : true,
                        error_message : "Invalid email address",
                        error_details : "Your password must be at least 8 characters long and contain at least one special character (which isn\'t a number or letter)"
                    ];
                } else if(value != value2) {
                    result = [
                        field_name : "' . $field_name[1] . '",
                        is_error : true,
                        error_message : "Your passwords do not match"
                    ];
                }
                
                ';
	}

}
