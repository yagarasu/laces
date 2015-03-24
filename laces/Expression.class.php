<?php
class Expression {
    
    private $buffer = '';
    private $stack = array();
    private $context = null;
    
    public function __construct($code, &$context=null) {
        $this->buffer = $code;
        $this->context = ($context===null) ? new Context() : $context;
    }
    
    private function consumeRegex($pattern) {
        $m = array();
        if(preg_match($pattern, $this->buffer, $m)===0) return null;
        $this->buffer = substr($this->buffer, strlen($m[0]));
        array_push($this->stack, $m[0]);
        return $m[0];
    }
    
    private function backtrack($amnt=1) {
        for($i=0;$i<$amnt;$i++) {
            $v = array_pop($this->stack);
            $this->buffer = $v . $this->buffer;
        }
    }
    
    // operation ::= value ("+"/"-" ... ) value
    private function parse_operation() {
        $opa = $this->parse_value();
        if($opa===null) return null;
        // value A matches
        $op = $this->consumeRegex('/^ &&|\|\||\^\^|==|!=|\<=|\>=|\+|\-|\*|\/|%|\<|\> /x');
        if($op===null) {
            $this->backtrack();
            return null;
        }
        $opb = $this->parse_value();
        if($opb===null) {
            $this->backtrack(2);
            return null;
        }
        switch($op) {
            case '+':
                return $opa + $opb;
                break;
            case '-':
                return $opa - $opb;
                break;
            case '*':
                return $opa * $opb;
                break;
            case '/':
                return $opa / $opb;
                break;
            case '%':
                return $opa % $opb;
                break;
            case '^':
                return $opa ^ $opb;
                break;
            case '&&':
                return $opa && $opb;
                break;
            case '||':
                return $opa || $opb;
                break;
            case '^^':
                return $opa xor $opb;
                break;
            case '==':
                return $opa == $opb;
                break;
            case '!=':
                return $opa != $opb;
                break;
            case '>=':
                return $opa >= $opb;
                break;
            case '<=':
                return $opa <= $opb;
                break;
            case '>':
                return $opa > $opb;
                break;
            case '<':
                return $opa < $opb;
                break;
        }
    }
    
    // value ::= variable / literal
    private function parse_value() {
        $val = $this->parse_variable();
        if($val!==null) return $val;
        $val = $this->parse_literal();
        if($val!==null) return $val;
        return null;
    }
    
    // variable ::= "$" [a-z]+ (":" [a-z]+)? / "#" [a-z]+
    private function parse_variable() {
        $var = $this->consumeRegex('/^ \$ \w+ (?: \:\w+ )* /xi');
        if($var!==null) {
            return $this->context->get($var);
        }
        $var = $this->consumeRegex('/^ \# \w+ /xi');
        if($var!==null) {
            return $this->context->get($var);
        }
        return null;
    }
    
    // literal ::= bool / number / string
    private function parse_literal() {
        $lit = $this->parse_bool();
        if($lit!==null) return $lit;
        $lit = $this->parse_number();
        if($lit!==null) return $lit;
        $lit = $this->parse_string();
        if($lit!==null) return $lit;
        return null;
    }
    
    // number ::= float / int
    private function parse_number() {
        $num = $this->parse_float();
        if($num !== null) return $num;
        $num = $this->parse_int();
        if($num !== null) return $num;
        return null;
    }
    
    // float ::= [0-9]+ (?: \.[0-9]+)?
    private function parse_float() {
        $num = $this->consumeRegex('/^ [0-9]+ \. [0-9]+ /x');
        if($num===null) return null;
        return floatval($num);
    }
    
    // int ::= [0-9]+
    private function parse_int() {
        $num = $this->consumeRegex('/^ [0-9]+ /x');
        if($num===null) return null;
        return intval($num);
    }
    
    // bool ::= "true" / "false"
    private function parse_bool() {
        $val = $this->consumeRegex('/^ true /xi');
        if(strtoupper($val)==='TRUE') return TRUE;
        $val = $this->consumeRegex('/^ false /xi');
        if(strtoupper($val)==='FALSE') return FALSE;
        return null;
    }
    
    // string ::= '"' !('"') . '"'
    private function parse_string() {
        $val = $this->consumeRegex('/^ \" .*? \" /xs');
        if($val===null) return null;
        return substr($val, 1, strlen($val)-2);
    }
    
}
?>