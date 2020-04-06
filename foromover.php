<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus usuarios convivan y se sientan parte de tu web
Copyright � 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

require 'foroconfig.php' ;
require 'eforo_funciones/aviso.php' ;
require 'eforo_funciones/sesion.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de moderaci�n � Mover',
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
# * Temas a mover desde el panel de moderadores
if(!empty($_POST['id_foro']) && preg_match('^[0-9]+$',$_POST['id_foro'])) {
	if(!$es_moderador) {
		aviso('Error','<p>Tu no puedes mover temas.',1) ;
	}
	$con = $conectar->query("select count(id) from eforo_foros where id='{$_POST['id_foro']}'") ;
	if(!mysqli_result($con,0,0)) {
		aviso('Error','<p>El subforo seleccionado no existe.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
	}
	mysqli_free_result($con) ;
	$temas_sel = false ;
	foreach($_POST as $a => $b) {
		if(preg_match('^id_tema[0-9]+$',$a)) {
			$temas_sel = true ;
			# * Se obtiene el n�mero de mensajes (el tema junto con las respuestas)
			$con = $conectar->query("select num_respuestas from eforo_mensajes where id='$b'") ;
			$num_mensajes = mysqli_result($con,0,0) + 1 ;
			mysqli_free_result($con) ;
			# * Se restan los temas y mensajes del subforo anterior
			$conectar->query("update eforo_foros set num_temas=num_temas-1,num_mensajes=num_mensajes-$num_mensajes where id='{$_GET['foro']}'") ;
			# * Se suma el n�mero de temas y mensajes del nuevo subforo
			$conectar->query("update eforo_foros set num_temas=num_temas+1,num_mensajes=num_mensajes+$num_mensajes where id='{$_POST['id_foro']}'") ;
			$conectar->query("update eforo_mensajes set id_foro='{$_POST['id_foro']}' where id_tema='$b'") ;
		}
	}
	if(!$temas_sel) {
		aviso('Error','<p>Debes seleccionar al menos un tema.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
	}
	aviso('Temas movidos',"<p>Los temas han sido movidos al subforo indicado.<p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_POST['id_foro']}$u[5]\" class=\"eforo_enlace\">� Ir a este subforo</a><p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar</a>") ;
}
else {
	aviso('Error','No se han indicado temas para mover.',1) ;
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>