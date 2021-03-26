<?php

function hash_data($secret, $data, $mechanism = 'hmac_ripemd160') {
	ksort($data);
	
	if($mechanism == 'hmac_ripemd160') {
		$s = '';
		foreach($data as $k=>$v) {
			$s .= md5($k) . md5($v);// main purpose of this is to give consistent length to each k+v
		}	
		return hash_hmac('ripemd160', $s, $secret);
	} else if($mechanism == 'md5') {
		log_message(WF_LOG_LEVEL_INFO, "simple md5 hash method is fairly secure but no longer advised");
		$s = $secret;
		foreach($data as $k=>$v) {
			$s .= md5($k) . md5($v);
		}
		return md5($s);
	}
	throw new \WorkFrame\Exceptions\Invalid_hashing_mechanism_exception('Mechanism: '.$mechanism);
}

function check_hash_data($hash, $secret, $data, $mechanism = 'hmac_ripemd160') {
	return $hash === hash_data($secret, $data, $mechanism);
}

function encrypt_openssl($msg, $key, $alg = 'AES-256-CBC', $openssl_options = 0) {
	$iv_size = openssl_cipher_iv_length($alg);
	$iv = openssl_random_pseudo_bytes($iv_size);
	$encryptedMessage = openssl_encrypt($msg, $alg, $key, $openssl_options, $iv);
	return base64_encode($iv . $encryptedMessage);
}

function decrypt_openssl($data, $key, $alg = 'AES-256-CBC', $openssl_options = 0) {
	$data = base64_decode($data);
	$iv_size = openssl_cipher_iv_length($alg);
	$iv = substr($data, 0, $iv_size);
	$data = substr($data, $iv_size);
	return openssl_decrypt($data, $alg, $key, $openssl_options, $iv);
}

/**
 * It's often useful to strip nulls on data before hashing as quite often
 * the same array is used to build query strings. (NULL entries are stripped
 * when building query strings causing breakages when the hash built with
 * NULL values included doesn't match at the other end.)
 * @param array an array 
 * @param array the array without any of it's null entires
 */
function strip_nulls($array) {
	foreach($array as $k=>$v) {
		if(is_null($v)) {
			unset($array[$k]);
		}
	}
	return $array;
}
