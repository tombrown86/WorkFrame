<?php
namespace WorkFrame\Exceptions;
class Request_handler_rewrite_exception extends \WorkFrame\Exceptions\WorkFrame_exception {
	private $rewrite_request_handler_class;
	private $rewrite_request_handler_action;
	private $rewrite_request_routes;
	function __construct($rewrite_request_handler_class, $rewrite_request_handler_action, $rewrite_request_routes=null) {
		$this->rewrite_request_handler_class = $rewrite_request_handler_class;
		$this->rewrite_request_handler_action = $rewrite_request_handler_action;
		$this->rewrite_request_routes = $rewrite_request_routes;
	}
	function get_rewrite_request_handler_class(){
		return $this->rewrite_request_handler_class;
	}
	function get_rewrite_request_handler_action(){
		return $this->rewrite_request_handler_action;
	}
	function get_rewrite_request_routes(){
		return $this->rewrite_request_routes;
	}
}
