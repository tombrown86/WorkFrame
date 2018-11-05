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