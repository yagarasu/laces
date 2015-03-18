<?php

	require 'laces/Laces.class.php';

	$l = new Laces(array(
		'foo'	=> 'FOO<hr>OOOO',
		'bar'	=> array(
			'baz' => 'BAAAZZZ',
			'sql' => "SELEC<b>T</b> ''-- \x00 \n dasd \x00 \n \r \\ \x1a "
		),
		'attrs' => 'y me dijo "woooo"...'
	));

	$l->render('
		{{{ LacesTemplate author="yagarasu" language="es_MX" }}}
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore impedit rem placeat architecto, deserunt nihil quis tempora facere. Aliquam non numquam deleniti iusto dolore accusantium, veniam, tenetur recusandae obcaecati modi!
		~{{ $foo | html }}~

		~{{ $bar:baz | html }}~

		~{{ $bar:sql | mysql | html }}~

		"~{{ $attrs | attr }}~"

		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas, similique, temporibus. Pariatur explicabo labore velit dolorum temporibus in tempora, nostrum dolorem at error nisi dolore fuga numquam officiis unde veniam!
	');

?>