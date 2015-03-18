<?php
interface iLace {
	private $pattern;
	public function __construct(string $rawString);
	public function parse(Context $context);
}
?>