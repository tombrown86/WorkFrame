<?php

namespace ExampleApp\Request_handlers;

class Site_base extends \WorkFrame\Request_handler {

	protected $user;

	function pre_action_hook() {
		parent::pre_action_hook();

		// Add standard resources for this site
		$this->add_scripts(['bootstrap.js', 'bootstrap-dialog.js', 'main.js', '../jquery-ui/jquery-ui.js'], TRUE, 'site_standard');
		$this->add_stylesheets(['../fontello/css/fontello.css', 'bootstrap.css', 'bootstrap-dialog.css', 'style.css', '../jquery-ui/jquery-ui.css', '../jquery-ui/jquery-ui.structure.css', '../jquery-ui/jquery-ui.theme.css']);
	}

}
