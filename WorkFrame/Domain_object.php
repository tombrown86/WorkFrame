<?php
/**
 * Entities extend me.
 */
namespace WorkFrame;

class Domain_object {

	use WorkFrame_component_trait;

	protected $scenarios = [];
	protected $scenario = null;
	protected $field_labels = [];

	function __construct() {
		
	}

	function set_scenario($scenario) {
		$this->scenario = $scenario;
	}

	function get_scenario() {
		return $this->scenario;
	}

	function from_assoc($data, $respect_current_scenario = TRUE) {
		//if(!is_array($data)) die(print_r($data) . debug_print_backtrace());
		foreach ($data as $k => $v) {
			if (is_string($k)
					&& $k[0] != '_'
					&& (!$respect_current_scenario || !isset($this->scenario) || in_array($k, $this->scenarios[$this->scenario]))) {
				$set_method = 'set_' . $k;
				if (is_callable([$this, $set_method])) { // We want to just ignore vars in data that aren't relevant
					try {
						$this->$set_method($v);
					} catch (\WorkFrame\Exceptions\No_property_to_get_or_set_exception $e) {
						// If using magic get set trait, callable always returns true so must instead catch invalid assignment attempts like this
					}
				}
			}
		}
	}

	function scenario_fields_to_assoc($scenario = null) {
		isset($scenario) or $scenario = $this->scenario;
		if (isset($scenario, $this->scenarios[$scenario])) {
			return $this->to_assoc($this->scenarios[$scenario], FALSE);
		}
		return [];
	}

	function to_assoc($fields = NULL, $respect_current_scenario = TRUE) {
		$data = [];
		foreach ($fields as $k) {
			if (is_string($k)
					&& $k[0] != '_'
					&& (!$respect_current_scenario || !isset($this->scenario) || in_array($k, $this->scenarios[$this->scenario]))) {
				$get_method = 'get_' . $k;
				if (is_callable([$this, $get_method])) {
					try {
						$data[$k] = $this->$get_method();
					} catch (\WorkFrame\Exceptions\No_property_to_get_or_set_exception $e) {                                                                                 
                    }    
				}
			}
		}
		return $data;
	}

	/*
	  function extract($respect_scenario=TRUE) {
	  $data = [];
	  foreach( as $k=>$v) {
	  if(!$respect_scenario || !isset($this->scenario) || in_array($k, $this->scenarios[$this->scenario])) {
	  $get_method = 'set_'.$k;
	  if(method_exists($this, $set_method)) {
	  $data[$k] => $this->$set_method($v);
	  } else {
	  $this->$k = $v;
	  }
	  }
	  }
	  } */

	function get_field_label($field_name) {
		if (isset($this->field_labels[$field_name])) {
			return $this->field_labels[$field_name];
		} else {
			return $field_name;
		}
	}

	/**
	 * Called before processing
	 * Override me for any heavy lifting required for processing
	 */
	function prepare_processors() {
	}

}
