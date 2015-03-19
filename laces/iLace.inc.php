<?php
interface iLace {
	public function __construct($rawString);
	public function parse(Context $context);
}
?>