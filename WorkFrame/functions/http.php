<?php

/**
 * Stupid function name, I blame brexit
 * @param string location
 */
function redirexit($location) {
	header('Location: '.$location);
	exit;
}

/**
 * Add parameter to URL
 * @param string $url
 * @param string $key
 * @param string $value
 * @return string result URL
 */
function set_url_param($url, $key, $value = null) {
	$query = parse_url($url, PHP_URL_QUERY);
	if ($query) {
		$query_params = null;
		parse_str($query, $query_params);
		$query_params[$key] = $value;
		$url = str_replace("?$query", '?' . http_build_query($query_params), $url);
	} else {
		$url .= '?' . urlencode($key) . '=' . urlencode($value);
	}
	return $url;
}
