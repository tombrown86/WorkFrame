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
	$output_name or $output_name = $files[0];

	// sort web and system paths to public dir
	$public_dir_name = $filetype == 'js' ? 'scripts' : 'stylesheets';
	$full_public_path = APP_PUBLIC_PATH . "/" . $public_dir_name . "/";
	$workframe_js_path = APP_PATH . "/js/";

	$safe_name = preg_replace("/[^a-z0-9\._-]*/i", '', $output_name . '__' . APP_CODENAME . '_v' . APP_BUILD) . '.min.' . $filetype;
	$safe_name = ltrim($safe_name, ' .');// files starting with '..' seem to be forbidden (nginx)
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
			if ($dev_environment_and_min_file_out_of_date = filemtime($path) > filemtime($full_public_path . $min_file)) {
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
			$code = $filetype == 'js' ? JSMin::minify($code) : CSSCompressor::minify($code);
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

	$files = array(WWW_PUBLIC_PATH . '/scripts/' . $min_file);

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
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @copyright 2012 Adam Goforth <aag@adamgoforth.com> (Updates)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.2 (2012-05-01)
 * @link https://github.com/rgrove/jsmin-php
 */
class JSMin {

	const ORD_LF = 10;
	const ORD_SPACE = 32;
	const ACTION_KEEP_A = 1;
	const ACTION_DELETE_A = 2;
	const ACTION_DELETE_A_B = 3;

	protected $a = '';
	protected $b = '';
	protected $input = '';
	protected $inputIndex = 0;
	protected $inputLength = 0;
	protected $lookAhead = null;
	protected $output = '';

	// -- Public Static Methods --------------------------------------------------

	/**
	 * Minify Javascript
	 *
	 * @uses __construct()
	 * @uses min()
	 * @param string $js Javascript to be minified
	 * @return string
	 */
	public static function minify($js) {
		$jsmin = new JSMin($js);
		return $jsmin->min();
	}

	// -- Public Instance Methods ------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param string $input Javascript to be minified
	 */
	public function __construct($input = '') {
		$this->input = str_replace("\r\n", "\n", $input);
		$this->inputLength = strlen($this->input);
	}

	// -- Protected Instance Methods ---------------------------------------------

	/**
	 * Action -- do something! What to do is determined by the $command argument.
	 *
	 * action treats a string as a single character. Wow!
	 * action recognizes a regular expression if it is preceded by ( or , or =.
	 *
	 * @uses next()
	 * @uses get()
	 * @throws JSMinException If parser errors are found:
	 * - Unterminated string literal
	 * - Unterminated regular expression set in regex literal
	 * - Unterminated regular expression literal
	 * @param int $command One of class constants:
	 * ACTION_KEEP_A Output A. Copy B to A. Get the next B.
	 * ACTION_DELETE_A Copy B to A. Get the next B. (Delete A).
	 * ACTION_DELETE_A_B Get the next B. (Delete B).
	 */
	protected function action($command) {
		switch ($command) {
			case self::ACTION_KEEP_A:
				$this->output .= $this->a;

			case self::ACTION_DELETE_A:
				$this->a = $this->b;

				if ($this->a === "'" || $this->a === '"') {
					for (;;) {
						$this->output .= $this->a;
						$this->a = $this->get();

						if ($this->a === $this->b) {
							break;
						}

						if (ord($this->a) <= self::ORD_LF) {
							throw new JSMinException('Unterminated string literal.');
						}

						if ($this->a === '\\') {
							$this->output .= $this->a;
							$this->a = $this->get();
						}
					}
				}

			case self::ACTION_DELETE_A_B:
				$this->b = $this->next();

				if ($this->b === '/' && (
						$this->a === '(' || $this->a === ',' || $this->a === '=' ||
						$this->a === ':' || $this->a === '[' || $this->a === '!' ||
						$this->a === '&' || $this->a === '|' || $this->a === '?' ||
						$this->a === '{' || $this->a === '}' || $this->a === ';' ||
						$this->a === "\n" )) {

					$this->output .= $this->a . $this->b;

					for (;;) {
						$this->a = $this->get();

						if ($this->a === '[') {
							/*
							  inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
							  return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
							 */
							for (;;) {
								$this->output .= $this->a;
								$this->a = $this->get();

								if ($this->a === ']') {
									break;
								} elseif ($this->a === '\\') {
									$this->output .= $this->a;
									$this->a = $this->get();
								} elseif (ord($this->a) <= self::ORD_LF) {
									throw new JSMinException('Unterminated regular expression set in regex literal.');
								}
							}
						} elseif ($this->a === '/') {
							break;
						} elseif ($this->a === '\\') {
							$this->output .= $this->a;
							$this->a = $this->get();
						} elseif (ord($this->a) <= self::ORD_LF) {
							throw new JSMinException('Unterminated regular expression literal.');
						}

						$this->output .= $this->a;
					}

					$this->b = $this->next();
				}
		}
	}

	/**
	 * Get next char. Convert ctrl char to space.
	 *
	 * @return string|null
	 */
	protected function get() {
		$c = $this->lookAhead;
		$this->lookAhead = null;

		if ($c === null) {
			if ($this->inputIndex < $this->inputLength) {
				$c = substr($this->input, $this->inputIndex, 1);
				$this->inputIndex += 1;
			} else {
				$c = null;
			}
		}

		if ($c === "\r") {
			return "\n";
		}

		if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
			return $c;
		}

		return ' ';
	}

	/**
	 * Is $c a letter, digit, underscore, dollar sign, or non-ASCII character.
	 *
	 * @return bool
	 */
	protected function isAlphaNum($c) {
		return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
	}

	/**
	 * Perform minification, return result
	 *
	 * @uses action()
	 * @uses isAlphaNum()
	 * @uses get()
	 * @uses peek()
	 * @return string
	 */
	protected function min() {
		if (0 == strncmp($this->peek(), "\xef", 1)) {
			$this->get();
			$this->get();
			$this->get();
		}

		$this->a = "\n";
		$this->action(self::ACTION_DELETE_A_B);

		while ($this->a !== null) {
			switch ($this->a) {
				case ' ':
					if ($this->isAlphaNum($this->b)) {
						$this->action(self::ACTION_KEEP_A);
					} else {
						$this->action(self::ACTION_DELETE_A);
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
							$this->action(self::ACTION_KEEP_A);
							break;

						case ' ':
							$this->action(self::ACTION_DELETE_A_B);
							break;

						default:
							if ($this->isAlphaNum($this->b)) {
								$this->action(self::ACTION_KEEP_A);
							} else {
								$this->action(self::ACTION_DELETE_A);
							}
					}
					break;

				default:
					switch ($this->b) {
						case ' ':
							if ($this->isAlphaNum($this->a)) {
								$this->action(self::ACTION_KEEP_A);
								break;
							}

							$this->action(self::ACTION_DELETE_A_B);
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
									$this->action(self::ACTION_KEEP_A);
									break;

								default:
									if ($this->isAlphaNum($this->a)) {
										$this->action(self::ACTION_KEEP_A);
									} else {
										$this->action(self::ACTION_DELETE_A_B);
									}
							}
							break;

						default:
							$this->action(self::ACTION_KEEP_A);
							break;
					}
			}
		}

		return $this->output;
	}

	/**
	 * Get the next character, skipping over comments. peek() is used to see
	 * if a '/' is followed by a '/' or '*'.
	 *
	 * @uses get()
	 * @uses peek()
	 * @throws JSMinException On unterminated comment.
	 * @return string
	 */
	protected function next() {
		$c = $this->get();

		if ($c === '/') {
			switch ($this->peek()) {
				case '/':
					for (;;) {
						$c = $this->get();

						if (ord($c) <= self::ORD_LF) {
							return $c;
						}
					}

				case '*':
					$this->get();

					for (;;) {
						switch ($this->get()) {
							case '*':
								if ($this->peek() === '/') {
									$this->get();
									return ' ';
								}
								break;

							case null:
								throw new JSMinException('Unterminated comment.');
						}
					}

				default:
					return $c;
			}
		}

		return $c;
	}

	/**
	 * Get next char. If is ctrl character, translate to a space or newline.
	 *
	 * @uses get()
	 * @return string|null
	 */
	protected function peek() {
		$this->lookAhead = $this->get();
		return $this->lookAhead;
	}

}

// -- Exceptions ---------------------------------------------------------------
class JSMinException extends Exception {
	
}

/**
 * This is a port of the CSS Compressor contained in YUI Compressor
 * The original license is below
 *
 * Port by Dave T. Johnson <dave@dtjohnson.net>
 *
 * Usage: $minified = CSSCompressor::minify($source);
 *
 * *****************************************************************
 *
 * YUI Compressor
 * Author: Julien Lecomte <jlecomte@yahoo-inc.com>
 * Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 * Code licensed under the BSD License:
 *     http://developer.yahoo.net/yui/license.txt
 *
 * This code is a port of Isaac Schlueter's cssmin utility.
 */
class CSSCompressor {

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
		$source = preg_replace_callback('~(^|\})(([^\{:])+:)+([^\{]*\{)~', create_function('$matches', '
			return str_replace(":", "___PSEUDOCLASSCOLON___", $matches[0]);
		'), $source);
		$source = preg_replace('~\s+([!{};:>+\(\)\],])~', '$1', $source);
		$source = str_replace('___PSEUDOCLASSCOLON___', ':', $source);

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
		$source = preg_replace_callback('~rgb\s*\(\s*([0-9,\s]+)\s*\)~', create_function('$matches', '
				$colors = explode(",", $matches[1]);
				$hexcolor = "#";
				foreach ($colors as $color) {
					$color = (int)$color;
					if ($color < 16) $hexcolor .= "0";
					$hexcolor .= dechex($color);
				}
				return $hexcolor;
		'), $source);

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
