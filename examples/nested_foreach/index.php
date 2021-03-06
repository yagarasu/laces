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
	$c->set('$cats:inter',array(
		array( 'title'=>'Laces first public version!', 'author'=>'Carmen Crinoline', 'date'=>'28/03/2015' ),
		array( 'title'=>'Laces v1.0.0 released!', 'author'=>'Chumel Laces', 'date'=>'28/03/2015' ),
		array( 'title'=>'The new template engine', 'author'=>'Lydia C.', 'date'=>'28/03/2015' ),
	));
	$c->set('$cats:nation',array(
		array( 'title'=>'Laces is part of the Crinoline Framework', 'author'=>'Carmen Crinoline', 'date'=>'28/03/2015' ),
		array( 'title'=>'Just try it!', 'author'=>'Chumel Laces', 'date'=>'28/03/2015' ),
		array( 'title'=>'I \'m really hungry', 'author'=>'Lydia C.', 'date'=>'28/03/2015' ),
	));
	$c->set('$cats:tech',array(
		array( 'title'=>'I ran out of creativity', 'author'=>'Carmen Crinoline', 'date'=>'28/03/2015' ),
		array( 'title'=>'Foo bar bar??', 'author'=>'Chumel Laces', 'date'=>'28/03/2015' ),
		array( 'title'=>'I give up... lorem ipsum dolor sit amet', 'author'=>'Lydia C.', 'date'=>'28/03/2015' ),
	));

	// Parse
	$laces = new Laces($c);
	$laces->loadAndRender('myTemplate.ltp');

?>