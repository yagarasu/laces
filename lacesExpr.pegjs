/**
 * Laces Expr PEG.js grammar
 *
 * Warning! Infinite recursion on unbalanced parentheses.
 */

start
  = expr?

expr
  = boolop

boolop
  = opa:comp "&&" opb:boolop { return opa && opb; }
  / opa:comp "||" opb:boolop { return opa || opb; }
  / opa:comp "^^" opb:boolop { return (!opa && opb) || (opa && !opb); }
  / comp

comp
  = opa:add "==" opb:comp { return opa == opb; }
  / opa:add "!=" opb:comp { return opa != opb; }
  / opa:add ">=" opb:comp { return opa >= opb; }
  / opa:add "<=" opb:comp { return opa <= opb; }
  / opa:add ">"  opb:comp { return opa >  opb; }
  / opa:add "<"  opb:comp { return opa <  opb; }
  / add

add
  = opa:mult "+" opb:add { return opa + opb; }
  / opa:mult "-" opb:add { return opa - opb; }
  / mult

mult
  = opa:pow "*" opb:mult { return opa * opb; }
  / opa:pow "/" opb:mult { return opa / opb; }
  / opa:pow "%" opb:mult { return opa % opb; }
  / pow

pow
  = opa:atom "^" opb:pow { return Math.pow(opa, opb); }
  / atom
  
atom
  = variable
  / literal
  / "(" e:boolop ")" { return e; }

variable
  = "$" ident:[a-zA-Z]+ sub:(":"[a-zA-Z]+)* {
    var f = '';
    for(var s=0; s<sub.length; s++) {
      f = f + ':' + sub[s][1].join('');
    }
    return '$' + ident.join('') + f;
  }
  / "#" ident:[a-zA-Z]+ { return "#" + ident.join(''); }

literal
  = nbool
  / number

nbool
  = "!" b:bool { return !b; }
  / bool

bool
  = "true"i { return true; }
  / "false"i { return false; }

number
  = float
  / integer

float
  = int:integer "." frac:integer { return parseFloat( int.toString() + '.' + frac.toString() ); }

integer
  = digits:[0-9]+ { return parseInt(digits.join('')); }