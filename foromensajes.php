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
require 'eforo_funciones/codigo.php' ;
require 'eforo_funciones/epaginas.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'foromensajes' => $conf['plantilla'].'foromensajes.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � '.$titulo_subforo.' � '.$titulo_tema,
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$menu = $ePiel->mostrar('menu',1) ;
echo $menu ;
# * Se comprueba si el usuario tiene permiso para entrar al subforo seleccionado
$con = $conectar->query("select p_leer from eforo_foros where id='{$_GET['foro']}'") ;
$datos = mysqli_fetch_row($con) ;
if($usuario['rango'] < $datos[0]) {
	require 'eforo_funciones/aviso.php' ;
	aviso('Nivel insuficiente','No tienes suficiente nivel para entrar a este subforo. Intenta iniciar sesi�n desde el men�.',1) ;
}
mysqli_free_result($con) ;
# * Se almacenan todos los rangos en un array
$con = $conectar->query('select * from eforo_rangos order by rango asc') ;
while($datos = mysqli_fetch_assoc($con)) {
	$rangos[$datos['rango']] = array($datos['minimo'],$datos['descripcion']) ;
}
mysqli_free_result($con) ;
# * Almacenar lista de usuarios en l�nea en una variable
# Servir� para comprobar si el autor del tema o de alguna respuesta est� conectado.
$usuarios_en_linea = array() ;
$con = $conectar->query("select id_usuario from eforo_enlinea order by fecha asc") ;
while($datos = mysqli_fetch_row($con)) {
	$usuarios_en_linea[] = $datos[0] ;
}
mysqli_free_result($con) ;
# **********************************************
# *** Mostrar los mensajes del tema seleccionado
# **********************************************
$ePaginas = new ePaginas("select * from eforo_mensajes where id_tema='{$_GET['tema']}' order by id asc",$conf['max_mensajes']) ;
$ePaginas->u = array($u[2],$u[3],$u[4],$u[5]) ;
$ePaginas->e = array('<a href="','" class="eforo_enlace">','</a>') ;
$con = $ePaginas->consultar() ;
# * Se suma una visita al tema seleccionado y se elimina del recordatorio
# S�lo se sumar�n visitas si se est� viendo la �ltima p�gina
if($ePaginas->total_pag == $_GET['pag']) {
	$conectar->query("update eforo_mensajes set num_visitas=num_visitas+1 where id='{$_GET['tema']}'") ;
	# Se considera visto el tema y se elimina del recordatorio
	if($c_id) $conectar->query("delete from eforo_recientes where id_usuario='$c_id' and id_mensaje='{$_GET['tema']}'") ;
}
$ePiel->variable('paginas',$ePaginas->paginar()) ;
$estilo_num = 1 ;
while($datos = mysqli_fetch_assoc($con)) {
	# Se comprueba si el mensaje es el tema inicial o una respuesta
	$que = $datos['id'] == $datos['id_tema'] ? 1 : 2 ;
	# Si es el tema inicial se comprueban las notificaciones por email
	if($que == 1) {
		# * Notificaci�n por email cuando haya respuestas
		# Si el autor ya recibi� una notificaci�n y no ha visitado su tema, se desactivan, as� que volver�n
		# a ser activadas hasta que visite su tema.
		if($conf['notificacion_email'] && $datos['id_usuario'] == $c_id && $datos['o_notificacion'] && !$datos['o_notificacion_email']) {
			$conectar->query("update eforo_mensajes set o_notificacion_email='1' where id='{$_GET['tema']}'") ;
		}
	}
	# Se obtienen los datos del autor del mensaje
	$con2 = $conectar->query("select nick,avatar,mensajes,rango,rango_fijo from $tabla_usuarios where id='{$datos['id_usuario']}'") ;
	if(mysqli_num_rows($con2)) {
		$autor_existe = true ;
		$datos2 = mysqli_fetch_assoc($con2) ;
		# --> Nick
		$autor_nick = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id_usuario']}$u[5]\" class=\"eforo_enlace\">{$datos2['nick']}</a>" ;
		# --> Rango (se utiliza el array $rangos creado m�s arriba)
		if($datos2['rango_fijo']) {
			$autor_rango = $rangos[$datos2['rango']][1] ;
		}
		else {
			$autor_rango = $rangos[1][1] ;
			foreach($rangos as $rango) {
				if($rango[0] != 0 && $datos2['mensajes'] >= $rango[0]) $autor_rango = $rango[1] ;
			}
		}
		# --> Total de mensajes enviados
		$autor_mensajes = $datos2['mensajes'] ;
		# --> Avatar (extensi�n de la imagen)
		$autor_avatar = $datos2['avatar'] ;
		$autor_avatar_alt = $datos2['nick'] ? 'Avatar de '.$datos2['nick'] : '' ;
		# --> Estado del autor
		$autor_estado = in_array($datos['id_usuario'],$usuarios_en_linea) ? 'Conectad@' : 'Desconectad@' ;
	}
	else {
		$autor_existe = false ;
		# --> Si el autor tiene una ID diferente de cero pero no existe en la base de datos entonces se muestra como "Eliminad@"
		$autor_nick = !$datos['id_usuario'] ? '<i>An�nim@</i>' : '<i>Eliminad@</i>' ;
		$autor_rango = '' ;
		$autor_mensajes = '' ;
		$autor_avatar = '' ;
		$autor_avatar_alt = '' ;
		$autor_estado = '' ;
	}
	mysqli_free_result($con2) ;
	# Se agrega el t�tulo del tema por defecto si la respuesta no tiene
	if(!$datos['tema']) $datos['tema'] = 'RE: '.$titulo_tema ;
	# Se aplican las funciones especiales seg�n la configuraci�n del foro y si el autor lo desea
	# --> C�digo especial
	if($conf['permitir_codigo'] && $datos['o_codigo']) $datos['mensaje'] = codigo($datos['mensaje']) ;
	# --> Caretos
	if($conf['permitir_caretos'] && $datos['o_caretos']) $datos['mensaje'] = caretos($datos['mensaje']) ;
	# --> Censurar palabras (s�lo modificable a trav�s de la configuraci�n del foro)
	if($conf['censurar_palabras']) {
		$datos['tema'] = censurar($datos['tema']) ;
		$datos['mensaje'] = censurar($datos['mensaje']) ;
	}
	# --> Sustituir saltos de l�nea por c�digo HTML
	$datos['mensaje'] = nl2br($datos['mensaje']) ;
	$ePiel->variables_bloque('mensaje',array(
	'id' => $datos['id'],
	'estilo_num' => $estilo_num,
	'autor_id' => $datos['id_usuario'],
	'autor_nick' => $autor_nick,
	'tema' => $datos['tema'],
	'contenido' => $datos['mensaje'],
	'fecha' => fecha($datos['fecha']),
	'que' => $que,
	'url_editar' => "$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]mensaje$u[4]{$datos['id']}$u[3]pag$u[4]{$_GET['pag']}$u[5]",
	'url_borrar' => "$u[0]foroborrar$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]mensaje$u[4]{$datos['id']}$u[3]pag$u[4]{$_GET['pag']}$u[5]",
	'url_citar' => "$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]citar$u[4]{$datos['id']}$u[5]"
	)) ;
	# --> S�lo se muestran los datos del autor si este existe en la base de datos de otra forma s�lo se mostrar� "An�nimo" o "Eliminad@"
	if($autor_existe) {
		$ePiel->variables_bloque('mensaje.usuario',array(
		'autor_rango' => $autor_rango,
		'autor_mensajes' => $autor_mensajes,
		'autor_estado' => $autor_estado
		)) ;
		# --> Se muestra el avatar del usuario (s�lo si lo ha subido)
		if($autor_avatar) {
			$ePiel->variables_bloque('mensaje.usuario.avatar',array(
			'autor_id' => $datos['id_usuario'],
			'ext' => $autor_avatar,
			'alt' => $autor_avatar_alt
			)) ;
		}
	}
	# --> Se muestra la firma si est� creada en el perfil
	if($conf['permitir_firma'] && $datos['o_firma'] && $autor_existe) {
		$con2 = $conectar->query("select firma from $tabla_usuarios where id='{$datos['id_usuario']}'") ;
		$datos2 = mysqli_fetch_row($con2) ;
		if($datos2[0]) {
			if($conf['censurar_palabras']) censurar($datos2[0]) ;
			$ePiel->variables_bloque('mensaje.firma',array(
			'contenido' => nl2br(codigo($datos2[0]))
			)) ;
		}
		mysqli_free_result($con2) ;
	}
	# --> Si el mensaje ha sido editado se muestra la fecha de la �ltima vez que se edit�
	if($datos['fecha_editado'] > $datos['fecha']) {
		$ePiel->variables_bloque('mensaje.editado',array(
		'fecha' => fecha($datos['fecha_editado'])
		)) ;
	}
	# --> Si el mensaje tiene un archivo adjunto se muestra para poder descargarlo
	$con2 = $conectar->query("select id,archivo from eforo_adjuntos where id_mensaje='{$datos['id']}' limit 1") ;
	if(mysqli_num_rows($con2)) {
		$datos2 = mysqli_fetch_row($con2) ;
		$ePiel->variables_bloque('mensaje.adjunto',array(
		'url' => 'foroadjuntos.php?id='.$datos2[0],
		'archivo' => $datos2[1]
		)) ;
	}
	mysqli_free_result($con2) ;
	$estilo_num = ($estilo_num == 1) ? 2 : 1 ;
}
mysqli_free_result($con) ;
$ePiel->variable('url_escribir',"$u[0]foroescribirpro$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[5]") ;
$con = $conectar->query("select p_responder from eforo_foros where id='{$_GET['foro']}'") ;
$datos = mysqli_fetch_row($con) ;
if($usuario['rango'] >= $datos[0]) $ePiel->variables_bloque('respuesta_r') ;
mysqli_free_result($con) ;
$ePiel->mostrar('foromensajes') ;
echo $menu ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>