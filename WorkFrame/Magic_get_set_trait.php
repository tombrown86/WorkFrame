<?php
/**
 * Convenience for the lazy programmer
 * 
 * Allows privates to be set/got by referencing them as properties.
 * Also makes calls to get/set_blah() automatically get/set $this->blah
 */

namespace WorkFrame;
trait Magic_get_set_trait {

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}

	function __call($method, $params) {
		if(strlen($method) > 4) {
			$bt = debug_backtrace();
			$caller = array_shift($bt);
			$prop = substr($method, 4);
			$get_or_set = substr($method, 0, 4);
			if(in_array($get_or_set , ['get_', 'set_'])) {
				if(!property_exists($this, $prop)) {
					throw new \WorkFrame\Exceptions\No_property_to_get_or_set_exception('Invalid magic get_ or set_ ('.$get_or_set.$prop.') on '.get_class($this).".......".'. Called in '.$caller['file'].' line '.$caller['line'].'.', $method);
				}
				if($get_or_set == 'get_') {
					return $this->$prop;
				} else if($get_or_set == 'set_') {
					$this->$prop = isset($params[0]) ? $params[0] : NULL;
					return;
				}
			}
		}
		trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'() (triggered from magic __call)', E_USER_ERROR);
	}
}
