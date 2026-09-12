<?php

/**
 * To give us minified JS or CSS.
 * 
 * Assuming minify is turned on in config and there is no $_GET['dontminify']:
 * This will minify the given JS or CSS files and write to a .min file if:
 * 	- The minified file doesn't already exist 
 * 	- $CI->input->get('minify') is set
 * 
 * Resulting file(s) either printed as tags ($print_tags param) or returned in an array
 * 
 * @param array|string array of filenames or single filename
 * @param string label for minified file
 * @param string filetype, js (default) or css
 * @param bool return src array or print script/link tags (default)
 * @return array|void array of js/css file src(s) or nothing if $print_tags
 */
function minify($files, $output_name = null, $filetype = 'js', $print_tags = true) {
	// sort out params
	is_array($files) or $files = array($files);
	$output_name or $output_name = 'wfmin_'.$files[0];

	// sort web and system paths to public dir
	$public_dir_name = $filetype == 'js' ? 'scripts' : 'stylesheets';
	$full_public_path = APP_PUBLIC_PATH . "/" . $public_dir_name . "/";
	$workframe_js_path = APP_PATH . "/js/";

	$safe_name = preg_replace("/[^a-z0-9\._-]*/i", '', $output_name . '__' . APP_CODENAME . '_v' . APP_BUILD) . '.min.' . $filetype;
	$min_file = 'min/' . $safe_name;
	$min_file_exists = file_exists($full_public_path . $min_file);

	$dev_environment_and_min_file_out_of_date = FALSE;

	// Validate + get full file paths,.. remove any that aren't found
	foreach ($files as $k => $file) {
		$path = (strpos($file, '/') === 0) ? $file : $full_public_path . preg_replace('/\?.*/', '', $file);

		if (!is_readable($path)) {
			log_message(WF_LOG_LEVEL_WARNING, 'JS file not found or not readable: ' . $path);
			unset($files[$k]);
		} else {
			$files[$k] = $path;
		}
	}

	// If dev environment and any min files are out of date, reminify
	if ($min_file_exists && WORKFRAME_ENVIRONMENT == 'DEV') {
		foreach ($files as $k => $file) {
			if ($dev_environment_and_min_file_out_of_date = filemtime($file) > filemtime($full_public_path . $min_file)) {
				break;
			}
		}
	}

	if (!$min_file_exists || (isset($_GET['minify']) && $_GET['minify']) || (isset($GLOBALS['_ORIGINAL_GET']['minify']) && $GLOBALS['_ORIGINAL_GET']['minify']) || $dev_environment_and_min_file_out_of_date) {
		log_message('info', "Minifying $filetype code for: $public_dir_name/$min_file with files: " . print_r($files, true));

		// get code from all files and minify
		// MINIFY FILES INDIVIDUALLY >
//			$code = '';
//			foreach ($files as $k=>$file)
//			{
//				$file_code = file_get_contents($full_public_path . preg_replace(array('/\?.*/', '/^DONTMINIFY\:/'), '', $file));
//				if(strpos($file, 'DONTMINIFY:') === 0)
//					$code .= "\n$file_code";
//				else
//					$code .= "\n" .($filetype == 'js' ? JSMin::minify($file_code) : CSSCompressor::minify($file_code));
//			}
		// OR DO THEM ALL IN ONE GO:
		$code = '';
		foreach ($files as $k => $file) {
			$code .= file_get_contents($file) . ($filetype == 'js' ? "\n;\n" : "\n\n");
		}
 
		if((isset($_GET['dontminify']) && $_GET['dontminify']) || (isset($GLOBALS['_ORIGINAL_GET']['dontminify']) && $GLOBALS['_ORIGINAL_GET']['dontminify'])) {
			$code = $filetype == 'js' ? $code : $code;
		} else {
			$code = $filetype == 'js' ? JShrink::minify($code) : CSSMinifier::minify($code);
		}

		if (($f = fopen($full_public_path . $min_file, 'w')) && fwrite($f, trim($code, "\n")) > 0) {
			//  die($full_public_path . $min_file);
			fclose($f);
			log_message(WF_LOG_LEVEL_INFO, "Written minified $filetype code to: $full_public_path$min_file");
		} else {
			log_message(WF_LOG_LEVEL_ERROR, "Couldn't write minified $filetype code to: $full_public_path$min_file.. Check perms?");
			throw new \WorkFrame\Exceptions\Cant_write_minify_file_exception();
		}

//			// Cleanup? rm any old versions of the min file we have generated
//			array_map('unlink', glob($full_path.'min/'.$output_name.'_v*_min.'.$mode));
	}

	$web_dir = $filetype == 'js' ? '/scripts/' : '/stylesheets/';
	$files = array(WWW_PUBLIC_PATH . $web_dir . $min_file);

	if (!$print_tags)
		return $files;

	foreach ($files as $file)
		echo $filetype == 'js' ? '
	<script type="text/javascript" src="' . $http_public_path . htmlspecialchars($file) . '"></script>' : '
	<link rel="stylesheet" href="' . $http_public_path . htmlspecialchars($file) . '" type="text/css" />';
}

/**
 * Call this when you just want to minify single files and return the source path
 *
 * @param string path to original file
 * @param string filetype, js|css
 * @return string source path 
 */
function minifySrc($file, $filetype = 'js') {
	$arr = minify($file, null, $filetype, false);
	return array_pop($arr);
}

/**
 * JShrink - PHP minifier for JavaScript that supports ES6+ including template literals.
 *
 * @package JShrink
 * @author Robert Hafner <tedivm@tedivm.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/tedious/JShrink
 * @version 1.7.0
 */
class JShrink {

	/**
	 * Minify Javascript
	 *
	 * @param string $js Javascript to be minified
	 * @param array $options Options for minification
	 * @return string
	 */
	public static function minify($js, $options = array()) {
		$instance = new static();
		return $instance->performMinify($js, $options);
	}

	protected function performMinify($js, $options) {
		$js = str_replace("\r\n", "\n", $js);
		$js = str_replace('/**/', '', $js);
		
		$flags = array();
		$flags['cleanup'] = !isset($options['flaggedComments']) || $options['flaggedComments'];
		
		$jshrink = new JShrinkParser();
		return $jshrink->parse($js, $flags);
	}
}

class JShrinkParser {
	
	protected $a = '';
	protected $b = '';
	protected $input = '';
	protected $inputIndex = 0;
	protected $inputLength = 0;
	protected $lookAhead = null;
	protected $output = '';
	protected $lastCharType = 'other';

	public function parse($js, $flags = array()) {
		$this->input = $js;
		$this->inputLength = strlen($js);
		$this->inputIndex = 0;
		$this->output = '';
		$this->a = '';
		$this->b = '';
		$this->lookAhead = null;
		
		$this->a = "\n";
		$this->action(3);

		while ($this->a !== false && $this->a !== null) {
			switch ($this->a) {
				case ' ':
					if ($this->isAlphaNum($this->b)) {
						$this->action(1);
					} else {
						$this->action(2);
					}
					break;

				case "\n":
					switch ($this->b) {
						case '{':
						case '[':
						case '(':
						case '+':
						case '-':
						case '!':
						case '~':
							$this->action(1);
							break;

						case ' ':
							$this->action(3);
							break;

						default:
							if ($this->isAlphaNum($this->b)) {
								$this->action(1);
							} else {
								$this->action(2);
							}
					}
					break;

				default:
					switch ($this->b) {
						case ' ':
							if ($this->isAlphaNum($this->a)) {
								$this->action(1);
								break;
							}

							$this->action(3);
							break;

						case "\n":
							switch ($this->a) {
								case '}':
								case ']':
								case ')':
								case '+':
								case '-':
								case '"':
								case "'":
								case '`':
									$this->action(1);
									break;

								default:
									if ($this->isAlphaNum($this->a)) {
										$this->action(1);
									} else {
										$this->action(3);
									}
							}
							break;

						default:
							$this->action(1);
							break;
					}
			}
		}

		return $this->output;
	}

	protected function action($command) {
		switch ($command) {
			case 1: // Output A, copy B to A, get next B
				$this->output .= $this->a;
				// fall through

			case 2: // Copy B to A, get next B (delete A)
				$this->a = $this->b;

				// Handle string literals (single and double quotes)
				if ($this->a === "'" || $this->a === '"') {
					$quote = $this->a;
					$this->output .= $this->a;
					
					while (true) {
						$this->a = $this->get();
						
						if ($this->a === $quote) {
							break;
						}
						
						if ($this->a === null || $this->a === "\n") {
							throw new JShrinkException('Unterminated string literal.');
						}
						
						$this->output .= $this->a;
						
						if ($this->a === '\\') {
							$this->a = $this->get();
							if ($this->a === null) {
								throw new JShrinkException('Unterminated string literal.');
							}
							$this->output .= $this->a;
						}
					}
				}

				// Handle template literals (backticks)
				if ($this->a === '`') {
					$this->output .= $this->a;
					
					while (true) {
						$this->a = $this->get();
						
						if ($this->a === '`') {
							break;
						}
						
						if ($this->a === null) {
							throw new JShrinkException('Unterminated template literal.');
						}
						
						$this->output .= $this->a;
						
						if ($this->a === '\\') {
							$this->a = $this->get();
							if ($this->a === null) {
								throw new JShrinkException('Unterminated template literal.');
							}
							$this->output .= $this->a;
						}
					}
				}
				// fall through

			case 3: // Get next B (delete B)
				$this->b = $this->next();

				// Handle regex literals
				if ($this->b === '/' && $this->isRegexStart()) {
					$this->output .= $this->a . $this->b;

					while (true) {
						$this->a = $this->get();

						if ($this->a === '/') {
							break;
						}
						
						if ($this->a === '\\') {
							$this->output .= $this->a;
							$this->a = $this->get();
						} elseif ($this->a === '[') {
							$this->output .= $this->a;
							while (true) {
								$this->a = $this->get();
								if ($this->a === ']') {
									break;
								} elseif ($this->a === '\\') {
									$this->output .= $this->a;
									$this->a = $this->get();
								} elseif ($this->a === null || $this->a === "\n") {
									throw new JShrinkException('Unterminated regex character class.');
								}
								$this->output .= $this->a;
							}
						} elseif ($this->a === null || $this->a === "\n") {
							throw new JShrinkException('Unterminated regex literal.');
						}

						$this->output .= $this->a;
					}

					$this->b = $this->next();
				}
				break;
		}
	}

	protected function isRegexStart() {
		return in_array($this->a, array('(', ',', '=', ':', '[', '!', '&', '|', '?', '{', '}', ';', "\n", '+', '-', '*', '/', '%', '<', '>'));
	}

	protected function get() {
		$c = $this->lookAhead;
		$this->lookAhead = null;

		if ($c === null) {
			if ($this->inputIndex < $this->inputLength) {
				$c = $this->input[$this->inputIndex];
				$this->inputIndex++;
			} else {
				return null;
			}
		}

		if ($c === "\r" || $c === "\n") {
			return "\n";
		}

		if (ord($c) < 32) {
			return ' ';
		}

		return $c;
	}

	protected function isAlphaNum($c) {
		return ($c !== null && ($c === '_' || $c === '$' || ctype_alnum($c) || ord($c) > 126));
	}

	protected function next() {
		$c = $this->get();

		if ($c === '/') {
			switch ($this->peek()) {
				case '/':
					// Single line comment
					while (true) {
						$c = $this->get();
						if ($c === "\n" || $c === null) {
							return $c;
						}
					}

				case '*':
					// Multi-line comment
					$this->get();
					while (true) {
						switch ($this->get()) {
							case '*':
								if ($this->peek() === '/') {
									$this->get();
									return ' ';
								}
								break;

							case null:
								throw new JShrinkException('Unterminated comment.');
						}
					}

				default:
					return $c;
			}
		}

		return $c;
	}

	protected function peek() {
		$this->lookAhead = $this->get();
		return $this->lookAhead;
	}
}

// -- Exceptions ---------------------------------------------------------------
class JShrinkException extends Exception {
	
}

/**
 * Modern CSS Minifier - Updated version without deprecated functions
 *
 * Based on YUI Compressor CSS minification
 * Author: Julien Lecomte <jlecomte@yahoo-inc.com>
 * Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 * Code licensed under the BSD License:
 *     http://developer.yahoo.net/yui/license.txt
 *
 * Updated to remove deprecated create_function calls
 */
class CSSMinifier {

	public static function minify($source, $linebreakpos = 0) {
		// Remove all comment blocks...
		$startIndex = 0;
		$iemac = false;
		$preserve = false;
		while ($startIndex < strlen($source)) {
			$startIndex = strpos($source, '/*', $startIndex + 2);
			if ($startIndex === false)
				break;
			$preserve = strlen($source) > $startIndex + 2 && $source[$startIndex + 2] == '!';
			$endIndex = strpos($source, '*/', $startIndex + 2);

			if ($endIndex === false) {
				if (!$preserve) {
					$source = substr($source, 0, $startIndex);
				}
			} elseif ($endIndex >= $startIndex + 2) {
				if ($source[$endIndex - 1] == '\\') {
					// Looks like a comment to hide rules from IE Mac.
					// Leave this comment, and the following one, alone...
					$startIndex = $endIndex + 2;
					$iemac = true;
				} elseif ($iemac) {
					$startIndex = $endIndex + 2;
					$iemac = false;
				} elseif (!$preserve) {
					$source = substr($source, 0, $startIndex) . substr($source, $endIndex + 2);
				} else {
					//Strip !
					$source = substr($source, 0, $startIndex + 2) . substr($source, $startIndex + 3);
				}
			}
		}

		// Normalize all whitespace strings to single spaces. Easier to work with that way.
		$source = preg_replace('/\s+/', ' ', $source);

		// Replace the pseudo class for the Box Model Hack
		$source = preg_replace('~"\\\\"}\\\\""~', '___PSEUDOCLASSBMH___', $source);

		// Remove the spaces before the things that should not have spaces before them.
		// But, be careful not to turn "p :link {...}" into "p:link{...}"
		// Swap out any pseudo-class colons with the token, and then swap back.
		$source = preg_replace_callback('~(^|\})(([^\{:])+:)+([^\{]*\{)~', function($matches) {
			return str_replace(":", "___PSEUDOCLASSCOLON___", $matches[0]);
		}, $source);
		// Do not strip space before '(': that turns "and (max-width: …)" into
		// invalid "and(max-width: …)", which browsers treat as @media not all.
		$source = preg_replace('~\s+([!{};:>+\)\],])~', '$1', $source);
		$source = str_replace('___PSEUDOCLASSCOLON___', ':', $source);
		// Any remaining media-query "and("/"or(" (not :not()) need the space back.
		$source = preg_replace('/(?<!:)\b(and|or|only)\(/i', '$1 (', $source);

		// Remove the spaces after the things that should not have spaces after them.
		$source = preg_replace('~([!{}:;>+\(\[,])\s+~', '$1', $source);

		// Add the semicolon where it's missing.
		$source = preg_replace('~([^;\}])}~', '$1;}', $source);

		// Replace 0(px,em,%) with 0.
		$source = preg_replace('~([\s:])(0)(px|em|%|in|cm|mm|pc|pt|ex)~', '$1$2', $source);

		// Replace 0 0 0 0; with 0.
		$source = preg_replace('~:0(\s0){1,3};~', ':0;', $source);

		// Replace background-position:0; with background-position:0 0;
		$source = str_replace('background-position:0;', 'background-position:0 0;', $source);

		// Replace 0.6 to .6, but only when preceded by : or a white-space
		$source = preg_replace('~(:|\s)0+\.(\d+)~', '$1.$2', $source);

		// Shorten colors from rgb(51,102,153) to #336699
		// This makes it more likely that it'll get further compressed in the next step.
		$source = preg_replace_callback('~rgb\s*\(\s*([0-9,\s]+)\s*\)~', function($matches) {
			$colors = explode(",", $matches[1]);
			$hexcolor = "#";
			foreach ($colors as $color) {
				$color = (int)$color;
				if ($color < 16) $hexcolor .= "0";
				$hexcolor .= dechex($color);
			}
			return $hexcolor;
		}, $source);

		// Shorten colors from #AABBCC to #ABC. Note that we want to make sure
		// the color is not preceded by either ", " or =. Indeed, the property
		//     filter: chroma(color="#FFFFFF");
		// would become
		//     filter: chroma(color="#FFF");
		// which makes the filter break in IE.
		$source = preg_replace('~([^"\'=\s])(\s*)#([0-9a-fA-F])\3([0-9a-fA-F])\4([0-9a-fA-F])\5~', '$1$2#$3$4$5', $source);

		// Remove empty rules.
		$source = preg_replace('~[^\}]+\{;\}~', '', $source);

		if ($linebreakpos) {
			// Some source control tools don't like it when files containing lines longer
			// than, say 8000 characters, are checked in. The linebreak option is used in
			// that case to split long lines after a specific column.
			$i = 0;
			$linestartpos = 0;
			$temp = '';
			while ($i < strlen($source)) {
				$c = $source[$i++];
				if ($c == '}' && $i - $linestartpos > $linebreakpos) {
					$temp .= $c . "\n";
					$linestartpos = $i;
				} else {
					$temp .= $c;
				}
			}
			$source = $temp;
		}

		// Replace the pseudo class for the Box Model Hack
		$source = preg_replace('/___PSEUDOCLASSBMH___/', '"\\"}\\""', $source);

		// Replace multiple semi-colons in a row by a single one
		// See SF bug #1980989
		$source = preg_replace('/;;+/', ';', $source);

		// Trim the final string (for any leading or trailing white spaces)
		$source = trim($source);

		return $source;
	}

}


