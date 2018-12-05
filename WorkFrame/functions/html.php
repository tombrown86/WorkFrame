<?php
function h($s) {
	return htmlspecialchars($s);
}
function script_tags($paths) {
	$html = '';
	foreach((array)$paths as $path) {
		$html .= '<script type="text/javascript" src="'.h($path).'"></script>'."\n";
	}
	return $html;
}
function stylesheet_tags($paths) {
	$html = '';
	foreach((array)$paths as $path) {
		$html .= '<link rel="stylesheet" href="'.h($path).'"/>'."\n";
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

