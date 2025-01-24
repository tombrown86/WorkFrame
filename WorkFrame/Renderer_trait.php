<?php
/**
 * Request_handlers are the prime user of this, however - anything else can too if 
 * they need to render full pages or partials
 */
namespace WorkFrame;

trait Renderer_trait {

	private $view_vars = [];
	private $scripts = [];
	private $module_scripts = [];
	private $stylesheets = [];
	protected $page_title;

	function render($partial, $template = null) {
		functions('html');

		// Define variables for view
		$PAGE_TITLE = $this->get_title();
		extract($this->view_vars); // (Turn view vars array vals to vars.)

		ob_start();
		include(APP_PATH . '/html/partials/' . $partial);
		$ret_html = ob_get_clean();

		
		if (!is_null($template)) {
			$HTML = $unique_placeholder = uniqid('___HTML_PLACEHOLDER___');
			ob_start();
			include(APP_PATH . '/html/templates/' . $template);
			$template_html = ob_get_clean();
			$ret_html = str_replace($unique_placeholder, $ret_html, $template_html);
		}

		return $ret_html;
	}

	function add_view_var($k, $v) {
		$this->view_vars[$k] = $v;
	}

	function add_view_vars($data) {
		foreach ($data as $k => $v) {
			$this->add_view_var($k, $v);
		}
	}

	function add_script($script, $minify = TRUE, $minify_filename = null, $is_module = FALSE) {
		$this->add_scripts([$script], $minify, $minify_filename, $is_module);
	}

	/**
	 * Note: full paths only work with minify
	 * @param array scripts (filename in public/scripts or full path)
	 * @param bool minify 
	 * @param string desired minified file name
	 * @param bool script(s) are modules
	 */
	function add_scripts($scripts, $minify = TRUE, $minify_filename = null, $are_modules = FALSE) {
		/* check not already added? foreach($scripts as $k=>$script) {
		  if(in_array()) {
		  unset($scripts[$k]);
		  }
		  } */

		$attr = $are_modules ? 'module_scripts' : 'scripts';
		$dir = $are_modules ? 'scripts/modules' : 'scripts';
		if ($are_modules/*can't minify modules yet*/ || !$minify) {
			foreach ($scripts as $script) {
				if (strpos('/', $script) !== 0) {
					$this->$attr[] = WWW_PUBLIC_PATH . '/'.$dir.'/' . $script;
				} else {// Can't add full file path files without minify
					throw new Exceptions\Workframe_exception('Cannot add script by full file path unless it is to be minified');
				}
			}
			$this->$attr = array_unique($this->$attr);
		} else {
			functions('minify');
			$this->$attr = array_merge($this->$attr, minify($scripts, $minify_filename, 'js', FALSE));
		}
	}

	function add_foreign_script($script, $is_module = FALSE) {
		$this->add_foreign_scripts([$script], $is_module);
	}

	function add_foreign_scripts($scripts, $are_modules = FALSE) {
		$attr = $are_modules ? 'module_scripts' : 'scripts';
		foreach ($scripts as $script) {
			$this->$attr[] = $script;
		}
	}

	function add_stylesheet($stylesheet) {
		$this->add_stylesheets([$stylesheet]);
	}

	function add_stylesheets($stylesheets) {
//		if(!$minify) {
		foreach ($stylesheets as $stylesheet) {
			$this->stylesheets[] = WWW_PUBLIC_PATH . '/stylesheets/' . $stylesheet;
		}
		$this->stylesheets = array_unique($this->stylesheets);
//		} else {
//			functions('minify');
//			array_merge($this->scripts, minify($files, $minify_filename, 'js', FALSE));
//		}
	}

	function add_foreign_stylesheet($stylesheet) {
		$this->add_foreign_stylesheets([$stylesheet]);
	}

	function add_foreign_stylesheets($stylesheets) {
		foreach ($stylesheets as $stylesheet) {
			$this->stylesheets[] = $stylesheet;
		}
	}

	function set_page_title($title) {
		$this->page_title = $title;
	}

	function get_title() {
		return isset($this->page_title) ? $this->page_title : conf('app')['site_name'];
	}

}
