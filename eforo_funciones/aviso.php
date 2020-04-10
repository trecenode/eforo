<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus visitantes convivan y se sientan parte de tu web
Copyright © 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

function aviso($titulo,$mensaje,$finalizar = '',$url = '') {
	$ePiel = &$GLOBALS['ePiel'] ;
	$ePiel->cargar(array(
	'aviso' => $url.$GLOBALS['conf']['plantilla'].'aviso.pta'
	)) ;
	$ePiel->variables(array(
	'aviso_titulo' => $titulo,
	'aviso_mensaje' => $mensaje
	)) ;
	$ePiel->mostrar('aviso') ;
	if($finalizar) {
		$ePiel->variable('tiempo_carga',round(tiempo_carga() - $GLOBALS['tiempo'],4)) ;
		$ePiel->mostrar('piedepagina') ;
		exit ;
	}
}
?>
