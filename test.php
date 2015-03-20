<?php

	// require 'laces/Laces.class.php';
	
	// $c = new Context();
	// $c->set('$title','Prueba de Laces.');
	// $c->set('$nombre','Alexys <em>Yagarasu</em>');
	
	// $c->set('$news',array(
	// 	'Fooooo',
	// 	'Baaaarrrr',
	// 	'Baaaaaaaazzzzz'
	// ));
	
	// $c->set('$sNews', 'FUUUUUU');
	
	// $l = new Laces($c);
	
	// $l->loadAndRender('test.ltp');
	
	// echo '<hr><pre>';
	// var_dump($c->getRawArray());
	// echo '</pre><hr>';

	require 'laces/Expression.class.php';
	
	$e = new Expression('2 + 2');
	echo $e->parse();
	
?>