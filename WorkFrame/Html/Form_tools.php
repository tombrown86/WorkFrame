<?php

namespace WorkFrame\Html;

class Form_tools {

	private $processable; # \WorkFrame\Processable_trait
	private $form_id;
	private $process_onchange = TRUE;
	private $process_onkeyup = TRUE;
	private $process_onblur = TRUE;
	private $process_onclick = TRUE;
	private $process_onsubmit = TRUE;
	private $name_container_array = NULL;
	protected $custom_field_error_class = '';
	protected $custom_field_success_class = '';
	protected $custom_field_warning_class = '';
	protected $custom_field_group_class = '';
	protected $custom_label_class = '';
	protected $custom_small_input_height_wrapper_class = '';
	protected $custom_medium_input_height_wrapper_class = '';
	protected $custom_large_input_height_wrapper_class = '';
	protected $custom_small_width_wrapper_class = '';
	protected $custom_medium_width_wrapper_class = '';
	protected $custom_large_width_wrapper_class = '';
	protected $custom_control_field_class = ''; # Is used on wide inputs.. (all except checkboxes and radio)

	function __construct($form_id, $processable = NULL, $name_container_array = NULL) {
		$this->form_id = $form_id;
		$this->processable = $processable;
		$this->name_container_array = $name_container_array;
	}

	function set_process_onchange($process_onchange) {
		$this->process_onchange = $process_onchange;
	}

	function set_process_onkeyup($process_onkeyup) {
		$this->process_onkeyup = $process_onkeyup;
	}

	function set_process_onblur($process_onblur) {
		$this->process_onblur = $process_onblur;
	}
	function set_process_onclick($process_onclick) {
		$this->process_onclick = $process_onclick;
	}

	function set_process_onsubmit($process_onsubmit) {
		$this->process_onsubmit = $process_onsubmit;
	}

	function set_processable($processable) {
		$this->processable = $processable;
	}

	function set_form_id($form_id) {
		$this->form_id = $form_id;
	}

	function set_custom_field_error_class($custom_field_error_class) {
		$this->custom_field_error_class = $custom_field_error_class;
	}

	function set_custom_field_warning_class($custom_field_warning_class) {
		$this->custom_field_warning_class = $custom_field_warning_class;
	}

	function set_custom_field_success_class($custom_field_success_class) {
		$this->custom_field_success_class = $custom_field_success_class;
	}

	function set_custom_field_group_class($custom_field_group_class) {
		$this->custom_field_group_class = $custom_field_group_class;
	}

	function set_custom_label_class($custom_label_class) {
		$this->custom_label_class = $custom_label_class;
	}

	function set_custom_small_input_height_wrapper_class($custom_small_input_height_wrapper_class) {
		$this->custom_small_input_height_wrapper_class = $custom_small_input_height_wrapper_class;
	}

	function get_custom_small_input_height_wrapper_class() {
		return $this->custom_small_input_height_wrapper_class;
	}

	function set_custom_medium_input_height_wrapper_class($custom_medium_input_height_wrapper_class) {
		$this->custom_medium_input_height_wrapper_class = $custom_medium_input_height_wrapper_class;
	}

	function get_custom_medium_input_height_wrapper_class() {
		return $this->custom_medium_input_height_wrapper_class;
	}

	function set_custom_large_input_height_wrapper_class($custom_large_input_height_wrapper_class) {
		$this->custom_large_input_height_wrapper_class = $custom_large_input_height_wrapper_class;
	}

	function get_custom_large_input_height_wrapper_class() {
		return $this->custom_large_input_height_wrapper_class;
	}

	function set_custom_control_field_class($custom_control_field_class) {
		$this->custom_control_field_class = $custom_control_field_class;
	}

	function get_custom_control_field_class() {
		return $this->custom_control_field_class;
	}

	function error_list() {
		$this->_check_processable_set(__METHOD__);
		$html = '';
		$errors_by_field = $this->processable->get_errors_by_field();

		if ($this->processable->is_processed() && count($errors_by_field)) {
			$html = '<ul id="' . htmlspecialchars($this->form_id ?? '') . '__wf_errors" class="wf_errors">';

			foreach ($errors_by_field as $field_name => $errors) {
				$html .= '<li>';
				$html .= '<span class="wf_error_field_name">' . htmlspecialchars($errors[0]['field_label'] ?? '') . '</span>';
				$html .= '<ul class="wf_field_errors">';
				
				foreach ($errors as $error) {
					$html .= '<li>';
					$html .= htmlspecialchars($error['error_message'] ?? '');
					if (!empty($error['error_details'])) {
						$html .= ' <span class="wf_error_details">' . htmlspecialchars($error['error_details']) . '</span>';
					}
					$html .= '</li>';
				}
				$html .= '</ul>';
			}
			$html .= '</ul>';
		}
		return $html;
	}

	function form_open($attributes = [], $classes = []) {
		isset($attributes['id']) || $attributes['id'] = $this->form_id;
		isset($attributes['role']) || $attributes['role'] = 'form';
		isset($attributes['class']) || $attributes['class'] = trim('wf_form ' . static::classes_string($classes));

		if ($this->process_onsubmit) {
			isset($attributes['onsubmit']) || $attributes['onsubmit'] = 'return ' . $this->js_process_form_function_name() . '("onsubmit");';
		}

		$attributes = static::attributes_string($attributes);

		return '<form' . $attributes . '>';
	}

	function form_close() {
		return '</form>';
	}

	function label($field_name, $label, $attributes = [], $classes = []) {
		isset($attributes['class']) || $attributes['class'] = trim($this->custom_label_class . ' ' . static::classes_string($classes));
		isset($attributes['for']) || $attributes['for'] = static::field_id($this->form_id, $field_name, $this->name_container_array);
		$attributes = static::attributes_string($attributes);

		return '<label' . $attributes . '>' . htmlspecialchars($label ?? '') . '</label>';
	}

	// TODO: merge the following functions as they are basically the same
	function field_error_container($field_name) {
		$this->_check_processable_set(__METHOD__);
		$errors = $this->processable->get_errors_by_field();
		$field_errors_html = '';
		if ($this->processable->is_processed() && $this->processable->field_has_error($field_name)) {
			$field_errors_html .= '<ul class="wf_field_errors_inline">';
			foreach ($errors[$field_name] as $error) {
				$field_errors_html .= '<li>';
				$field_errors_html .= htmlspecialchars($error['error_message'] ?? '');
				if (!empty($error['error_details'])) {
					$field_errors_html .= ' <span class="wf_error_details">' . htmlspecialchars($error['error_details']) . '</span>';
				}
				$field_errors_html .= '</li>';
			}
			$field_errors_html .= '</ul>';
		}
		return '<div id="' . htmlspecialchars(static::field_id($this->form_id, $field_name,$this->name_container_array)) . '__wf_field_error_container" class="wf_error_container">' . $field_errors_html . '</div>';
	}

	function field_warning_container($field_name) {
		$this->_check_processable_set(__METHOD__);
		$warnings = $this->processable->get_warnings_by_field();
		$field_warnings_html = '';
		if ($this->processable->is_processed() && $this->processable->field_has_warning($field_name)) {
			$field_warnings_html .= '<ul class="wf_field_warnings_inline">';
			foreach ($warnings[$field_name] as $warning) {
				$field_warnings_html .= '<li>';
				$field_warnings_html .= htmlspecialchars($warning['warning_message'] ?? '');
				if (!empty($warning['warning_details'])) {
					$field_warnings_html .= ' <span class="wf_warning_details">' . htmlspecialchars($warning['warning_details']) . '</span>';
				}
				$field_warnings_html .= '</li>';
			}
			$field_warnings_html .= '</ul>';
		}
		return '<div id="' . htmlspecialchars(static::field_id($this->form_id, $field_name,$this->name_container_array)) . '__wf_field_warning_container" class="wf_warning_container">' . $field_warnings_html . '</div>';
	}

	function field_group_open($main_field_name, $attributes = [], $classes = []) {
		$this->_check_processable_set(__METHOD__);
		$classes = static::classes_string($classes);

		if ($this->processable->is_processed()) {
			if ($this->processable->field_has_error($main_field_name)) {
				$classes .= ' ' . $this->get_custom_field_error_class() . ' wf_error_field';
			} else if ($this->processable->field_has_warning($main_field_name)) {
				$classes .= ' ' . $this->get_custom_field_warning_class() . ' wf_warning_field';
			} else {
				$classes .= ' ' . $this->get_custom_field_success_class() . ' wf_success_field';
			}
		}

		isset($attributes['id']) || $attributes['id'] = static::field_id($this->form_id, $main_field_name,$this->name_container_array) . '__field_group';
		isset($attributes['class']) || $attributes['class'] = trim('wf_field_group ' . $this->get_custom_field_group_class() . ' ' . trim($classes));
		$attributes = static::attributes_string($attributes);

		return '<div ' . $attributes . '>';
	}

	function field_group_close() {
		return '</div>';
	}

	function get_custom_field_error_class() {
		return $this->custom_field_error_class;
	}

	function get_custom_field_success_class() {
		return $this->custom_field_success_class;
	}

	function get_custom_field_warning_class() {
		return $this->custom_field_warning_class;
	}

	function get_custom_field_group_class() {
		return $this->custom_field_group_class;
	}

	function js_process_form_function_name() {
		return '_wf_process__' . (isset($this->name_container_array) ? $this->name_container_array . '__' : '') . $this->form_id;
	}

	function js_process_field_function_name($field_name) {
		$id = static::field_id($this->form_id, $field_name,$this->name_container_array);
		$id = preg_replace('/[^\w_]/i','_',$id);
		return '_wf_process__' .$id ;
	}
//
//	function js_remote_process_field_function_name($field_name) {
//		return '_wf_process_remote__' . (static::field_id($this->form_id, $field_name));
//	}

	function legend($text, $attributes = [], $classes = []) {
		isset($attributes['class']) || $attributes['class'] = static::classes_string($classes);
		$attributes = static::attributes_string($attributes);

		return '<legend' . $attributes . '>' . htmlspecialchars($text ?? '') . '</legend>';
	}

	function input_wrapper_open() {
		$classes = [];
		$classes[] = 'wf_input_wrapper';

		return '<div class="' . self::classes_string($classes) . '">';
	}

	function input_wrapper_close() {
		return '</div>';
	}

	function hidden_field($field_name = '', $args=[]) {
			$value = $this->_get_value($field_name);
			$type = 'hidden';
			extract($args);

			isset($args['classes']) or $args['classes'] = [];
			$attributes = $this->_field_attributes($field_name, $args);
			$attributes['type'] = $type;
			$attributes['value'] = $value;

			return '<input' . static::attributes_string($attributes) . '/>';
	}

	function input_field($field_name = '', $args=[]) {
		$value = $this->_get_value($field_name);
		$placeholder = '';
		$type = 'text';
		$height = 'medium';
		$width = 'medium';
		extract($args);
		
		isset($args['classes']) or $args['classes'] = [];
		$args['classes'][] = 'wf_input_wrapper_height_' . $height;
		$args['classes'][] = 'wf_input_wrapper_width_' . $width;
		$args['classes'][] = $this->get_custom_control_field_class();
		
		$attributes = $this->_field_attributes($field_name, $args);
		isset($attributes['type']) || $attributes['type'] = $type;
		isset($attributes['placeholder']) || $attributes['placeholder'] = $placeholder;
		
		if($type != 'password') {
			$attributes['value'] = $value;
		} else {
			unset($attributes['value']);
		}
		return '<input' . static::attributes_string($attributes) . '/>';
	}

	function checkbox_field($field_name = '', $args=[]) {
		$value = $this->_get_value($field_name);
		$height = 'medium';
		$width = 'medium';
		extract($args);
		
		isset($args['classes']) or $args['classes'] = [];
		$args['classes'][] = 'wf_input_wrapper_height_' . $height;
		$args['classes'][] = 'wf_input_wrapper_width_' . $width;
		
		$attributes = $this->_field_attributes($field_name, $args);
		$attributes['type'] = 'checkbox';
		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : '1';
		if($value == $attributes['value']) {
			$attributes['checked'] = 'checked';
		}
		return '<input type="hidden" name="'.htmlspecialchars($attributes['name'] ?? '').'" value="0"/>'
			.' <input' . static::attributes_string($attributes) . '/>';
	}

	function select_field($field_name='', $args=[], $multiple=FALSE) {
		$value = $this->_get_value($field_name);
		$options=[];
		$height = 'medium';
		$width = 'medium';
		$identical_value = FALSE;
		
		extract($args);
		
		isset($args['classes']) or $args['classes'] = [];
		$args['classes'][] = 'wf_input_wrapper_height_' . $height;
		$args['classes'][] = 'wf_input_wrapper_width_' . $width;
		$args['classes'][] = $this->get_custom_control_field_class();
		$attributes = $this->_field_attributes($field_name, $args);
		
		if($multiple) {
			$attributes['name'] .= '[]';
			$attributes['multiple'] = 'multiple';
		}
		
		return '<select' . static::attributes_string($attributes) . '>'
				. $this->select_options($options, $value, $identical_value)
				.'</select>';
	}
	
	/**
	 * 
	 * @param array $options  
	 * @param string $selected_value
	 * @return string
	 */
	static function select_options($options=[], $selected_value=NULL, $identical_value=FALSE) {
		$h = "\n";
		
//		$is_assoc = array_keys($options) !== $options;
//		
//		if(!$is_assoc) {
//			// check all keys were ints (not strings)
//			$all_keys_ints = TRUE;
//			foreach($options as $k=>$v) {
//				if(!is_int($k)) {
//					$all_keys_ints = FALSE;
//					break;
//				}
//			}
//			if($all_keys_ints) {
//				$options_assoc = [];
//				foreach($options as $k=>$v) {
//					$options_assoc[$v] = $v;
//				}
//				$options = $options_assoc;
//			}
//		}
		
		foreach($options as $k=>$v) {
			if(is_array($selected_value) && in_array($k, $selected_value)) {
				$selected_attr = 'selected="selected" ';
			} elseif($identical_value ? ($k === $selected_value) : ($k == $selected_value)) {	
				$selected_attr = 'selected="selected" ';
			} else {
				$selected_attr = '';
			}
			$h .= "<option ".$selected_attr."value=\"".  htmlspecialchars($k)."\">".  htmlspecialchars($v ?? '')."</option>\n";
		}
		return $h;
	}

	function textarea_field($field_name = '', $args=[]) {
		$value = $this->_get_value($field_name);
		$placeholder = '';
		$width = 'medium';
		$height = 'medium';
		extract($args);
		
		isset($args['classes']) or $args['classes'] = [];
		$args['classes'][] = 'wf_textarea_wrapper_height_' . $height;
		$args['classes'][] = 'wf_textarea_wrapper_width_' . $width;
		$args['classes'][] = $this->get_custom_control_field_class();
		
		$attributes = $this->_field_attributes($field_name, $args);
		isset($attributes['placeholder']) || $attributes['placeholder'] = $placeholder;
		return '<textarea' . static::attributes_string($attributes) . '>' . htmlspecialchars($value ?? '') . '</textarea>';
	}

	private function _field_attributes($field_name = '', $args=[]) {
		$required = FALSE;
		$height = 'medium';
		$width = 'medium';
		$attributes = [];
		$classes = [];
		$post_name = NULL;
		extract($args);

		$this->_check_processable_set(__METHOD__);
		isset($attributes['id']) || $attributes['id'] = $this->id_for_field($field_name);
		isset($attributes['class']) || $attributes['class'] = self::classes_string($classes);

		if(!isset($attributes['name'])) {
			$attributes['name'] = isset($post_name) ? $post_name : $field_name;
			if(isset($this->name_container_array)) {
				$attributes['name'] = $this->name_container_array . '['.$attributes['name'].']';
			}
		}

		if (!isset($attributes['required'])) {
			if ($required) {
				$attributes['required'] = 'required';
			}
		}

		if ($this->process_onchange && isset($this->processable) && $this->processable->field_has_processors($field_name)) {
			isset($attributes['onchange']) || $attributes['onchange'] = $this->js_process_field_function_name($field_name) . '("onchange")';
		}
		if ($this->process_onkeyup && isset($this->processable) && $this->processable->field_has_processors($field_name)) {
			isset($attributes['onkeyup']) || $attributes['onkeyup'] = $this->js_process_field_function_name($field_name) . '("onkeyup")';
		}
		if ($this->process_onblur && isset($this->processable) && $this->processable->field_has_processors($field_name)) {
			isset($attributes['onblur']) || $attributes['onblur'] = $this->js_process_field_function_name($field_name) . '("onblur")';
		}
		if ($this->process_onclick && isset($this->processable) && $this->processable->field_has_processors($field_name)) {
			isset($attributes['onclick']) || $attributes['onclick'] = $this->js_process_field_function_name($field_name) . '("onclick")';
		}
		return $attributes;
	}

	function get_form_id() {
		return $this->form_id;
	}

	function field_group($main_field_name, $field_html, $args = []) {
		$required = FALSE;

		$label = NULL;
		$field_error_container = TRUE;
		$field_warning_container = TRUE;
		$help_text = NULL;
		$help_text_inline = NULL;
		extract($args);

		$this->_check_processable_set(__METHOD__);

		if (is_null($label))
			$label = $this->processable->get_field_label($main_field_name);

		$html = $this->field_group_open($main_field_name) . "\n";
		if($label !== FALSE) {
			$html .= '	' . $this->label($main_field_name, $label . ($required ? ' *' : '')) . "\n";
		}
		$html .= '	' . $this->input_wrapper_open() . "\n";
		$html .= '		' . $field_html . "\n";
		$help_text_inline and $html .= '<small class="text-muted">'.$help_text_inline.'</small>';
		$help_text and $html .= '<p class="form-text text-muted">'.$help_text.'</p>';
		$field_error_container and $html .= '		' . $this->field_error_container($main_field_name) . "\n";
		$field_warning_container and $html .= '		' . $this->field_warning_container($main_field_name) . "\n";
		$html .= '	' . $this->input_wrapper_close() . "\n";
		$html .= $this->field_group_close() . "\n";
		return $html;
	}

	function input_field_group($field_name = '', $args = []) {
		extract($args);
		$field_html = $this->input_field($field_name, $args);
		return $this->field_group($field_name, $field_html, $args);
	}

	private function _get_value($field_name) {
		$get_method = 'get_'.$field_name;
		$value = '';
		if (is_callable([$this->processable, $get_method])) { 
			try {
				$value = $this->processable->$get_method();
			} catch (\WorkFrame\Exceptions\No_property_to_get_or_set_exception $e) {
			}
		}
		return $value;
	}
	
	function checkbox_field_group($field_name, $args = []) {
		$value = $this->_get_value($field_name);
		$attributes = [];
		$classes = [];
		$post_name = NULL;

		extract($args);
		$field_html = $this->checkbox_field($field_name, ['value'=>$value, 'attributes'=>$attributes, 'classes'=>$classes, 'post_name'=>$post_name]);
		return $this->field_group($field_name, $field_html, $args);
	}

	function select_field_group($field_name, $args=[], $multiple=FALSE) {
		$field_html = $this->select_field($field_name, $args, $multiple);
		return $this->field_group($field_name, $field_html, $args);
	}

	function textarea_field_group($field_name = '', $args = []) {
		$placeholder = '';
		$required = FALSE;
		$attributes = [];
		$classes = [];
		$width = 'medium';
		$height = 'medium';
		$type = 'text';
		$post_name = NULL;
		$value = $this->_get_value($field_name);

		extract($args);
		$field_html = $this->textarea_field($field_name, ['value'=>$value, 'placeholder'=>$placeholder, 'required'=>$required, 'width'=>$width, 'height'=>$height, 'attributes'=>$attributes, 'classes'=>$classes, 'type'=>$type, 'post_name'=>$post_name]);
		return $this->field_group($field_name, $field_html, $args);
	}

	function button($value, $extra_attributes, $classes) {
		return $this->_button('button', $value, $extra_attributes, $classes);
	}

	function submit($value, $extra_attributes, $classes) {
		return $this->_button('submit', $value, $extra_attributes, $classes);
	}

	function set_name_container_array($name_container_array) {
		$this->name_container_array = $name_container_array;
	}
	
	protected function _button($type, $value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' wf_button wf_button_' . $type;
		$attributes['class'] = $classes;
		$attributes['type'] = $type;
		$attributes = static::attributes_string($attributes);
		return '<button' . $attributes . '>' . $value . '</button>';
	}

	function id_for_field($field_name) {
		return self :: field_id($this->form_id, $field_name, $this->name_container_array);
	}

	static function field_id($form_id, $field_name, $name_container_array=NULL) {
		return (isset($name_container_array) ? $name_container_array.'__' : '') . $form_id . '__' . $field_name;
	}

	static function attributes_string($data) {
		if (is_string($data)) {
			return ' ' . trim($data);
		} else {
			$attributes = '';
			foreach ($data as $k => $v) {
				$attributes .= ' ' . $k . '="' . htmlspecialchars($v ?? '') . '"';
			}
			return $attributes;
		}
	}

	static function classes_string($classes) {
		if (is_string($classes)) {
			return trim($classes);
		} else {
			return implode(' ', array_unique($classes));
		}
	}

	private function _check_processable_set($function_name) {
		//this doesn't work if trait is present via it's parent:		if (!in_array('WorkFrame\\Processable_trait', class_uses($this->processable))) {
		if (isset($this->processable->_is_processable) && $this->processable->_is_processable) {
			throw new \WorkFrame\Exceptions\Unset_processable_exception('This function ' . $function_name . ' of ' . __CLASS__ . ' requires an processable');
		}
	}

	private function _input_height_class($size) {
		switch ($size) {
			case 'small':
				return $this->custom_small_input_height_wrapper_class;
			case 'large':
				return $this->custom_large_input_height_wrapper_class;
			case 'medium':
				return $this->custom_medium_input_height_wrapper_class;
				
			default:
				return $size;
		}
	}

	private function _width_class($size) {
		switch ($size) {
			case 'small':
				return $this->custom_small_width_wrapper_class;
			case 'large':
				return $this->custom_large_width_wrapper_class;
			case 'medium':
				return $this->custom_medium_width_wrapper_class;
				
			default:
				return $size;
		}
	}

}
