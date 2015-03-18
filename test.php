<?php

	require 'laces/Context.class.php';
	
	$c = new Context();
	
	$c->set('$foo:bar', 'foooooo');
	$c->set('COOONST', 'const const');
	$c->set('#myId', array('asd','asd'));

	var_dump($c->exists('#myId'));

	//var_dump($c->getRawArray());

?>