<?php
/**
 * Client side bridge to server side only processors
 */
namespace WorkFrame\Request_handlers;

class Async_processor extends \WorkFrame\Request_handler {

	function process() {
		header('Content-type: application/json');
		if(preg_replace('/[^\w_]/i','', $_POST['processor_name']) !== $_POST['processor_name']) {
			throw new \WorkFrame\Exceptions\Invalid_processor_name_exception('Invalid_processor_name_exception', 'POST:'.print_R($_POST, 1));
		}
		$process_name = '\\'.APP_NAMESPACE.'\\Processors\\'.$_POST['processor_name'];
		$processor = new $process_name;
		echo json_encode([
			'result' => (array)$processor::server_side($_POST['field_name'], $_POST['value'], $_POST['data'], $_POST['form_id']),
			'value' => $_POST['value']
		]);
		exit;
	}

}
