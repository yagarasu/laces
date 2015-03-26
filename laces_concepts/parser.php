<?php

	require 'laces/Laces.class.php';
	
	$c = new Context();
	$c->set('$title','Prueba de Laces.');
	$c->set('$nombre','Alexys <em>Yagarasu</em>');
	$c->set('#foo','Alexys <em>Yagarasu</em>');
	
	$c->set('$news:title','Lorem ipsum dolor sit amet');
	
	$c->set('$sNews', 'FUUUUUU');
	
	$c->set('$num', 5);
	
	if(!isset($_POST['parse'])) {
		//form
		echo '
		
		<form method="post" action="parser.php">
		<textarea name="parse"></textarea>
		<button type="submit">Parse</button>
		</form>
		<p>Accepts: '.htmlentities('Math: + - * / % ^ ; Boolean: && || ^^ ; Comparation == != >= <= > < ; Nesting: (  ) ; Variables. Use $num to test.') . '</p>
		
		';
	} else {
		$e = (!empty($_POST['parse'])) ? $_POST['parse'] : '   2 + 2   ';
		$p = new Expression($e, $c);
		echo '<p>Parse: ' . htmlentities($e) . '</p>';
		var_dump($p->parse());
		echo '<hr>';
		var_dump($p->getContext());
	}
	
?>