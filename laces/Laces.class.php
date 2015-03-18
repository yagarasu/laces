<?php
class Laces {

	private $context = null;

	public function __construct($context=null) {
		$this->context = ($context===null) ? new Context() : $context;
	}

	public function parse(string $template) {
	}

	public function render(string $template) {
		echo $this->parse($template);
	}

	public function loadAndParse(string $url) {
		if(preg_match('/^https?\:\/\//')===1) throw new Exception('For security reasons, you can only load relative paths.');
		$temp = file_get_contents($url);
		if($temp===false) throw new Exception('Unable to get the content from "'.$url.'".');
		return $this->parse($temp);
	}

	public function loadAndRender(string $url) {
		echo $this->loadAndParse($url);
	}

}
?>