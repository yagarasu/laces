<?php
class Filters {

	public static function strToFilterList(string $rawString) {
		// Remove extra spaces and trim
		$rawString = preg_replace('/\s+/', ' ', $rawString);
		$rawString = trim($rawString);
		// Get array from pipes without the first pipe
		$filters = preg_split('/\|\s/', substr($match['filters'],1));
		$res = array();
		foreach ($filters as $filter) {
			$finalFilter = preg_replace('/\s/', '_', $filter);
			array_push($res, 'filter_' . $finalFilter);
		}
		return $res;
	}

	public function filter_html($input) {
		return htmlspecialchars($input);
	}

	public function filter_mysql($input) {
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

	public function filter_attr($input) {
		$replace = array(
			'"'	=> "'"
		);
		return strtr($input, $replace);
	}

}
?>