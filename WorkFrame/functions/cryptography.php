<?php

function hash_data($secret, $data, $mechanism = 'md5') {
	ksort($data);
	$s = $secret;
	if($mechanism == 'md5') {
		foreach($data as $k=>$v) {
			$s .= md5($k) . md5($v);
		}
		return md5($s);
	}
	throw new \WorkFrame\Exceptions\Invalid_hashing_mechanism_exception('Mechanism: '.$mechanism);
}

function check_hash_data($hash, $secret, $data, $mechanism = 'md5') {
	return $hash === hash_data($secret, $data, $mechanism);
}
