<?php
class LaceReplacer implements iLace {

    private $pattern = '/~\{\{ \s* 
        ( (?<varname> \$\w+(\:\w+)*) | (?<id> \#\w+\:\w+) | (?<expr> \(.*?\)) ) \s* 
        (?<filters> (\|\s*\w+\s*)*) 
    \}\}~
    /six';
    
	private $id = '';
	private $varname = '';
	private $expr = '';
	
	private $filters = array();

	public function __construct(string $rawString) {
		$m = array();
		if(preg_match($this->pattern, $rawString)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Include.');
		$this->id = (isset($m['id'])&&!empty($m['id'])) ? $m['id'] : '';
		$this->varname = (isset($m['varname'])&&!empty($m['varname'])) ? $m['varname'] : '';
		$this->expr = (isset($m['expr'])&&!empty($m['expr'])) ? $m['expr'] : '';
		$filters = (isset($m['filters'])&&!empty($m['filters'])) ? $m['filters'] : ''; 
		$this->filters = Filters::strToFilterList($filters);
	}

	public function parse(Context $context) {

	}

}
?>