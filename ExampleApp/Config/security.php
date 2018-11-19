<?php

return [
	'use_workframe_security_library' => FALSE,
  
  # If enabled, access _GET _POST and _REQUEST  with $this->GET($key), and $this->GET($key, TRUE) for the original uncleaned value..
  # To get the entire cleaned _GET array, ommit the first $key parameter or pass in NULL.
  # Same goes for _POST and _REQUEST
	'xss_filter' => FALSE, 
];
