<?php
/**
 * Laces template engine
 * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
 * @version 1.0.0
 */
class Laces {

	public $template = '';
	private $context = null;

	/**
	 * Constructor
	 * @param string $template Text to be rendered.
	 * @param array  $context  Default blank. Associative array containing the variables to use within the template.
	 */
	public function __construct($template, $context = array()) {
		$this->template = $template;
		$this->context = $context;
	}

	/**
	 * Sets a variable in the current context
	 * @param string $key 	The identifier
	 * @param mixed $value 	The new value
	 */
	public function setVar($key, $value) {
		$this->context[$key] = $value;
	}

	/**
	 * Returns the variable from the current context
	 * @param  string $key The identifier
	 * @return mixed      The previously set value
	 */
	public function getVar($key) {
		if (!isset($this->context[$key])) return null;
		return $this->context[$key];
	}

	/**
	 * Deletes the variable in the current context
	 * @param  string $key The identifier
	 */
	public function delVar($key) {
		if (!isset($this->context[$key])) return null;
		unset $this->context[$key];
	}

	/**
	 * Returns true if the variable is set, false if not
	 * @param  string $key The identifier
	 * @return boolean Whether the variable is set or not
	 */
	public function issetVar($key) {
		return isset($this->context[$key]);
	}

	/**
	 * Loads a template
	 * @param  string $url URL to get the template from
	 * @return boolean      True on success, false on error
	 */
	public function load($url) {
		if(preg_match('/^[a-zA-Z]+\:\/\//i', $templateUrl)===1) throw new Exception("You can't import a template with an absolute route.");
		if(!is_readable($url)) return false;
		var $buffer = '';
		$buffer = @file_get_contents($url);
		if($buffer===false) return false;
		$this->template = $buffer;
		return true;
	}

	public function render() {
		echo $this->parse();
	}

	/**
	 * Returns the parsed template using the current context
	 * @return string The parsed template
	 * @todo Check the pattern to reflect the specification
	 */
	public function parse() {
		var $buffer = '';
		$pattern = ''
			.'~\{\s*'									// ~{
			.'(?P<cmd>\$?\w+)(?:\:(?P<subcmd>\w+))?\s*'	// [$]foo[:bar]
			.'(?P<attrs>\w+=\".*?\")*\s*'				// attrn="attrv" ...
			.'(\}(?P<cont>.*?)\{\1)?\s*'				// [} ... {foo]
			.'\}~';										// }~
		return preg_replace_callback('/'.$pattern.'/mis', array($this,'parseSingle'), $this->template);
	}

	/**
	 * Handles the replacement of single matched patterns
	 * @param  array $matches Array result of the preg
	 * @return string          String to replace the pattern with
	 * @todo Use Crinoline View as base to handle the replacements
	 */
	private function parseSingle($matches) {

	}

}
?>
