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
    public static function create($rawString) {
        
        if(preg_match('/~\{ \s* 
		(foreach (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
				(?<cont> .*?)
		\{ \s* foreach \k<id> \s* \}~ 
		/six', $rawString)===1) return new LaceForeach($rawString);
        
        if(preg_match('/~\{ \s* 
		(include (?<id>\#\w+)?) \s* 
			(?<attrs> (?:\w+=\".*?\"\s*)*) \s*
			(?<filters> (?:\|\s*\w+\s*)*) \s*
		\}~ 
		/six', $rawString)===1) return new LaceInclude($rawString);
		
		if(preg_match('/~\{\{ \s*
		(
		  (?<id> \#\w+) |
		  (?<var> \$\w+(?:\:\w+)* ) |
		  (?<exp> \[.*?\])
		)
		  (?<filters> (\s*\|\s*\w+)*)
		\s* \}\}~
        /six', $rawString)===1) return new LaceReplacer($rawString);
        
        return null;
    }
    
}
?>