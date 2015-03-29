<?php

	// Define the correct directory. Optional. Default set to 'laces/'.
	define('LACES_ROOT', '../../laces/');
	// Require the autoloader
	require LACES_ROOT . 'autoloader.inc.php';
	// Register it
	laces_register_autoloader();

	// Parse
	$laces = new Laces();
	$laces->render('
		{{{ LacesTemplate author="Alexys Hegmann - Yagarasu" }}}

		Hello. Did you know that this template was made by ~{{ $_HEADER:author }}~?
	');

?>