<?php

	require 'laces/Laces.class.php';
	
	$l = new Laces();
	
	$l->loadAndRender('test.ltp');
	
	// $l->render('
	// 	{{{ LacesTemplate language="es_MX" author="yo mero mero sabor ranchero" }}}
	// 	Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
		
	// 	INC START
	// 	~{ include#myInclude src="test.ltp" parse="true" | html }~
	// 	INC END

	// 	~{ include#myInclude | html }~

	// 	Eos atque amet, excepturi voluptate illo rerum culpa incidunt odit tempore officiis neque, doloremque inventore sunt voluptas, dolor, nesciunt aliquid architecto maxime!
	// ');

?>