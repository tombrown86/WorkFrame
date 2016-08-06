<?php
/**
 * A little wrapper for a recommended Mysql DB library: https://github.com/joshcam
 */
namespace ExampleApp;

class MysqliDb_bridge_mapper extends \MysqliDb {
	
	function __construct($connection_identifier='default') {
		$conf = $this->_get_db_conf($connection_identifier);
		parent::__construct($conf['server'], $conf['username'], $conf['password'], $conf['database']);
	}


	// override me if you have complex/multiple db conf info
	protected function _get_db_conf($connection_identifier) {
		$db_conf = conf('db');
		if(isset($db_conf[$connection_identifier]))
			return $db_conf[$connection_identifier];
		else 
			throw new \WorkFrame\Exceptions\Database_connection_exception('Unknown db connection identifier: '.$connection_identifier);
	}
}
