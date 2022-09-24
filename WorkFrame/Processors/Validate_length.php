<?php

namespace WorkFrame\Processors;

class Validate_length extends Processor {
	private $min_length;
	private $max_length;
	function __construct($min_length=0, $max_length=PHP_INT_MAX) {
		$this->min_length = $min_length;
		$this->max_length = $max_length;
	}
	function server_side($field_name, $value) {
		$value = $value ?? '';
		if (strlen($value) < $this->min_length) {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Value is too short (min length: '.$this->min_length.')',
			];
		} else if (strlen($value) > $this->max_length) {
			return [
				'field_name' => $field_name,
				'is_error' => TRUE,
				'error_message' => 'Value is too long (max length: '.$this->max_length.')',
			];
		} else {
			return TRUE;
		}
	}

	function client_side($form_id, $field_name, $client_side_processor_args, $name_container_array) {
		$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
		return	'
			var result = true;
			var value = $("#' . $field_id . '").val();
			if(value.length < '.$this->min_length.') {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Value is too short (min length: '.$this->min_length.')"
				};
			} else if(value.length > '.$this->max_length.') {
				result = {
					field_name : "' . $field_name . '",
					is_error : true,
					error_message : "Value is too long (max length: '.$this->max_length.')"
				};
			}
		';
	}


}
