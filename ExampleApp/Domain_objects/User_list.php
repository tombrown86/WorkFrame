<?php
namespace ExampleApp\Domain_objects;
class User_list {
	private $user_type;
	private $user_sub_type;
	private $activated;
	private $email;
	private $near_by_point_lat;
	private $near_by_point_lon;
	private $near_by_point_max_distance;
	private $ids;
	private $practitioner_visible_to_practice_practice_user_ids;
	private $practitioner_visible_to_practice_practitioner_user_ids;
	private $practice_internal_list_practitioner_practice_user_ids;
	private $practice_internal_list_practitioner_practitioner_user_ids;
	private $users;
		
	function get_users() {
		return $this->users;
	}
	function set_users($users) {
		$this->users = $users;
	}
	function get_activated() {
		return $this->activated;
	}
	function set_activated($activated) {
		$this->activated = $activated;
	}
	function get_email() {
		return $this->email;
	}
	function set_email($email) {
		$this->email = $email;
	}

	function get_near_by_point_lat() {
		return $this->near_by_point_lat;
	}
	function set_near_by_point_lat($near_by_point_lat) {
		$this->near_by_point_lat = $near_by_point_lat;
	}

	function get_near_by_point_lon() {
		return $this->near_by_point_lon;
	}
	function set_near_by_point_lon($near_by_point_lon) {
		$this->near_by_point_lon = $near_by_point_lon;
	}

	function get_near_by_point_max_distance() {
		return $this->near_by_point_max_distance;
	}
	function set_near_by_point_max_distance($near_by_point_max_distance) {
		$this->near_by_point_max_distance = $near_by_point_max_distance;
	}

	function get_practice_internal_list_practitioner_practice_user_ids() {
		return $this->practice_internal_list_practitioner_practice_user_ids;
	}
	function set_practice_internal_list_practitioner_practice_user_ids($practice_internal_list_practitioner_practice_user_ids) {
		$this->practice_internal_list_practitioner_practice_user_ids = $practice_internal_list_practitioner_practice_user_ids;
	}

	function get_practice_internal_list_practitioner_practitioner_user_ids() {
		return $this->practice_internal_list_practitioner_practitioner_user_ids;
	}
	function set_practice_internal_list_practitioner_practitioner_user_ids($practice_internal_list_practitioner_practitioner_user_ids) {
		$this->practice_internal_list_practitioner_practitioner_user_ids = $practice_internal_list_practitioner_practitioner_user_ids;
	}

	function get_practitioner_visible_to_practice_practice_user_ids() {
		return $this->practitioner_visible_to_practice_practice_user_ids;
	}
	function set_practitioner_visible_to_practice_practice_user_ids($practitioner_visible_to_practice_practice_user_ids) {
		$this->practitioner_visible_to_practice_practice_user_ids = $practitioner_visible_to_practice_practice_user_ids;
	}

	function get_practitioner_visible_to_practice_practitioner_user_ids() {
		return $this->practitioner_visible_to_practice_practitioner_user_ids;
	}
	function set_practitioner_visible_to_practice_practitioner_user_ids($practitioner_visible_to_practice_practitioner_user_ids) {
		$this->practitioner_visible_to_practice_practitioner_user_ids = $practitioner_visible_to_practice_practitioner_user_ids;
	}

	function get_ids() {
		return $this->ids;
	}
	function set_ids($ids) {
		$this->ids = $ids;
	}

	function get_user_sub_type() {
		return $this->user_sub_type;
	}
	function set_user_sub_type($user_sub_type) {
		$this->user_sub_type = $user_sub_type;
	}

	function get_user_type() {
		return $this->user_type;
	}
	function set_user_type($user_type) {
		$this->user_type = $user_type;
	}

}
