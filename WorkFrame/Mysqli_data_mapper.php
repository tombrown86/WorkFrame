<?php

namespace WorkFrame;

//Mysql database data mapper to be precise
class Mysqli_data_mapper {
	private static $dbs = [];
	function __construct() {
	}
//	
//	// lazy close connection
//	function __destruct() {
//		foreach(self::$dbs as $db) {
//			mysqli_close($db);
//		}
//	}
	
	// lazy open connection
	protected function db($connection_identifier=null) {
		$connection_identifier = $connection_identifier ?: 'default';
		if(!isset(self::$dbs[$connection_identifier])) {
			$conf = $this->_get_db_conf($connection_identifier);
			$link = mysqli_connect($conf['server'], $conf['username'], $conf['password'], $conf['database']);

			if (!$link) {
				$msg = "Error: Unable to connect to MySQL. "
					. " Debugging errno: " . mysqli_connect_errno()
					. " Debugging error: " . mysqli_connect_error();
				log_message(WF_LOG_LEVEL_ERROR, $msg);
				throw new \WorkFrame\Exceptions\Database_connection_exception($msg);
			}

			if(WORKFRAME_DEBUG) {
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			}
			mysqli_set_charset($link, 'utf8');
			self::$dbs[$connection_identifier] = $link;
		}

		return self::$dbs[$connection_identifier];
	}

	// override me if you have complex/multiple db conf info
	protected function _get_db_conf($connection_identifier) {
		$db_conf = conf('db');
		if(isset($db_conf[$connection_identifier]))
			return $db_conf[$connection_identifier];
		else 
			throw new \WorkFrame\Exceptions\Database_connection_exception('Unknown db connection identifier: '.$connection_identifier);
	}

	protected function error_check($connection_identifier=null) {
		$db = $this->db($connection_identifier);

		if(mysqli_error($db) != '') {
			throw new \WorkFrame\Exceptions\Database_query_error_exception('A database error occurred', mysqli_error($db));
		}
	}
	protected function escape($s, $connection_identifier=null) {
		$db = $this->db($connection_identifier);
		return mysqli_real_escape_string($db, $s);
	}
	protected function query($q, $connection_identifier=null) {
		$db = $this->db($connection_identifier);
		$result = mysqli_query($db, $q);
		$this->error_check();
		return $result;
	}
	protected function row_query($q, $connection_identifier=null) {
		$db = $this->db($connection_identifier);
		$result = mysqli_query($db, $q);
		$this->error_check();
		return mysqli_fetch_assoc($result);
	}
	protected function rows_query($q, $connection_identifier=null) {
		$db = $this->db($connection_identifier);
		$result = mysqli_query($db, $q);
		$this->error_check();
		$rows = [];
		while($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
	}
	protected function value_query($q, $connection_identifier=null) {
		$db = $this->db($connection_identifier);
		$result = mysqli_query($db, $q);
		$this->error_check();
		$row = mysqli_fetch_assoc($result);
		return $row ? array_pop($row) : FALSE;
	}
	protected function comma_separated_ints($id_array) {
		$id_array = array_map('intval', $id_array);
		return implode($id_array, ',');
	}
	protected function last_id($connection_identifier=null) {
		$db = $this->db($connection_identifier);
		return mysqli_insert_id($db);
	}
}
