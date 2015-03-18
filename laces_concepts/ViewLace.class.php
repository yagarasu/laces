<?php
/**
 * Implements View Lace
 * ~{ view src="path/file.ext" [parse="true"] }~
 * src string File to load
 * parse boolean Whether to parse with Laces or not. Default true. 
 */
class ViewLace implements ILace {
	public function parse($context, $expr=null, $attrs=null) {
		// Defaults
		$src = (isset($attrs['src'])&&!empty($attrs['src'])) ? $attrs['src'] : '';
		$parse = (isset($attrs['parse'])&&!empty($attrs['parse'])) ? $attrs['parse'] : 'true';

		if(!is_readable($src)) return '<!-- Unable to retrieve '.$src.' -->';
		$buffer = file_get_contents($src);
		if($buffer===false) return '<!-- Unable to retrieve '.$src.' -->';
		if(strtolower($parse)==='false') return $buffer;
		$l = new Laces($context);
		$buffer = $l->parse($buffer);
		unset($l);
		return $buffer;
	}
}
?>