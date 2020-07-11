<?php

namespace WorkFrame\Processors;

class Processor {
	use \WorkFrame\WorkFrame_component_trait;
	#function server_side($field_name, $value, $data=null, $field_id=null/*not always available*/);
	#function client_side($form_id, $field_name);
	
	private $condition_field_names = [];
	private $allow_empty = FALSE;
	
	function set_condition_field_names($condition_field_names) {
		$this->condition_field_names = (array)$condition_field_names;
	}
	
	function set_allow_empty($allow_empty) {
		$this->allow_empty = (bool)$allow_empty;
	}
	
	function check_rule_pre_conditions($processable, $value) {
		if($this->allow_empty && empty($allow_empty)) {
			return TRUE;
		}
		foreach ($this->condition_field_names as $condition_field_name) {
			$get_func = 'get_'.$condition_field_name;
			if(!(bool)$processable->$get_func()) {
				return TRUE;
			}
		}
		return FALSE;
	}
	function get_pre_conditions_client_side_check($form_id, $field_names, $name_container_array) {
		if(count($this->condition_field_names) || $this->allow_empty) {
			$js = 'if(';
			$conditions = [];
			foreach($this->condition_field_names as $condition_field_name) {
				$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $condition_field_name, $name_container_array);
				$conditions[] = '!!$("#'.$field_id.'").val()';
			}
			if($this->allow_empty) {
				foreach((array)$field_names as $field_name) {
					$field_id = \WorkFrame\Html\Form_tools::field_id($form_id, $field_name, $name_container_array);
					$conditions[] = '$("#'.$field_id.'").val().length > 0';
				}
			}
			$js .= implode(' && ', $conditions);
			$js .= ') {%s} else {result = true;}';
			return $js;
		} else {
			return '%s';
		}
	}
	
}
