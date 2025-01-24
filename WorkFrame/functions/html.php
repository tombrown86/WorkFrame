<?php
function h($s) {
	return htmlspecialchars($s ?? '', ENT_QUOTES);
}
function script_tags($paths, $are_modules = FALSE) {
	$html = '';
	$app_build_get_var_str = defined('APP_BUILD') ? 'app_build='.urlencode(APP_BUILD) : '';
	foreach((array)$paths as $path) {	
		$html .= '<script type="'.($are_modules ? 'module' : 'text/javascript').'" src="'.h($path).(empty($app_build_get_var_str) ? '' : (strpos($path, '?')!==FALSE ? '&amp;'.$app_build_get_var_str : '?'.$app_build_get_var_str)).'"></script>'."\n";
	}
	return $html;
}
function stylesheet_tags($paths) {
	$html = '';
	$app_build_get_var_str = defined('APP_BUILD') ? 'app_build='.urlencode(APP_BUILD) : '';
	foreach((array)$paths as $path) {
		$html .= '<link rel="stylesheet" href="'.h($path).(empty($app_build_get_var_str) ? '' : (strpos($path, '?')!==FALSE ? '&amp;'.$app_build_get_var_str : '?'.$app_build_get_var_str)).'"/>'."\n";
	}
	return $html;
}
/**
 * Limits $str to max length of $chars.
 * Cuts off after last word and appends $end.
 *
 * @param string $str the string
 * @param int $chars max chars
 * @param string $end string to append to end
 * @return string truncated string 
 */
function truncate($str, $chars = 150, $end = '&hellip;')
{
        if(strlen($str) <= $chars ) return $str;

        $substr = substr($str, 0, $chars);
        preg_match("/^.*(\s).*$/", $substr, $last_space_char_matches); # Should give last space char as the only match
        return substr($str, 0, strrpos($substr, count($last_space_char_matches)==2 ? $last_space_char_matches[1] : ' ')) . $end;
}

