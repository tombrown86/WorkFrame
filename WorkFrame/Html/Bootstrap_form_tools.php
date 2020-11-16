<?php

namespace WorkFrame\Html;

class Bootstrap_form_tools extends Form_tools {

	protected $custom_field_error_class = 'has-error'/* boostrap 3 */;
	protected $custom_field_success_class = 'has-success'/* boostrap 3 */;
	protected $custom_field_warning_class = 'has-warning'/* boostrap 3 */;
	protected $custom_field_group_class = 'form-group'/* boostrap 3 */;
	protected $custom_label_class = 'control-label'/* boostrap 3 */;
	protected $custom_small_input_height_wrapper_class = 'input-sm'/* boostrap 3 */;
	protected $custom_medium_input_height_wrapper_class = 'input-md'/* boostrap 3 */;
	protected $custom_large_input_height_wrapper_class = 'input-lg'/* boostrap 3 */;
	protected $custom_small_width_wrapper_class = 'col-lg-4 col-sm-6 col-md-8'/* boostrap 3 */;
	protected $custom_medium_width_wrapper_class = 'col-lg-6 col-sm-8 col-md-10'/* boostrap 3 */;
	protected $custom_large_width_wrapper_class = 'col-lg-8 col-sm-10 col-md-12'/* boostrap 3 */;
	protected $custom_control_field_class = 'form-control'/* boostrap 3 */;

	function primary_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-primary';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function primary_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-primary';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function secondary_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-secondary';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function secondary_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-secondary';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function success_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-success';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function success_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-success';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function info_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-info';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function info_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-info';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function warning_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-warning';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function warning_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-warning';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function danger_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-danger';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function danger_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-danger';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function link_button($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-link';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function link_submit($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-link';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function primary_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-primary-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function primary_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-primary-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function secondary_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-secondary-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function secondary_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-secondary-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function success_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-success-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function success_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-success-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function info_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-info-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function info_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-info-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function warning_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-warning-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function warning_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-warning-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function danger_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-danger-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function danger_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-danger-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	function link_button_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-link-outline';
		return $this->_button('button', $value, $attributes, $classes);
	}

	function link_submit_outline($value, $attributes = [], $classes = []) {
		$classes = static::classes_string($classes) . ' ' . 'btn btn-link-outline';
		return $this->_button('submit', $value, $attributes, $classes);
	}

	/**
	 * 
	 * @param string $type (danger, warning, info, success)
	 * @param string $strong_html
	 * @param string $message_html
	 * @param array $options an array of teh following keys - set value for each to true to enable the option (dismissible, animate)
	 * @return string $html
	 */
	static function alert($type, $strong_html = '', $message_html = '', $options = []) {
		$classes[] = 'alert';
		$classes[] = 'alert-'.$type;
		if( isset($options['dismissible']) && $options['dismissible'] === TRUE ) {
			$classes[] = 'alert-dismissible';
			if( isset($options['animate']) && $options['animate'] === TRUE ) {
				$classes[] = 'fade';
				$classes[] = 'show';
			}
		}
		$classes = static::classes_string($classes);

		$html = '';
		$html .= '<div class="' . $classes . '">' . "\n";
		if( isset($options['dismissible']) && $options['dismissible'] === TRUE ) {
			$html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' . "\n";
			$html .= '<span aria-hidden="true">&times;</span>' . "\n";
			$html .= '</button>' . "\n";
		}
		$html .= '<strong>' . $strong_html . '</strong> ' . $message_html . "\n";
		$html .= '</div>' . "\n";
		
		return $html;
	}

}
