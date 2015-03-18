<?php
class LaceInclude implements iLace {

	private $pattern = '/~\{ \s* 
		view (?<id>\#\w+)?) \s* 
			(?<attrs> (?:\w+=\".*?\"\s*)*) \s*
			(?<filters> (?:\|\s*\w+\s*)*) \s*
		\}~ 
		/six';

	private $id = '';
	private $src = '';
	private $parse = true;
	private $filters = array();

	public function __construct(string $rawString) {
		$m = array();
		if(preg_match($this->pattern, $rawString)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Include.');
		$this->id = (isset($m['id'])&&!empty($m['id'])) ? $m['id'] : '';
		$this->src = (isset($m['src'])&&!empty($m['src'])) ? $m['src'] : '';
		$this->parse = (isset($m['parse'])&&!empty($m['parse'])) ? $m['parse'] : 'true';
		if(strtoupper($this->parse)==='TRUE') {
			$this->parse = true;
		} else if(strtoupper($this->parse)==='FALSE') {
			$this->parse = false;
		} else {
			throw new Exception('Unknown value "'.$this->parse.'" for "parse" attribute.');
		}
		$filters = (isset($m['filters'])&&!empty($m['filters'])) ? $m['filters'] : ''; 
		$this->filters = Filters::strToFilterList($filters);
	}

	public function parse(Context $context) {

	}

}
?>