<?php
class Expression {
    
    private $rawString = '';
    
    private $tokens = array(
        'T_LITERAL_NUMBER' => '/^\d+(?:\.\d+)?/',
        'T_LITERAL_BOOLEAN' => '/^(?:true|false)/',
        'T_VARIABLE' => '/^\$\w+(?:\:\w+)*/',
        'T_OP' => '/^(?:&&|\|\||\^\^|[\+\-\/\*%\^])/',
        'T_PARENTHESES_OP' => '/^\(/',
        'T_PARENTHESES_CL' => '/^\)/'
    );
    
    public function __construct($expression='') {
        $this->rawString = $expression;
    }
    
    public function parse(&$context) {
        $tokenList = array();
        $buffer = $this->rawString;
        while(strlen($buffer)>0) {
            foreach($this->tokens as $tokenName=>$pattern) {
                $m = array();
                if(preg_match($pattern, $buffer, $m)===1) {
                    echo $tokenName;
                    $buffer = substr($buffer, strlen($m[0]));
                }
            }
        }
    }
    
}
?>