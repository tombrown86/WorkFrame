<?php
/**
 * Everything uses this slutty trait, it'll fetch the loaded and load the unloaded
 */
namespace WorkFrame;
	
trait WorkFrame_component_trait {	
	
	static function SERVICE($name, $shared_identifier=FALSE) {
		return self::_load($name, $shared_identifier, 'Services', 'service');
	}
	static function DATA_MAPPER($name, $shared_identifier=FALSE) {
		return self::_load($name, $shared_identifier, 'Data_mappers', 'data_mapper');
	}
	static function DOMAIN_OBJECT($name, $shared_identifier=FALSE) {
		return self::_load($name, $shared_identifier, 'Domain_objects', 'domain_object');
	}
	static function LIBRARY($name, $shared_identifier=FALSE) {
		return self::_load($name, $shared_identifier, 'Libraries', 'library');
	}

	/*function _unload_service($name) {
		self::_unload($name, 'service');
	}
	function _unload_data_mapper($name) {
		self::_unload($name, 'data_mapper');
	}
	function _unload_domain_mapper($name) {
		self::_unload($name, 'domain_mapper');
	}
	function _unload_service($name) {
		self::_unload($name, 'service');
	}
	private function _unload($name, $type){ 
		unset(self::_loaded_things[$type][$name]);
	}*/
	
	/**
	 * Loads the thing.
	 * If shared_identifier is given, it'll shove the new instance
	 * inside the WORKFRAME object using the identifier as key
	 */
	static private function  _load($name, $shared_identifier, $namespace, $type) {
		// first look in App, then WorkFrame
		$class = '\\'.APP_NAMESPACE.'\\'.$namespace.'\\'.$name;

		if(!class_exists($class)) {
			$class = '\\'.APP_NAMESPACE.'\\'.$namespace.'\\'.ucfirst($name);
			if(!class_exists($class)) {
				$class = '\\WorkFrame\\'.$namespace.'\\'.$name;
				if(!class_exists($class)) {
					$class = '\\WorkFrame\\'.$namespace.'\\'.ucfirst($name);
					if(!class_exists($class)) {
						throw new \WorkFrame\Exceptions\Loader_exception('Unable to load '.$type.': '.$name.". Not found in App or WorkFrame.");
					}
				}
			}
		}
		
		if(!$shared_identifier) {
			return new $class();
		} else {
			$wf = _workframe();
			if(!isset($wf->LOADED[$shared_identifier])) {
                // claim array entry before instantiating class to
                // avoid potential recursion when component constructors
                // init each other
                $wf->LOADED[$shared_identifier] = 'defined';
				$wf->LOADED[$shared_identifier] = new $class();
			}
			return $wf->LOADED[$shared_identifier];
		}

	}

	// magic get for shared loaded items
	function __get($key) {
		if(isset($this->$key)) {
			return $this->$key;
		} else {
			$wf = _workframe();
			if(isset($wf->LOADED[$key])) {
				return $wf->LOADED[$key];
			}
		}
		return NULL;
	}

}
