<?php
namespace ExampleApp\Domain_objects;
class Current_user extends \WorkFrame\Domain_object {
	protected $is_authenticated = FALSE;
	protected $user;
	function __construct() {
		$this->user = new \ExampleApp\Domain_objects\User();
	}
	function set_is_authenticated($is_authenticated) {
		$this->is_authenticated = $is_authenticated;
	}
	function is_authenticated() {
		return $this->is_authenticated;
	}
	function set_user($user) {
		$this->user = $user;
	}
	function get_user() {
		return $this->user;
	}
	function get_user_type() {
		return isset($this->user) ? $this->user->get_user_type() : 'guest';
	}
	function get_user_sub_type() {
		return isset($this->user) ? $this->user->get_user_sub_type() : 'guest';
	}
	function get_user_id(){
		return $this->get_id();
	}
	function get_id(){
		return $this->get_user()->get_id();
	}
}
