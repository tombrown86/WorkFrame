<?php

namespace WorkFrame\Processors;

class Async_processor extends Processor {

	protected $data;

	function get_data() {
		return $this->data;
	}

	function set_data($data) {
		$this->data = $data;
	}

	public function get_processor_url() {
		return '/_async_processor/';
	}

	static final function client_side() {
		return NULL;
	}

}
