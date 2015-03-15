<?php

/**
 * Parse tree node
 */
interface IParseNode {
	public function run($context);
}

/**
 * Literal String Node
 */
class NodeLiteralString implements IParseNode {

	private $str = '';

	public function __conscruct($string) {
		$this->str = $string;
	}

	public function run($context) {
		//return substr($this->code, 1, strlen($this->code)-2);
		return $this->str;
	}

}

/**
 * Literal Number Node
 */
class NodeLiteralNumber implements IParseNode {

	private $num = 0;

	public function __conscruct($number) {
		$this->num = $number;
	}

	public function run($context) {
		//if(preg_match('/^\-?\d*\.\d+$/', $this->code)===1) return floatval($this->code);
		//return intval($this->code);
		return $this->num;
	}

}

/**
 * Boolean Literal Node
 */
class NodeLiteralBool extends Node implements IParseNode {

	private $val = false;

	public function __construct($value) {
		$this->val = $value;
	}
	public function run($context) {
		//if( strtolower($this->code==='true') ) return TRUE;
		//if( strtolower($this->code==='false') ) return FALSE;
		//throw new Exception('Unexpected value for boolean literal.');
		return $this->val;
	}

}

/**
 * Variable node
 */
class NodeVariable implements IParseNode {

	private $varname = '';
	private $varprop = null;

	public function __construct($varname, $varprop=null) {
		$this->varname = $varname;
		$this->varprop = $varprop;
	}

	public function run($context) {
		if($this->varprop===null) return return $context[$this->varname];
		return $context[$this->varname][$this->varprop];
	}

}

/**
 * Binary Operation Node
 */
class NodeOperationBi implements IParseNode {

	const NODE_OP_TYPE_NULL = null;
	const NODE_OP_TYPE_ADD = 0;
	const NODE_OP_TYPE_SUB = 1;
	const NODE_OP_TYPE_MUL = 2;
	const NODE_OP_TYPE_DIV = 3;
	const NODE_OP_TYPE_MOD = 4;
	const NODE_OP_TYPE_POW = 5;
	const NODE_OP_TYPE_AND = 6;
	const NODE_OP_TYPE_OR  = 7;
	const NODE_OP_TYPE_XOR = 8;
	const NODE_OP_TYPE_EQ  = 9;
	const NODE_OP_TYPE_NEQ = 10;
	const NODE_OP_TYPE_EGT = 11;
	const NODE_OP_TYPE_ELT = 12;
	const NODE_OP_TYPE_GT  = 13;
	const NODE_OP_TYPE_LT  = 14;

	private $opA = null;
	private $opB = null;

	private $op = self::NODE_OP_TYPE_NULL;

	public function __construct($opA, $opB, $op) {
		$this->opA = $opA;
		$this->opB = $opB;
		$this->op  = $op;
	}
	public function run($context) {
		switch ($this->op) {

			case self::NODE_OP_TYPE_ADD:
				return $this->opA->run($context) + $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_SUB:
				return $this->opA->run($context) - $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_MUL:
				return $this->opA->run($context) * $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_DIV:
				return $this->opA->run($context) / $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_MOD:
				return $this->opA->run($context) % $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_POW:
				return $this->opA->run($context) ^ $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_AND:
				return $this->opA->run($context) && $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_OR:
				return $this->opA->run($context) || $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_XOR:
				return $this->opA->run($context) xor $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_EQ:
				return $this->opA->run($context) === $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_NEQ:
				return $this->opA->run($context) !== $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_EGT:
				return $this->opA->run($context) >= $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_ELT:
				return $this->opA->run($context) <= $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_GT:
				return $this->opA->run($context) > $this->opB->run($context);
				break;

			case self::NODE_OP_TYPE_LT:
				return $this->opA->run($context) < $this->opB->run($context);
				break;
			
			default:
				throw new Exception('Unexpected binary operation type.');
				break;

		}
	}
}

/**
 * Unary Operation Node
 */
class NodeOperationUnary implements IParseNode {

	const NODE_OP_TYPE_NULL = null;
	const NODE_OP_TYPE_NOT = 1;
	const NODE_OP_TYPE_INC = 2;
	const NODE_OP_TYPE_DEC = 3;

	private $opA = null;
	private $op = self::NODE_OP_TYPE_NULL;

	public function __construct($opA, $op) {
		$this->opA = $opA;
		$this->op  = $op;
	}

	public function run($context) {
		switch ($this->op) {

			case self::NODE_OP_TYPE_NOT:
				return !$this->opA;
				break;

			case self::NODE_OP_TYPE_INC:
				return ++$this->opA;
				break;

			case self::NODE_OP_TYPE_DEC:
				return ++$this->opA;
				break;
			
			default:
				throw new Exception('Unexpected unary operation');
				break;

		}
	}

}

/**
 * Expression node
 */
class NodeExpression implements IParseNode {

	private $cont = null;

	public function __construct($cont) {
		$this->cont = $cont;
	}

	public function run($context) {
		return $this->cont->run($context);
	}

}

/**
 * As-is node
 */
class NodeAsis implements IParseNode {

	private $content = '';

	public function __construct($content) {
		$this->content = $content;
	}

	public function run($context) {
		return $this->content;
	}

}

/**
 * Filters node
 */
class NodeFilters implements IParseNode {

	private $content = null;
	private $filter_cbs = array();

	public function __construct($content, $filter_cbs=array()) {
		$this->content;
		$this->filter_cb = $filter_cbs;
	}

	public function run($context) {
		if(count($this->filter_cbs)===0) return $this->content->run($context);
		$cont = $this->content;
		foreach ($this->filter_cbs as $id=>$callback) {
			if(is_callable($callback)) $cont = $callback($cont);
		}
		return $cont;
	}

}

/**
 * Lace Replacer Node
 */
class NodeLaceReplacer implements IParseNode {

	private $var = null;
	private $filters = null;

	public function __construct($variable, $filters) {
		$this->var = $variable;
		$this->filters = $filters;
	}

	public function run($context) {
		return $this->filters->run($this->var->run($context));
	}

}

/**
 * Standalone Lace Node
 */
class NodeLaceStandalone implements IParseNode {

	private $tag = '';
	private $expr = null;
	private $attrs = array();
	private $filters = null;


}

?>