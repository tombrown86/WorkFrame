<?php

namespace WorkFrame\Libraries;

class Invalid_input_exception extends \WorkFrame\Exceptions\WorkFrame_exception {
	
}

class Invalid_security_configuration_exception extends \WorkFrame\Exceptions\WorkFrame_exception {
	
}


class Security {

	private $conf;
//	private $protected_input_globals = ['_GET', '_POST', '_REQUEST', /* '_FILES', '_SERVER', '_SESSION', '_ENV', //'GLOBALS', 'HTTP_RAW_POST_DATA',
//						'system_folder', 'application_folder', 'BM', 'EXT', 'CFG', 'URI', 'RTR', 'OUT', 'IN'*/];
	
	function set_config($conf) {
		$this->conf = $conf;
	}

	function pre_router_securtiy_hook() {
		if ($this->get_conf_value('xss_filter')) {
		
			foreach (['_GET', '_POST', '_COOKIES', '_REQUEST'] as $inp_arr_name) {
				// Declare _ORIGINAL_blah (to store original value)
				// and _CLEAN_blah globals (to store safe)
				$ORIGINAL_inp_arr_name = '_ORIGINAL' . $inp_arr_name;
				$CLEAN_inp_arr_name = '_CLEAN' . $inp_arr_name;
				global $$ORIGINAL_inp_arr_name;
				global $$CLEAN_inp_arr_name;

				$$ORIGINAL_inp_arr_name = [];
				$$CLEAN_inp_arr_name = [];

				if(isset($GLOBALS[$inp_arr_name])) {
					foreach ($GLOBALS[$inp_arr_name] as $k => $v) {
						$GLOBALS[$ORIGINAL_inp_arr_name][$k] = $v;
						$GLOBALS[$CLEAN_inp_arr_name][$k] = $this->xss_filter_var($v);
						unset($GLOBALS[$inp_arr_name][$k]);
					}
				}
			}
			
			// Explicity destory these (as they seem to repopulate (at least _REQUEST does))
			unset($_GET);
			unset($_POST);
			unset($_COOKIES);
			unset($_REQUEST);
		}
	}

	function xss_filter_var($v) {
		if( is_array($v) ) {
			foreach( $v as $kk => $vv ) {
				$v[$kk] = xss_filter_var($vv);
			}
		}
		else {
			$v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK|FILTER_FLAG_NO_ENCODE_QUOTES);
		}
		return $v;
	}

	function get_conf_value($key) {
		if(!isset($this->conf[$key])) {
			throw new Invalid_security_configuration_exception;
		}
		return $this->conf[$key];
	}
}
