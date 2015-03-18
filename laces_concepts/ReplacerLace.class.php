<?php
class ReplacerLace implements ILace {
	public function parse($context, $expr=null, $attrs=null) {
		$buffer = $context->getVar($match['var']);
        return $buffer;
	}
}
?>