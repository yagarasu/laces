<?php
class Context {

	private $rawArray = null;

	public function __construct($rawArray=array()) {
		$this->rawArray = $rawArray;
	}
	
}
?>