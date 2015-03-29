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
	$c->set('$cat','International');
	$c->set('$news',array(
		array( 'title'=>'Laces first public version!', 'author'=>'Carmen Crinoline', 'date'=>'28/03/2015' ),
		array( 'title'=>'Laces v1.0.0 released!', 'author'=>'Chumel Laces', 'date'=>'28/03/2015' ),
		array( 'title'=>'The new template engine', 'author'=>'Lydia C.', 'date'=>'28/03/2015' ),
	));

	// Parse
	$laces = new Laces($c);
	$laces->loadAndRender('myTemplate.ltp');

?>