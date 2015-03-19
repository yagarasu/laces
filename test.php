<?php

	require 'laces/Laces.class.php';
	
	$l = new Laces();
	
	$l->render('
		{{{ LacesTemplate language="es_MX" author="yo mero" }}}
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. 

		~{ include#foo src="tesst.tpl" parse="false" | html }~

		Querida, ~{{ $nombre }}. Te escribo este ~{{ $fecha }}~

		Eos atque amet, excepturi voluptate illo rerum culpa incidunt odit tempore officiis neque, doloremque inventore sunt voluptas, dolor, nesciunt aliquid architecto maxime!
	');

?>