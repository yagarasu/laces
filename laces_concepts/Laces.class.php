<?php
define('LACES_VERSION','1.0.0');
/**
 * Laces
 */
class Laces {
    
    public $context = array();

    public function __construct($context=array()) {
    	$this->context = $context;
    }
    
    private function getHeader($template) {
        $pattern = '/^\s*(?<hdr>\{\{\{.*?\}\}\})/';
        $m = array();
        if(preg_match($pattern, $template, $m)===1) return array(
            'raw' => $m[0],
            'hdr' => $m['hdr']
        );
        return null;
    }
    
    private function parseHeader($header) {
        $meta = array(
            'filetype' => 'LacesTemplate',
            'version'  => LACES_VERSION,
            'author'   => 'Unknown',
            'language' => 'eo'
        );
        $pattern = '/\{\{\{\s*(?<filetype>[a-zA-Z][a-zA-Z0-9\-\_]+)\s*(?<attrs>(?:[a-zA-Z][a-zA-Z0-9\-\_]+=\".*?\")*)\s*\}\}\}/';
        $m = array();
        if(preg_match($pattern, $header, $m)===0) throw new Exception('Header syntax error.');
        $meta['filetype'] = $m['filetype'];
        $attrs = preg_split('/\s+/',$m['attrs']);
        foreach($attrs as $attr) {
            $this->parseHeader_attrib($attr, $meta);
        }
        return $meta;
    }
    
    private function parseHeader_attrib($attrib, &$meta) {
        $m = array();
        $pattern = '/(?<aname>[a-zA-Z][a-zA-Z0-9\-\_]+)=(?<aval>\".*?\")/';
        if(preg_match($pattern, $attrib, $m)===0) throw new Exception('Attribute syntax error.');
        $meta[$m['aname']] = substr($m['aval'], 1, strlen($m['aval'])-2);
    }
    
    public function parse($template, $context=null) {
        $buffer = $template;
        if($context===null) $context=$this->context;
        $header = $this->getHeader($buffer);
        if($header===null) throw new Exception('Header not found.');
        $buffer = substr($buffer, strlen($header['raw']));
        $meta = $this->parseHeader($header['hdr']);
        $buffer = preg_replace_callback('/~\{\{\s*(?:(?<var>\$[a-zA-Z][a-zA-Z0-9\-\_]*(?:\:[a-zA-Z][a-zA-Z0-9\-\_]*)*))\s*(?<filters>(?:\|\s*\w+\s*)*)\s*\}\}~/', array($this, 'parse_lace_replacer'), $buffer);
        $buffer = preg_replace_callback('/~\{\s*(?<fulltag>(?<tag>\w+)(?<id>\#\w+)?)\s*(?<expr>\(.*?\))?\s*(?<attrs>(?:\w+=\".*?\"\s*)*)\s*(?<filters>(?:\|\s*\w+\s*)*)\s*\}~/', array($this, 'parse_lace_standalone', $buffer);
        return $buffer;
    }
    
    private function parse_lace_replacer($match) {
        $data = $this->getVariable($match['var']);
        $filters = preg_split('/\|\s*/', substr($match['filters'],1));
        foreach ($filters as $filter) {
        	$func = 'filter_' . preg_replace('/\s/', '', $filter);
        	$data = (is_callable(array($this, $func))) ? call_user_func(array($this, $func),$data) : $data;
        }
        return $data;
    }

    private function parse_lace_standalone($match) {
    	$buffer = '';
    	
    	if(isset($match['id'])) $this->context[$match['id']] = $buffer;
    	return $buffer;
    }

    private function filter_html($input) {
    	return htmlspecialchars($input);
    }

    private function filter_mysql($input) {
    	$replace = array(
			"\x00"	=>'\x00',
			"\n"	=>'\n',
			"\r"	=>'\r',
			"\\"	=>'\\\\',
			"'"		=>"\'",
			'"'		=>'\"',
			"\x1a"	=>'\x1a'
    	);
    	return strtr($input, $replace);
    }

    private function filter_attr($input) {
    	$replace = array(
    		'"'	=> "'"
    	);
    	return strtr($input, $replace);
    }

    private function getVariable($varQuery, $searchIn=null) {
    	$haystack = ($searchIn===null) ? $this->context : $searchIn;
    	$var = explode(':', $varQuery);
    	if(count($var)>1) {
    		$thisLevelVar = array_shift($var);
    		$thisLevelVar = (substr($thisLevelVar, 0, 1)==='$') ? substr($thisLevelVar, 1) : $thisLevelVar;
    		if(isset($haystack[$thisLevelVar])) return $this->getVariable(implode(':', $var),$haystack[$thisLevelVar]);
    		throw new Exception('Variable not set.');
    		
    	} else {
    		$thisLevelVar = $var[0];
    		$thisLevelVar = (substr($thisLevelVar, 0, 1)==='$') ? substr($thisLevelVar, 1) : $thisLevelVar;
    		if(isset($haystack[$thisLevelVar])) return $haystack[$thisLevelVar];
    		throw new Exception('Variable not set.');
    		
    	}
    }
    
    public function render($template, $context=null) {
        echo $this->parse($template);
    }
    
}
?>