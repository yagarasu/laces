<?php
// Shared
define('LACES_VERSION','1.0.0');
if(!defined('LACES_ROOT')) define('LACES_ROOT', 'laces/');

// Autoload
spl_autoload_register(function($className) {
	if(preg_match('/^i[A-Z]\w*$/', $className)===1) {
		// Is interface
		$filename = LACES_ROOT . $className . '.inc.php';
	} else {
		// Is class
		$filename = LACES_ROOT . $className . '.class.php';
	}
	if(!is_readable($filename)) throw new Exception('Unable to load file "'.$filename.'".');
	require_once($filename);
});

/**
 * Laces core class
 */
class Laces {

	// Main context
	private $context = null;

	/**
	 * Constructor
	 * 
	 * @param Context $context Optional. Context used for the next parsing.
	 */
	public function __construct($context=null) {
		$this->context = ($context===null) ? new Context() : $context;
	}

	/**
	 * Takes a well formed template and returns the result of parsing it.
	 * 
	 * @param string $template Template to parse
	 * @return string Parsed template
	 */
	public function parse($template) {
		$buffer = $template;
		$hdr = $this->header_get($buffer);
		if($hdr===null) throw new Exception('Header not found.');
		$buffer = substr($buffer, strlen($hdr['raw']));
		$header = $this->header_parse($hdr['hdr']);
		$pattern = '/
			(?:
				(~\{\{ \s* ((\$\w+(\:\w+)*) | (\#\w+\:\w+) | (\(.*?\)) ) \s* (\|\s*\w+\s*)* \}\}~)
				|
				(~{ \s* \w+ \s* (\(.*?\))? \s* (\w+=\".*?\")* \s* (\|\s*\w+\s*)* \}~)
				|
				(~{ \s* (?<fulltag>\w+(\#\w+)?) \s* (\(.*?\))? \s* (\w+=\".*?\")* \s* (\|\s*\w+\s*)* \} .*? \{ \s* \k<fulltag> \s*\}~)
			)
		/sxmi';
		$buffer = preg_replace_callback($pattern, array($this, 'parse_preg_replace_cb'), $buffer);
		return $buffer;
	}
	
	/**
	 * Recieves the matched lace and replaces it with the correct value given by the lace itself.
	 * 
	 * @param array $match Matched elements from preg_replace_callback
	 * @return string The correct replacement given the lace behaviour and context
	 */
	private function parse_preg_replace_cb($match) {
		$lace = LaceFactory::create($match[0]);
		if($lace===null) return $match[0];
		return $lace->parse($this->context);
	}
	
	/**
	 * Takes the current template and gets the header. If no header is found, returns null
	 * 
	 * @param string $template Template to parse
	 * @return array An array containing the raw match ['raw'] and the parsed match ['hdr'].
	 */
	private function header_get($template) {
		$pattern = '/^\s*(?<hdr>\{\{\{.*?\}\}\})/';
        $m = array();
        if(preg_match($pattern, $template, $m)===1) return array(
            'raw' => $m[0],
            'hdr' => $m['hdr']
        );
        return null;
	}
	
	/**
	 * Takes the header lace and returns the metadata contained in it. Throws exception if header is wrongly formed.
	 * 
	 * @param string $header A correctly formed header.
	 * @return array The metadata from the header
	 */
	private function header_parse($header) {
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
        	if($attr==='') continue;
            $a = $this->header_parseAttribs($attr);
            $meta[$a['name']] = $a['value'];
        }
        var_dump($meta);
        return $meta;
	}

	/**
	 * Takes a string formatted as a param and returns key->value
	 * @param  string $rawString The raw string
	 * @return array The result of the parsing in the form of array( 'name'=>$name, 'value'=>$value )
	 */
	private function header_parseAttribs($rawString) {
		$m = array();
        $pattern = '/(?<aname>[a-zA-Z][a-zA-Z0-9\-\_]+)=(?<aval>\".*?\")/';
        if(preg_match($pattern, $rawString, $m)===0) throw new Exception('Attribute syntax error.');
        return array(
        	'name'	=>	$m['aname'] ,
        	'value'	=>	substr($m['aval'], 1, strlen($m['aval'])-2)
        );
	}

	/**
	 * Wrapper for parse. Prints the result of the parsing
	 * 
	 * @param string $template Template to render
	 */
	public function render($template) {
		echo $this->parse($template);
	}

	/**
	 * Loads a file and then parses it.
	 * 
	 * @param string $url URL to get and parse
	 * @return string The result of the parsed content.
	 */
	public function loadAndParse($url) {
		if(preg_match('/^https?\:\/\//')===1) throw new Exception('For security reasons, you can only load relative paths.');
		$temp = file_get_contents($url);
		if($temp===false) throw new Exception('Unable to get the content from "'.$url.'".');
		return $this->parse($temp);
	}

	/**
	 * Wrapper for loadAndParse. Prints the result of parsing
	 * 
	 * @param string $url URL to get, parse and print
	 */
	public function loadAndRender($url) {
		echo $this->loadAndParse($url);
	}

}
?>