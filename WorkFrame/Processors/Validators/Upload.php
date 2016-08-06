<?php
namespace WorkFrame\Processor\Validators;

class Upload {
	private $maxsize = null;
	private $extensions = null;
	
	function __construct() {}
	function setMaxSize($maxsize) {
		$this->maxsize = $maxsize;
	}
	function setExtensions($extensions) {
		$this->extensions = $extensions;
	}
	function validate_upload($field, $value, $scenario)  {
		/*if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return TRUE;
		} else {
			return [
				'field' => $field,
				'is_error' => TRUE,
				'error_message' => 'Invalid email address',
			];
		}*/
	}
}
