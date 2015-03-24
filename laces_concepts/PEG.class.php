<?php
class PEG {
    
    public $buffer = '';
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
        $cnt = 0;
        while($cnt<$amnt) {
            $v = array_pop($this->stack);
            $this->buffer = $v . $this->buffer;
            if(preg_match('/^ [\s\t]+ /xs',$v)===0) $cnt++;
        }
    }
    
    private function ignoreWhitespace() {
        $ws = $this->consumeRegex('/^ [\s\t]+ /xs');
        if($ws===null) return false;
        return true;
    }
    
    // opbool ::= opcomp ( "&&" | "||" | "^^" ) opbool / opcomp
    public function parse_opbool() {
        $opa = $this->parse_opcomp();
        if($opa===null) return null;
        // value A matches
        $this->ignoreWhitespace();
        $op = $this->consumeRegex('/ ^\&\& | ^\|\| | ^\^\^ /x');
        if($op===null) {
            // It's not the first match, but the second
            return $opa;
        }
        $this->ignoreWhitespace();
        $opb = $this->parse_opbool();
        if($opb===null) {
            $this->backtrack(2);
            return null;
        }
        switch($op) {
            case '&&':
                return $opa && $opb;
                break;
            case '||':
                return $opa || $opb;
                break;
            case '^^':
                return $opa xor $opb;
                break;
        }
    }
    
    // opcomp ::= opmathsum ( "==" | "!=" | ">=" ... ) opcomp / opmathsum
    public function parse_opcomp() {
        $opa = $this->parse_opmath_sum();
        if($opa===null) return null;
        // value A matches
        $this->ignoreWhitespace();
        $op = $this->consumeRegex('/ ^\=\= | ^\!\= | ^\>\= | ^\<\= | ^\> | ^\< /x');
        if($op===null) {
            // It's not the first match, but the second
            return $opa;
        }
        $this->ignoreWhitespace();
        $opb = $this->parse_opcomp();
        if($opb===null) {
            $this->backtrack(2);
            return null;
        }
        switch($op) {
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
    
    // opmathsum ::= opmathmult ("+"/"-" ... ) opmathsum / opnmathmult
    public function parse_opmath_sum() {
        $opa = $this->parse_opmath_mult();
        if($opa===null) return null;
        // value A matches
        $this->ignoreWhitespace();
        $op = $this->consumeRegex('/ ^\+ | ^\- /x');
        if($op===null) {
            // It's not the first match, but the second
            return $opa;
        }
        $this->ignoreWhitespace();
        $opb = $this->parse_opmath_sum();
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
        }
    }
    
    // opmathmult ::= value ("+"/"-" ... ) opmathmult / value
    public function parse_opmath_mult() {
        $opa = $this->parse_value();
        if($opa===null) return null;
        // value A matches
        $this->ignoreWhitespace();
        $op = $this->consumeRegex('/ ^\* | ^\/ | ^% | ^\^(?!\^) /x');
        if($op===null) {
            return $opa;
        }
        $this->ignoreWhitespace();
        $opb = $this->parse_opmath_mult();
        if($opb===null) {
            $this->backtrack(2);
            return null;
        }
        switch($op) {
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
        }
    }
    
    // value ::= variable / literal / "(" opbool ")"
    public function parse_value() {
        $this->ignoreWhitespace();
        $val = $this->parse_variable();
        if($val!==null) return $val;
        $val = $this->parse_literal();
        if($val!==null) return $val;
        // It's not a variable nor a literal. If it doesn't have a parentheses, fail
        $this->ignoreWhitespace();
        if($this->consumeRegex('/^ \( /x')===null) return null;
        // It looks like a nested expr. Check opbool
        $this->ignoreWhitespace();
        $expr = $this->parse_opbool();
        if($expr===null) {
            // opbool failed, backtrack
            $this->backtrack();
            return null;
        }
        $this->ignoreWhitespace();
        if($this->consumeRegex('/^ \) /x')!==null) {
            return $expr;
        }
        // Unbalanced parentheses maybe?!
        $this->backtrack(2);
        throw new Exception('Syntax error. Unbalanced parentheses.');
    }
    
    // variable ::= "$" [a-z]+ (":" [a-z]+)? / "#" [a-z]+
    public function parse_variable() {
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
    public function parse_literal() {
        $lit = $this->parse_bool();
        if($lit!==null) return $lit;
        $lit = $this->parse_number();
        if($lit!==null) return $lit;
        $lit = $this->parse_string();
        if($lit!==null) return $lit;
        return null;
    }
    
    // number ::= float / int
    public function parse_number() {
        $num = $this->parse_float();
        if($num !== null) return $num;
        $num = $this->parse_int();
        if($num !== null) return $num;
        return null;
    }
    
    // float ::= [0-9]+ (?: \.[0-9]+)?
    public function parse_float() {
        $num = $this->consumeRegex('/^ [0-9]+ \. [0-9]+ /x');
        if($num===null) return null;
        return floatval($num);
    }
    
    // int ::= [0-9]+
    public function parse_int() {
        $num = $this->consumeRegex('/^ [0-9]+ /x');
        if($num===null) return null;
        return intval($num);
    }
    
    // bool ::= "true" / "false"
    public function parse_bool() {
        $val = $this->consumeRegex('/^ true /xi');
        if(strtoupper($val)==='TRUE') return TRUE;
        $val = $this->consumeRegex('/^ false /xi');
        if(strtoupper($val)==='FALSE') return FALSE;
        return null;
    }
    
    // string ::= '"' !('"') . '"'
    public function parse_string() {
        $val = $this->consumeRegex('/^ \" .*? \" /xs');
        if($val===null) return null;
        return substr($val, 1, strlen($val)-2);
    }
    
}
?>