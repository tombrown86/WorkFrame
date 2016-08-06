<?php
namespace App\Request_handlers;

class Site_base extends \WorkFrame\Request_handler {
        function pre_action_hook() {
            parent::pre_action_hook();

            // Add standard resources for this site
            $this->add_scripts(['bootstrap.js', 'main.js']);
            $this->add_stylesheets(['bootstrap.css', 'style.css']);
        }
}
