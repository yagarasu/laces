<?php
class Filters {

	public static function strToFilterList($rawString) {
		// Remove extra spaces and trim
		$rawString = preg_replace('/\s+/', ' ', $rawString);
		$rawString = trim($rawString);
		// Get array from pipes without the first pipe
		$filters = preg_split('/\|\s/', substr($rawString,1));
		$res = array();
		foreach ($filters as $filter) {
			$finalFilter = preg_replace('/\s/', '_', $filter);
			array_push($res, 'filter' . $finalFilter);
		}
		return $res;
	}
	
	public static function filterWith($input, $filters) {
		$buffer = $input;
		foreach($filters as $f) {
			if(is_callable('self::'.$f)) {
				$buffer = call_user_func('self::'.$f, $input);
			}
		}
		return $buffer;
	}

	public static function filter_html($input) {
		return htmlspecialchars($input);
	}

	public static function filter_mysql($input) {
		$replace = array(
			"\x00"	=>'\x00',
			"\n"	=>'\n',
			"\r"	=>'\r',
			"\\"	=>'\\\\',
			"'"		=>"\'",
			'"'		=>'\"',
			"\x1a"	=>'\x1a'
		);
		return strtr($input, $replace);
	}

	public static function filter_attr($input) {
		$replace = array(
			'"'	=> "'"
		);
		return strtr($input, $replace);
	}

}
?>