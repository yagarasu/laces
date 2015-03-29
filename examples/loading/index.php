<?php

	// Define the correct directory. Optional. Default set to 'laces/'.
	define('LACES_ROOT', '../../laces/');
	// Require the autoloader
	require LACES_ROOT . 'autoloader.inc.php';
	// Register it
	laces_register_autoloader();

	// Context
	$c = new Context();
	$c->set('$title','Laces Template.');
	$c->set('$name','Laces fan');

	// Parse
	$laces = new Laces($c);
	$laces->loadAndRender('myTemplate.ltp');

?>