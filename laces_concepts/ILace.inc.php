<?php
interface ILace {
	static public $pattern;
	public function parse($context, $expr=null, $attrs=null);
}
?>