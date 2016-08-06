<?php
/**
 * I'd expect in real life, you'd want some assistive library to interact with the DB.
 * 
 * I recommend: https://github.com/joshcam
 * I've even provided a wrapper class for the above (see \ExampleApp\MysqliDb_bridge_mapper
 */
namespace ExampleApp\Data_mappers;

class User_mapper extends \WorkFrame\Mysqli_data_mapper {

	function find($user) {
		$joins = $criteria = '';
		if ($user->get_id()) {
			$criteria = 'id=' . (int) $user->get_id();
		} else if (strlen($user->get_username())) {
			$criteria = 'username="' . $this->escape($user->get_username()) . '"';
		} else {
			log_message('INFO', 'No unique field for user select ' . __FILE__);
			return FALSE;
		}
		$user_data = $this->row_query('select * from user where ' . $criteria);

		if ($user_data) {
			$user_data ['how_hear_about_us_other'] = $user_data ['how_hear_about_us'];
			$user->from_assoc($user_data);
			return $user;
		}
		return FALSE;
	}

	function find_list($user_list) {
		$user_list->set_users([]);

		$criteria = '1 ';
		$joins = '';
		if (!is_null($user_list->get_user_type())) {
			$criteria .= ' and user_type="' . $this->escape($user_list->get_user_type()) . '" ';
		}
		if (!is_null($user_list->get_user_sub_type())) {
			$criteria .= ' and user_type="' . $this->escape($user_list->get_user_sub_type()) . '" ';
		}

		if (!is_null($user_list->get_activated())) {
			$criteria .= ' and activated="' . (int) ($user_list->get_activated()) . '" ';
		}

		if (!is_null($user_list->get_email())) {
			$criteria .= ' and email="' . $this->escape($user_list->get_email()) . '" ';
		}

		if (!is_null($user_list->get_ids())) {
			$ids = $user_list->get_ids();
			if (empty($ids)) {
				return $user_list;
			}
			$int_list = $this->comma_separated_ints($user_list->get_ids());
			$criteria .= ' and user_id in ("' . $int_list . ') ';
		}


		$rows = $this->rows_query('select * from user ' . $joins . ' where ' . $criteria);
		$list = [];
		foreach ($rows as $row) {
			$user = new \ExampleApp\User;
			$user->from_assoc($row);
			$list[$row['user_id']] = $user;
		}
		$user_list->set_users($list);
	}

	// laborious!.. from now on I'm using a gateway 
	function save($user) {
		$user_id = $id = $user->get_id();
		$random_identifier = $user->get_random_identifier();
		$username = $user->get_username();
		$email = $user->get_email();
		$first_name = $user->get_first_name();
		$last_name = $user->get_last_name();
		$user_type = $user->get_user_type();
		$user_sub_type = $user->get_user_sub_type();
		$password_hash = $user->get_password_hash();
		$activated = $user->get_activated();
		$email_confirmed = $user->get_email_confirmed();
		$address1 = $user->get_address1();
		$address2 = $user->get_address2();
		$address3 = $user->get_address3();
		$address4 = $user->get_address4();
		$postcode = $user->get_postcode();
		$lon = $user->get_lon();
		$lat = $user->get_lat();
		$phone = $user->get_phone();
		$phone2 = $user->get_phone2();
		$fax = $user->get_fax();
		$created_date_time = $user->get_created_date_time();
		$last_login_date_time = $user->get_last_login_date_time();
		$last_update_date_time = $user->get_last_update_date_time();
		$profile_image = $user->get_profile_image();
		$how_hear_about_us = $user->get_how_hear_about_us();

		$message = $user->get_message();
		if ($how_hear_about_us == 'other') {
			$how_hear_about_us = $user->get_how_hear_about_us_other();
		}

		if ($user->get_id() > 0) {
			$sql = 'update user set 
				message=?,
				random_identifier=?,
				username=?,
				first_name = ?,
				last_name = ?,
				email=?,
				password_hash=?,
				activated=?,
				email_confirmed=?,
				address1=?,
				address2=?,
				address3=?,
				address4=?,
				postcode=?,
				lon=?,
				lat=?,
				phone=?,
				phone2=?,
				fax=?,
				created_date_time=?,
				last_login_date_time=?,
				last_update_date_time=?,
				profile_image=?,
				how_hear_about_us=?
				
				where id=' . (int) $user->get_id();

			$statement = mysqli_prepare($this->db(), $sql);
			$this->error_check();
			mysqli_stmt_bind_param($statement, 'sssssssiisssssssssssssss', $message, $random_identifier, $username, $first_name, $last_name, $email, $password_hash, $activated, $email_confirmed, $address1, $address2, $address3, $address4, $postcode, $lon, $lat, $phone, $phone2, $fax, $created_date_time, $last_login_date_time, $last_update_date_time, $profile_image, $how_hear_about_us);
			mysqli_execute($statement);
		} else {
			$sql = 'insert into user  
				(id, message, random_identifier, first_name, last_name, username, created_date_time, email, user_type, user_sub_type, password_hash, activated, email_confirmed, address1, address2, address3, address4, postcode, lon, lat, phone, phone2, fax, last_login_date_time, last_update_date_time, profile_image, how_hear_about_us)
				values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
				';

			$statement = mysqli_prepare($this->db(), $sql);
			$this->error_check();

			mysqli_stmt_bind_param($statement, 'issssssssssiissssssssssssss', $id, $message, $random_identifier, $first_name, $last_name, $username, $created_date_time, $email, $user_type, $user_sub_type, $password_hash, $activated, $email_confirmed, $address1, $address2, $address3, $address4, $postcode, $lon, $lat, $phone, $phone2, $fax, $last_login_date_time, $last_update_date_time, $profile_image, $how_hear_about_us);
			mysqli_execute($statement);
			$id = $user_id = $this->last_id();

			$user->set_user_id($id);
			$user->set_id($id);
		}
		return $user->get_id();
	}

}
