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
	}

	public function loadAndRender(string $url) {
		echo $this->loadAndParse($url);
	}

}
?>