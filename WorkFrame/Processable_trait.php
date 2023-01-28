<?php
/**
 * Attach me to things that have properties that required processing by \WorkFrame\Processor's
 * Proviedes functions to run processors and deal with errors/warnings 
 * This can also construct client side JS for processors (provided they support a client side implementation)
 */
namespace WorkFrame;

trait Processable_trait {

	public $_is_processible = true; //  Possibly temporary. This allows descendants of classes using this trait to be tested as processable (since php provides no way of doing this with instanceof or class_uses, etc)
	protected $errors = [];
	protected $warnings = [];
	protected $processors = [];
	private $_prepared_processors = FALSE;
	private $_is_processed = FALSE;
	private $_bootstrap_form_tools;
	private $_form_tools;

	function __construct() {
		
	}

	private function _prepare_processors() {
		if (!$this->_prepared_processors) {
			$this->prepare_processors();
			// If a lazy programmer declared validator as a string, create it here
			foreach ($this->processors as $k => $processor) {
				if (is_string($processor['processor'])) {
					$class = '\\' . APP_NAMESPACE . '\\Processors\\' . $processor['processor'];
					if (class_exists($class)) {
						$this->processors[$k]['processor'] = new $class();
					} else {
						$class = '\\WorkFrame\\Processors\\' . $processor['processor'];
						if (class_exists($class)) {
							$this->processors[$k]['processor'] = new $class();
						} else {
							throw new \WorkFrame\Exceptions\Unknown_processor_exception('', $processor['processor']);
						}
					}
				}
			}
			$this->_prepared_processors = TRUE;
		}
	}

	// Use me for form validation or value sanitizing or whatever
	function process($stop_on_error = FALSE) {
		$this->_prepare_processors();
		$this->errors = [];
		foreach ($this->processors as $process) {
			if (method_exists($process['processor'], 'server_side') && (!$this instanceof \WorkFrame\Domain_object || empty($process['scenarios']) || in_array($this->scenario, $process['scenarios']))) {
				foreach ($process['fields'] as $field_names) {
					if (is_array($field_names)) {
						$value = [];
						foreach ($field_names as $f) {
							$get_func_name = 'get_' . $f;
							$value[$f] = $this->$get_func_name();
						}
					} else {
						$get_func_name = 'get_' . $field_names;
						$value = $this->$get_func_name();
					}

					$result = $process['processor']->check_rule_pre_conditions($this, $value);
					if ($result === FALSE) {
						$result = $process['processor']->server_side($field_names, $value, isset($process['args']) ? $process['args'] : NULL, $this);
					}

					if ($result === TRUE) {
						continue;
					}

					$field_name = $result['field_name'];

					if (!isset($result['field_label'])) {
						if (method_exists($this, 'get_field_label')) {
							$result['field_label'] = $this->get_field_label($field_name);
						} else {
							$result['field_label'] = $result['field_name'];
						}
					}

					if (isset($result['new_value'])) {
						$this->$field_name = $result['new_value'];
					}

					if (isset($result['is_error']) && $result['is_error']) {
						$this->errors[] = $result;
						if ($stop_on_error) {
							return FALSE;
						}
					} else if (isset($result['is_warning']) && $result['is_warning']) {
						$this->warnings[] = $result;
					}
				}
			}
		}
		$this->_is_processed = TRUE;
		return count($this->errors) === 0;
	}

	function is_processed() {
		return $this->_is_processed;
	}

	function set_is_processed($is_processed) {
		$this->_is_processed = $is_processed;
	}

	function get_client_side_processor_code($form_id, $name_container_array = NULL) {
		$this->_prepare_processors();

		$form_tools = new \WorkFrame\Html\Form_tools($form_id, $this, $name_container_array);

		$funcs_js = $async_funcs_js = [];
		foreach ($this->processors as $process) {
			$processor_namespace = explode('\\', get_class($process['processor']));
			$processor_name = array_pop($processor_namespace);

			if ((!$this instanceof \WorkFrame\Domain_object || empty($process['scenarios']) || in_array($this->scenario, $process['scenarios']))) {
				foreach ($process['fields'] as $field_names) {
					$func_body_js = '';
					if (method_exists($process['processor'], 'client_side')) {
						$func_body_js = $process['processor']->client_side($form_id, $field_names, isset($process['client_side_processor_args']) ? $process['client_side_processor_args'] : NULL, $name_container_array);
						if (isset($func_body_js)) {
							$condition_js = $process['processor']->get_pre_conditions_client_side_check($form_id, $field_names, $name_container_array);
							$func_body_js = sprintf($condition_js, $func_body_js);
						}
					}

					$field_names = array_values((array) $field_names); // Force non array to array containing the item, also ensure it has sequental numeric keys
					foreach ($field_names as $k => $field_name) {
						$this_fields_process_func_name = $form_tools->js_process_field_function_name($field_name);
						$field_id = $form_tools::field_id($form_id, $field_name, $name_container_array);
						if ($process['processor'] instanceof \WorkFrame\Processors\Async_processor) {
							// TODO: Handle multiple $field_names as single async call!

							$func_body_js .= ' 
								result = null;
								if(typeof window["' . $this_fields_process_func_name . '_result"] == "undefined" || window["' . $this_fields_process_func_name . '_result"] == null)  {
									var timeout_length = 0;
									if(event_type == "onkeyup") {
										timeout_length = 1000;
									}
									clearTimeout(window["' . $this_fields_process_func_name . '_timer"]);
									var make_ajax_req = function() {
										$("#' . $field_id . '").addClass("wf_validating_async");
										$.post(' . json_encode($process['processor']->get_processor_url()) . ' , {processor_name: ' . json_encode($processor_name) . ', value: $("#' . $field_id . '").val(), field_name: ' . json_encode($field_name) . ', form_id: ' . json_encode($form_id) . ', field_id: ' . json_encode($field_id) . ', data: ' . json_encode($process['processor']->get_data()) . '}, function(json) {
											if(json.result) {
												// once server returns with response, recall the outer func and pass thru the result using this global var
												console.log(json)
												if(json.value == $("#' . $field_id . '").val()) {
													window["' . $this_fields_process_func_name . '_result"] = json.result;
													' . $this_fields_process_func_name . '();
												} else {
													// just ignore if value has changed
												}
											} else {
												console.log("Invalid response back from async processor: ");
												console.log(json);
											}
										}, "json");
									};
									window["' . $this_fields_process_func_name . '_timer"] = setTimeout(make_ajax_req, timeout_length);//TODO: Only relevant to have this delay if called from onclick
								} else {
									$("#' . $field_id . '").removeClass("wf_validating_async");
									result = window["' . $this_fields_process_func_name . '_result"];
									window["' . $this_fields_process_func_name . '_result"] = null;
								}
							';
						}

						isset($funcs_js[$this_fields_process_func_name]) or $funcs_js[$this_fields_process_func_name] = ['js' => '', 'field_id' => $field_id];
						$funcs_js[$this_fields_process_func_name]['js'] .= '
							if(!errors.length) {';
						$funcs_js[$this_fields_process_func_name]['js'] .= $func_body_js . "\n";
						$funcs_js[$this_fields_process_func_name]['js'] .= '
								if(result != null) {
									if(result !== true && "new_value" in result) {
										$("#' . $field_id . '").val(result["new_value"]);
									}
									if(result !== true && "is_error" in result && result["is_error"]) {
										errors.push(result);
									} else if(result !== true && "is_warnings" in result && result["is_warning"]) {
										warnings.push(result);
									} else if(result) {
										successes.push("' . $field_id . '");
									}
								}
							}
						';
					}
				}
			}
		}

		// First add funcs to validate individual fields
		$js = '
			<script type="text/javascript">';
		foreach ($funcs_js as $process_field_func_name => $func_js) {
			$js .= '
				var ajax_promise_' . $process_field_func_name . ' = $.Deferred().resolve().promise();
				function ' . $process_field_func_name . '(event_type) {
					_wf_reset_field_processing_output("' . $func_js['field_id'] . '");
					var errors = [];
					var successes = [];
					var warnings = [];
';
			$js .= $func_js['js'] . '
						if(errors.length) {
							_wf_show_errors("' . $form_id . '", errors'.(isset($name_container_array) ? ', "'.$name_container_array.'"' : '').');
						} else {
							_wf_show_successes("' . $form_id . '", successes'.(isset($name_container_array) ? ', "'.$name_container_array.'"' : '').');
							_wf_show_warnings("' . $form_id . '", warnings'.(isset($name_container_array) ? ', "'.$name_container_array.'"' : '').');
						}
					return errors.length == 0;
				}';
		}


		// Now add a func to validate everything (for onsubmit)
		$process_form_func_name = $form_tools->js_process_form_function_name();
		$js .= 'function ' . $process_form_func_name . '(){
				var success = true;
';
		foreach ($funcs_js as $process_field_func_name => $func_js) {
			$js .= '
				var result = ' . $process_field_func_name . '();
				success = (result==true || result==null || ((typeof result.is_error != "undefined") && !result.is_error)) && success;' . "\n";
		}
		$js .= '
				return success;
			}
			</script>';

		return $js;
		// TODO: Minify + Cache me?  Or just minify .. use md5 to uniquely identify ? 
	}

	function field_has_processors($field_name) {
		$this->_prepare_processors();
		foreach ($this->processors as $process) {
			if (!$this instanceof \WorkFrame\Domain_object || empty($process['scenarios']) || in_array($this->scenario, $process['scenarios'])) {
				foreach ($process['fields'] as $field_names) {
					if (in_array($field_name, (array) $field_names)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	function get_errors_by_field() {
		$field_errors = [];
		foreach ($this->errors as $error) {
			$field_errors[$error['field_name']][] = $error;
		}
		return $field_errors;
	}

	function get_warnings_by_field() {
		$field_warnings = [];
		foreach ($this->warnings as $warning) {
			$field_warnings[$warning['field_name']][] = $warning;
		}
		return $field_warnings;
	}

	function field_has_error($field_name) {
		$errors_by_field = $this->get_errors_by_field();
		return !empty($errors_by_field[$field_name]);
	}

	function field_has_warning($field_name) {
		$warnings_by_field = $this->get_warnings_by_field();
		return !empty($warnings_by_field[$field_name]);
	}

	function get_form_tools($form_id) {
		if (!isset($this->_form_tools) || $this->_form_tools->get_form_id() != $form_id) {
			$this->_form_tools = new \WorkFrame\Html\Form_tools($form_id, $this);
		}
		return $this->_form_tools;
	}

	function get_bootstrap_form_tools($form_id) {
		if (!isset($this->_bootstrap_form_tools) || $this->_bootstrap_form_tools->get_form_id() != $form_id) {
			$this->_bootstrap_form_tools = new \WorkFrame\Html\Bootstrap_form_tools($form_id, $this);
		}
		return $this->_bootstrap_form_tools;
	}

	function all_fields() {
		return array_keys(get_object_vars($this));
	}

}
