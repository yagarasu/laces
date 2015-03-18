<?php
/**
 * Lace factory static class
 * 
 * Creates the right LaceX class with the given string
 */
class LaceFactory {
    
    /**
     * Returns a new Lace created from the raw string
     * 
     * @param string $rawString The captured string
     */
    public static function create(string $rawString) {
        
        if(preg_match('/~\{ \s* 
		include (?<id>\#\w+)?) \s* 
			(?<attrs> (?:\w+=\".*?\"\s*)*) \s*
			(?<filters> (?:\|\s*\w+\s*)*) \s*
		\}~ 
		/six', $rawString)===1) return new LaceInclude($rawString);
		
		if(preg_match('/~\{\{ \s* 
            ( (?<varname> \$\w+(\:\w+)*) | (?<id> \#\w+\:\w+) | (?<expr> \(.*?\)) ) \s* 
            (?<filters> (\|\s*\w+\s*)*) 
        \}\}~
        /six', $rawString)===1) return new LaceReplacer($rawString);
        
        return null;
    }
    
}
?>