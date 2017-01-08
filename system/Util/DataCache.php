<?php
namespace Application\Util;

class DataCache {

	private $key;
	private $fileName;
	private $exists;
	private $seconds;
	private $object;

	public function __construct($key, $seconds = 86400) {

		$this->key = $key;
		$this->seconds = $seconds;
		$this->fileName = $this->getFileName();
		$this->exists = $this->fileExists();

	}

	private function getFileName() {

		return DATA_PATH . 'data-cache/' . $this->key . '.cache';

	}

	public function purge() {

		if ($this->fileExists()) {
			return unlink($this->fileName);
		}

		return false;

	}

	public function hasCache() {

		$time = $this->getCacheTime();

		if($this->exists && $time <= $this->seconds){
			return true;
		}

		return false;

	}

	public function cache($data) {

		$this->object = new \stdClass;
		$this->object->time = time();
		$this->object->data = $data;

		file_put_contents($this->fileName, serialize($this->object));

	}

	private function fileExists() {

		return file_exists($this->fileName);

	}

	private function getObject() {

		if($this->exists){

			$this->object = unserialize(file_get_contents($this->fileName));

			return $this->object;

		}

		return null;

	}

	public function getCacheData() {

		if(empty($this->object)){
			$this->getObject();
		}

		if(isset($this->object->data)){
			return $this->object->data;
		}

		return null;

	}

	private function getCacheTime() {

		if($this->exists){

			$object = $this->getObject();

			if(!empty($object->time)){
				return time() - $object->time;
			}

		}

		return -1;

	}

}