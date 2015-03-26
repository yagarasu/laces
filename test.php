<?php

	require 'laces/Laces.class.php';
	
	$c = new Context();
	$c->set('$title','Prueba de Laces.');
	$c->set('$nombre','Alexys <em>Yagarasu</em>');
	$c->set('#foo','Alexys <em>Yagarasu</em>');
	
	$c->set('$news', array(
		array( 'title'=>'Article 1' , 'author'=>'foo bar' ),
		array( 'title'=>'Article 2' , 'author'=>'bar baz' ),
		array( 'title'=>'Article 3' , 'author'=>'bar bar' )
	));
	
	$c->set('$sNews', 'FUUUUUU');
	
	$c->set('$num', 5);

	$c->registerHook('CUSTOM_HOOK', function($hook) {
		echo "Do something in a hook hook";
		var_dump($hook);
	});
	
	$l = new Laces($c);
	
	$l->loadAndRender('test.ltp');
	
	echo '<hr><pre>';
	var_dump($c->getRawArray());
	echo '</pre><hr>';

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
	
	// if(!isset($_POST['parse'])) {
	// 	//form
		
	// 	echo '
		
	// 	<form method="post" action="test.php">
	// 	<textarea name="parse"></textarea>
	// 	<button type="submit">Parse</button>
	// 	</form>
	// 	<p>Accepts: '.htmlentities('Math: + - * / % ^ ; Boolean: ! && || ^^ ; Comparation == != >= <= > < ; Nesting: (  ) ; Variables. Use $num to test. "exists" prefix operator.') . '</p>
		
	// 	';
	// } else {
	// 	$e = (!empty($_POST['parse'])) ? $_POST['parse'] : ' 2 + 2';
	// 	$p = new PEG($e, $c);
	// 	echo '<p>Parse: ' . htmlentities($e) . '</p>';
	// 	var_dump($p->parse_opbool());
	// 	echo '<p><small>Buffer: '.$p->buffer.'</small></p>';
	// }
	
?>