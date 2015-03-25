<?php
class LaceHook extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(hook (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
		\}~ 
		/six';
		
	protected $attrs = array(
		'name'	=>	''
	);

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Include.');
		$this->parseAttrs($m['attrs']);
	}

	public function parse(Context &$context) {
	    if(empty($this->attrs['name'])) {
	        $output = '<!-- Laces Foreach';
			$output .= (isset($this->attrs['id'])) ? $this->attrs['id'] : '';
			$output .= ' Error. "'.$this->attrs['use'].'" is not an array or it does not exist.';
			$output .= '-->';
			return $output;
	    }
		return '';
	}
	
	public function __toString() {
		return '{ Lace:Hook }';
	}

}
?>