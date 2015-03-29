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
	$c->registerHook('DOC_END', function($input, $attrs) use ($c) {
		// is the end of the doc. Include scripts, maybe
		return $input.'<script>alert("Injected javascript via hooks with the attrib foo='.$attrs['foo'].'. Access to the context via use($context): '.$c->get('$_HEADER:author').'");</script>';
	});

	// Parse
	$laces = new Laces($c);
	$laces->loadAndRender('myTemplate.ltp');

?>