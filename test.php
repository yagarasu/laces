<?php

	require 'laces/Laces.class.php';
	
	$c = new Context();
	$c->set('$title','Prueba de Laces.');
	$c->set('$nombre','Alexys <em>Yagarasu</em>');
	$c->set('#foo','Alexys <em>Yagarasu</em>');
	
	$c->set('$news:title','Lorem ipsum dolor sit amet');
	
	$c->set('$sNews', 'FUUUUUU');
	
	$c->set('$num', 5);
	
	// $l = new Laces($c);
	
	// $l->loadAndRender('test.ltp');
	
	// echo '<hr><pre>';
	// var_dump($c->getRawArray());
	// echo '</pre><hr>';

	// require 'laces/Expression.class.php';
	
	// $e = new Expression('2 + 2');
	// echo $e->parse();
	
	require 'laces_concepts/PEG.class.php';
	
	// var_dump($c);
	
	// echo '<hr>INT: ';
	// $p = new PEG('123', $c);
	// var_dump($p->parse_literal());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>FLOAT: ';
	// $p = new PEG('123.5', $c);
	// var_dump($p->parse_literal());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>BOOL: ';
	// $p = new PEG('false', $c);
	// var_dump($p->parse_literal());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>BOOL: ';
	// $p = new PEG('tRuE', $c);
	// var_dump($p->parse_literal());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>STRING: ';
	// $p = new PEG('"coooollllll"', $c);
	// var_dump($p->parse_literal());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>VARIABLE: ';
	// $p = new PEG('$news:title', $c);
	// var_dump($p->parse_variable());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	// echo '<hr>ID: ';
	// $p = new PEG('#foo', $c);
	// var_dump($p->parse_variable());
	// echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
	echo '<hr>OPBOOL: ';
	$p = new PEG('(5==$num) ^^ true', $c);
	var_dump($p->parse_opbool());
	echo '<p><small>Stack: '.$p->buffer.'</small></p>';
	
?>