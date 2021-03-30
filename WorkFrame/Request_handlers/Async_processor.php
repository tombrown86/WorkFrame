<?php
/**
 * Client side bridge to server side only processors
 */
namespace WorkFrame\Request_handlers;

class Async_processor extends \WorkFrame\Request_handler {

	function process() {
		header('Content-type: application/json');
		$POST = $GLOBALS['_ORIGINAL_POST'] ?? $_POST;
		if(preg_replace('/[^\w_]/i','', $POST['processor_name']) !== $POST['processor_name']) {
			throw new \WorkFrame\Exceptions\Invalid_processor_name_exception('Invalid_processor_name_exception', 'POST:'.print_R($POST, 1));
		}
		$processor_name = '\\'.APP_NAMESPACE.'\\Processors\\'.$POST['processor_name'];
		$processor = new $processor_name;
		if(isset($POST['data'])) {
			$processor->set_data($POST['data']);
		}
		echo json_encode([
			'result' => (array)$processor->server_side($POST['field_name'], $POST['value'], $POST['data'], $POST['form_id']),
			'value' => $POST['value']
		]);
		exit;
	}

}

