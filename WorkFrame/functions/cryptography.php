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

function openssl_encrypt($data, $encryption_key, $alg='aes-256-cbc') {
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($alg));
	$encrypted = openssl_encrypt($data, $alg, $encryption_key, 0, $iv);
	return $encrypted . ':' . $iv;
}

function openssl_decrypt($encrypted, $encryption_key, $alg='aes-256-cbc') {
	$parts = explode(':', $encrypted);
	$decrypted = openssl_decrypt($parts[0], $alg, $encryption_key, 0, $parts[1]);
	return $decrypted;
}
