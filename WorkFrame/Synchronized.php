<?php

namespace WorkFrame;

trait Synchronized {
	private function _synchronized($key, $func, $params = []) {
		$semRes = sem_get($key); // get the resource for the semaphore

		if(sem_acquire($semRes)) { // try to acquire the semaphore. this function will block until the sem will be available
			$ret = call_user_func_array($func, $params);
			sem_release($semRes); // release the semaphore so other process can use it
			return $ret;
		}
		throw new \WorkFrame\Exceptions\WorkFrame_exception('Unable to aquire sem '.$key);
	}
	
	/*example: 
	function b() {
		$this->synchronized(123, [$this, 'c'], ['param1 value', 'param2 value']);
	}
	function c($param1, $param2) {
		echo 'synced code';
	}*/
}