<?php
class Expression {
    
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
    
    public function parse() {
        return $this->parse_opbool();
    }
    
    private function ignoreWhitespace() {
        $ws = $this->consumeRegex('/^ [\s\t]+ /xs');
        if($ws===null) return false;
        return true;
    }
    
    // opbool ::= opcomp ( "&&" | "||" | "^^" ) opbool / opcomp
    private function parse_opbool() {
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
    private function parse_opcomp() {
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
    private function parse_opmath_sum() {
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
    
    // opmathmult ::= unaryop ("+"/"-" ... ) opmathmult / unaryop
    private function parse_opmath_mult() {
        $opa = $this->parse_unaryop();
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
                return pow( $opa , $opb );
                break;
        }
    }
    
    // unaryop ::= ( "!" ) value / value
    private function parse_unaryop() {
        $op = $this->consumeRegex('/ ^\! | ^typeof /sx');
        $this->ignoreWhitespace();
        $opa = $this->parse_value();
        // None
        if($op===null&&$opa===null) return null;
        // It's just value
        if($op===null&&$opa!==null) return $opa;
        // It´s unary op
        switch($op) {
            case '!':
                return !$opa;
                break;
            case 'typeof':
                return gettype($opa);
                break;
        }
    }
    
    // value ::= variable / literal / "(" opbool ")"
    private function parse_value() {
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
    
    // variable ::= "$" [a-z]+ (":" [a-z]+)? ( "exists" )? / "#" [a-z]+ ( "exists" )?
    private function parse_variable() {
        $var = $this->consumeRegex('/^ \$ \w+ (?: \:\w+ )* /xi');
        if($var!==null) {
            $this->ignoreWhitespace();
            $exists = $this->consumeRegex('/ ^exists /xsi');
            if($exists!==null) return $this->context->exists($var);
            return $this->context->get($var);
        }
        $var = $this->consumeRegex('/^ \# \w+ /xi');
        if($var!==null) {
            $this->ignoreWhitespace();
            $exists = $this->consumeRegex('/ ^exists /xsi');
            if($exists!==null) return $this->context->exists($var);
            return $this->context->get($var);
        }
        return null;
    }
    
    private function parse_identifier() {
        $var = $this->consumeRegex('/^ \$ \w+ (?: \:\w+ )* /xi');
        if($var!==null) {
            return $var;
        }
        $var = $this->consumeRegex('/^ \# \w+ /xi');
        if($var!==null) {
            return $var;
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
        $num = $this->consumeRegex('/^ \-?[0-9]+ \. [0-9]+ /x');
        if($num===null) return null;
        return floatval($num);
    }
    
    // int ::= [0-9]+
    private function parse_int() {
        $num = $this->consumeRegex('/^ \-?[0-9]+ /x');
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