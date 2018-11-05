<?php
/**
 * Request_handlers are the prime user of this, however - anything else can too if 
 * they need to render full pages or partials
 */
namespace WorkFrame;

trait Renderer_trait {

	private $view_vars = [];
	private $scripts = [];
	private $stylesheets = [];
	protected $page_title;

	function render($partial, $template = null) {
		functions('html');

		// Define variables for view
		$PAGE_TITLE = $this->get_title();
		extract($this->view_vars); // (Turn view vars array vals to vars.)

		ob_start();
		include(APP_PATH . '/html/partials/' . $partial);
		$HTML = ob_get_clean();

		if (!is_null($template)) {
			ob_start();
			include(APP_PATH . '/html/templates/' . $template);
			$HTML = ob_get_clean();
			ob_get_clean();
		}

		return $HTML;
	}

	function add_view_var($k, $v) {
		$this->view_vars[$k] = $v;
	}

	function add_view_vars($data) {
		foreach ($data as $k => $v) {
			$this->add_view_var($k, $v);
		}
	}

	function add_script($script, $minify = TRUE, $minify_filename = null) {
		$this->add_scripts([$script], $minify, $minify_filename);
	}

	/**
	 * Note: full paths only work with minify
	 * @param array scripts (filename in public/scripts or full path)
	 * @param bool minify 
	 * @param string desired minified file name
	 */
	function add_scripts($scripts, $minify = TRUE, $minify_filename = null) {
		/* check not already added? foreach($scripts as $k=>$script) {
		  if(in_array()) {
		  unset($scripts[$k]);
		  }
		  } */

		if (!$minify) {
			foreach ($scripts as $script) {
				if (strpos('/', $script) !== 0) {
					$this->scripts[] = WWW_PUBLIC_PATH . '/scripts/' . $script;
				} else {// Can't add full file path files without minify
					throw new Exceptions\Workframe_exception('Cannot add script by full file path unless it is to be minified');
				}
			}
			$this->scripts = array_unique($this->scripts);
		} else {
			functions('minify');
			$this->scripts = array_merge($this->scripts, minify($scripts, $minify_filename, 'js', FALSE));
		}
	}

	function add_foreign_script($script) {
		$this->add_foreign_scripts([$script]);
	}

	function add_foreign_scripts($scripts) {
		foreach ($scripts as $script) {
			$this->scripts[] = $script;
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
