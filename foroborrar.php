<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2006
*** Sitio web: www.13node.com
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
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
if(!$es_usuario) aviso('Error','S�lo los usuarios registrados pueden borrar mensajes. Intenta iniciar sesi�n desde el men�.',1) ;
# * Comprobar permiso de usuario (si es que no es moderador o administrador)
if(!$es_moderador) permiso('p_borrar') ;
# * Mensajes a borrar desde el panel de moderadores
if(isset($_POST['temas_borrar'])) {
	if(!$es_moderador) aviso('Error','<p>T� no puedes borrar temas.',1) ;
	$temas_sel = false ;
	foreach($_POST as $a => $b) {
		if(ereg('^id_tema[0-9]+$',$a)) {
			$temas_sel = true ;
			$con = $conectar->query("select id,id_usuario from eforo_mensajes where id_tema='$b' order by id asc") ;
			while($datos = mysqli_fetch_assoc($con)) {
				$conectar->query("update $tabla_usuarios set mensajes=mensajes-1 where id='{$datos['id_usuario']}'") ;
				$con2 = $conectar->query("select id from eforo_adjuntos where id_mensaje='{$datos['id']}'") ;
				$datos2 = mysqli_fetch_assoc($con2) ;
				if(file_exists("eforo_adjuntos/{$datos2['id']}.dat")) unlink("eforo_adjuntos/{$datos2['id']}.dat") ;
				mysqli_free_result($con2) ;
				$conectar->query("delete from eforo_adjuntos where id_mensaje='{$datos['id']}'") ;
			}
			$total_borrados = mysqli_num_rows($con) ;
			mysqli_free_result($con) ;
			$conectar->query("delete from eforo_mensajes where id_tema='$b'") ;
			$conectar->query("update eforo_foros set num_temas=num_temas-1,num_mensajes=num_mensajes-$total_borrados where id='{$_GET['foro']}'") ;
		}
	}
	if(!$temas_sel) aviso('Error','<p>Debes seleccionar al menos un tema.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
	aviso('Temas borrados',"<p>Los temas y todos sus mensajes han sido borrados.<p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
}
else {
	# * Comprobar si el mensaje a borrar se trata de un tema o de una respuesta
	$tema_inicial = false ;
	if(!empty($_GET['foro']) && !empty($_GET['tema']) && !empty($_GET['mensaje'])) {
		$con = $conectar->query("select count(id) from eforo_mensajes where id=id_tema and id='{$_GET['mensaje']}'") ;
		$tema_inicial = mysqli_result($con,0,0) ? 1 : 2 ;
		mysqli_free_result($con) ;
	}
	switch($tema_inicial) {
		# * Eliminar un tema completo
		case 1 :
			# --> Se comprueba si el tema pertenece al usuario
			$con = $conectar->query("select id_usuario from eforo_mensajes where id='{$_GET['tema']}'") ;
			$datos = mysqli_fetch_row($con) ;
			if($datos[0] != $c_id && !$es_moderador) aviso('Error','<p>T� no puedes borrar este tema.',1) ;
			mysqli_free_result($con) ;
			$con = $conectar->query("select id,id_usuario from eforo_mensajes where id_tema='{$_GET['tema']}' order by id asc") ;
			while($datos = mysqli_fetch_row($con)) {
				$conectar->query("update $tabla_usuarios set mensajes=mensajes-1 where id='$datos[1]'") ;
				$con2 = $conectar->query("select id from eforo_adjuntos where id_mensaje='$datos[0]'") ;
				$datos2 = mysqli_fetch_row($con2) ;
				if(file_exists("eforo_adjuntos/$datos2[0].dat")) unlink("eforo_adjuntos/$datos2[0].dat") ;
				mysqli_free_result($con2) ;
				$conectar->query("delete from eforo_adjuntos where id_mensaje='$datos[0]'") ;
			}
			$total_borrados = mysqli_num_rows($con) ;
			mysqli_free_result($con) ;
			$conectar->query("delete from eforo_mensajes where id_tema='{$_GET['tema']}'") ;
			$conectar->query("update eforo_foros set num_temas=num_temas-1,num_mensajes=num_mensajes-$total_borrados where id='{$_GET['foro']}'") ;
			aviso('Tema borrado',"<p>El tema y todos sus mensajes han sido borrados.<p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
			break ;
		# * Eliminar un mensaje
		case 2 :
			# --> Se comprueba si el mensaje pertenece al usuario
			$con = $conectar->query("select id_usuario from eforo_mensajes where id='{$_GET['mensaje']}' limit 1") ;
			$datos = mysqli_fetch_row($con) ;
			if($datos[0] != $c_id && !$es_moderador) aviso('Error','<p>Tu no puedes borrar este mensaje.',1) ;
			$conectar->query("update $tabla_usuarios set mensajes=mensajes-1 where id='$datos[0]'") ;
			mysqli_free_result($con) ;
			$con = $conectar->query("select id from eforo_adjuntos where id_mensaje='{$_GET['mensaje']}'") ;
			$datos = mysqli_fetch_row($con) ;
			if(file_exists("eforo_adjuntos/$datos[0].dat")) unlink("eforo_adjuntos/$datos[0].dat") ;
			mysqli_free_result($con) ;
			$conectar->query("delete from eforo_adjuntos where id_mensaje='{$_GET['mensaje']}'") ;
			$conectar->query("delete from eforo_mensajes where id='{$_GET['mensaje']}'") ;
			$conectar->query("update eforo_mensajes set num_respuestas=num_respuestas-1 where id='{$_GET['tema']}'") ;
			$conectar->query("update eforo_foros set num_mensajes=num_mensajes-1 where id='{$_GET['foro']}'") ;
			aviso('Mensaje borrado',"<p>El mensaje ha sido borrado.<p><a href=\"$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]pag$u[4]{$_GET['pag']}$u[5]\" class=\"eforo_enlace\">� Regresar al tema</a><p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
	}
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>