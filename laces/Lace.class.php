<?php
abstract class Lace {

	protected $pattern = '/~\{ 
	        .*?
	    \}~ 
		/six';

	protected $filters = array();
	protected $attrs = array();
	
	public $rawString = '';

    protected function parseAttrs($rawString) {
        if(empty($rawString)) return;
        $am = array();
        if(preg_match_all('/\w+=\".*?\"/msx', $rawString, $am)===0) throw new Exception('Attribute syntax error.');
        foreach($am[0] as $attr) {
            $a = $this->parseSingleAttr($attr);
            $this->attrs[$a['name']] = $a['value'];
        }
    }
    
    protected function parseSingleAttr($rawString) {
        $m = array();
        $pattern = '/(?<aname> \w+ )=(?<aval> \".*?\" )/msx';
        if(preg_match($pattern, $rawString, $m)===0) throw new Exception('Attribute syntax error.');
        return array(
        	'name'	=>	$m['aname'] ,
        	'value'	=>	substr($m['aval'], 1, strlen($m['aval'])-2)
        );
    }
    
    public function __toString() {
        return '{ Lace }';
    }
    
}
?>