<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2006
*** Sitio web: www.13node.com
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus visitantes convivan y se sientan parte de tu web
Copyright � 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

require 'foroconfig.php' ;
require 'eforo_funciones/quitar.php' ;
require 'eforo_funciones/aviso.php' ;
require 'eforo_funciones/sesion.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'foroescribir' => $conf['plantilla'].'foroescribir.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
# * Comportamiento del formulario (escribir, responder o editar un mensaje)
switch(true) {
	# Escribir nuevo tema
	case !empty($_GET['foro']) && empty($_GET['tema']) && empty($_GET['mensaje']) :
		$que = 1 ;
		$permiso = 'p_nuevo' ;
		$form_titulo = 'Escribir nuevo tema' ;
		break ;
	# Responder al tema
	case !empty($_GET['foro']) && !empty($_GET['tema']) && empty($_GET['mensaje']) :
		$que = 2 ;
		$permiso = 'p_responder' ;
		$form_titulo = 'Responder al tema' ;
		break ;
	# Editar el mensaje
	case !empty($_GET['foro']) && !empty($_GET['tema']) && !empty($_GET['mensaje']) :
		$que = 3 ;
		$permiso = 'p_editar' ;
		$form_titulo = 'Editar el mensaje' ;
		break ;
	default :
		aviso('Error','No se ha escrito ning�n mensaje.',1) ;
}
# * Comprobar permiso de usuario si el usuario no es administrador o moderador
if(!$es_moderador) permiso($permiso) ;
# * Rellenar el formulario
# --> Opciones por defecto
$con = $conectar->query("select p_importante from eforo_foros where id='{$_GET['foro']}'") ;
$datos = mysqli_fetch_row($con) ;
$p_importante = $datos[0] ;
mysqli_free_result($con) ;
if(empty($_GET['vistaprevia'])) {
	if($que != 3) {
		$form_tema = '' ;
		$form_mensaje = '' ;
		$form_caretos = $conf['permitir_caretos'] ? true : false ;
		$form_codigo = $conf['permitir_codigo'] ? true : false ;
		$form_firma = $conf['permitir_firma'] ? true : false ;
		$form_imp = false ;
		$form_not = false ;
	}
	else {
		$con = $conectar->query("select * from eforo_mensajes where id='{$_GET['mensaje']}'") ;
		$datos = mysqli_fetch_assoc($con) ;
		if($datos['id_usuario'] != $c_id && !$es_moderador) aviso('Error','<p>Tu no puedes editar este mensaje.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
		$form_tema = $datos['tema'] ;
		$form_mensaje = $datos['mensaje'] ;
		$form_caretos = $datos['o_caretos'] ;
		$form_codigo = $datos['o_codigo'] ;
		$form_firma = $datos['o_firma'] ;
		$form_imp = $datos['o_importante'] ;
		$form_not = $datos['o_notificacion'] ;
		mysqli_free_result($con) ;
	}
}
else {
	# Vista previa del mensaje
	require 'eforo_funciones/codigo.php' ;
	if($que != 3) {
		$autor_nick = ($autor_nick = usuario($c_id)) ? "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]$c_id$u[5]\" target=\"_blank\" class=\"eforo_enlace\">$autor_nick</a>" : '<i>An�nim@</i>' ;
	}
	else {
		$con = $conectar->query("select id_usuario from eforo_mensajes where id='{$_GET['mensaje']}'") ;
		$datos = mysqli_fetch_row($con) ;
		$autor_nick = ($autor_nick = usuario($datos[0])) ? "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]$datos[0]$u[5]\" target=\"_blank\" class=\"eforo_enlace\">$autor_nick</a>" : '<i>An�nim@</i>' ;
		mysqli_free_result($con) ;
	}
	$form_tema = !empty($_POST['m_tema']) ? quitar($_POST['m_tema'],0,1) : '' ;
	$form_mensaje = !empty($_POST['m_mensaje']) ? quitar($_POST['m_mensaje'],0,1) : '' ;
	$form_caretos = !empty($_POST['m_caretos']) ? true : false ;
	$form_codigo = !empty($_POST['m_codigo']) ? true : false ;
	$form_firma = !empty($_POST['m_firma']) ? true : false ;
	$form_imp = !empty($_POST['m_importante']) ? true : false ;
	$form_not = !empty($_POST['m_notificacion']) ? true : false ;
	$vista_previa_tema = quitar($_POST['m_tema'],0,1) ;
	$vista_previa_mensaje = quitar($_POST['m_mensaje'],0,1) ;
	if($conf['permitir_codigo'] && !empty($_POST['m_codigo'])) $vista_previa_mensaje = codigo($vista_previa_mensaje) ;
	if($conf['permitir_caretos'] && !empty($_POST['m_caretos'])) $vista_previa_mensaje = caretos($vista_previa_mensaje) ;
	if($conf['censurar_palabras']) {
		$vista_previa_tema = censurar($vista_previa_tema) ;
		$vista_previa_mensaje = censurar($vista_previa_mensaje) ;
	}
	$vista_previa_mensaje = nl2br($vista_previa_mensaje) ;
	# --> Bloque vista_previa
	$ePiel->variables_bloque('vista_previa',array(
	'autor_nick' => $autor_nick,
	'tema' => $vista_previa_tema,
	'mensaje' => $vista_previa_mensaje
	)) ;
}
$url_tema = !empty($_GET['tema']) ? "$u[3]tema$u[4]{$_GET['tema']}" : '' ;
$url_mensaje = !empty($_GET['mensaje']) ? "$u[3]mensaje$u[4]{$_GET['mensaje']}" : '' ;
# * Citar mensaje
if($que == 2 && !empty($_GET['citar']) && preg_match('^[0-9]+$',$_GET['citar'])) {
	$con = $conectar->query("select id_usuario,mensaje from eforo_mensajes where id='{$_GET['citar']}'") ;
	$datos = mysqli_fetch_row($con) ;
	$form_mensaje = '[citar autor='.usuario($datos[0]).']'."\r\n".$datos[1]."\r\n".'[/citar]' ;
	mysqli_free_result($con) ;
	# --> Bloque citar
	$ePiel->variables_bloque('citar') ;
}
$url_pag = !empty($_GET['pag']) ? "$u[3]pag$u[4]{$_GET['pag']}$u[5]" : '' ;
$ePiel->variables(array(
'url_vista_previa' => "$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$url_tema$url_mensaje$url_pag$u[3]vistaprevia$u[4]1$u[5]",
'url_escribir' => "$u[0]foroescribirpro$u[1]$u[2]foro$u[4]{$_GET['foro']}$url_tema$url_mensaje$url_pag$u[5]",
'que' => $que,
'form_titulo' => $form_titulo,
'form_tema' => $form_tema,
'form_mensaje' => $form_mensaje
)) ;
# * Borrar archivo adjunto
if(!empty($_GET['borraradjunto'])) {
	$con = $conectar->query("select id from eforo_adjuntos where id_mensaje='{$_GET['mensaje']}'") ;
	$datos = mysqli_fetch_row($con) ;
	$conectar->query("delete from eforo_adjuntos where id='$datos[0]'") ;
	mysqli_free_result($con) ;
	unlink("eforo_adjuntos/$id_adjunto.dat") ;
}
# * Comprobar si el usuario tiene permiso para adjuntar archivos
$adjuntar = false ;
$con = $conectar->query("select p_adjuntar from eforo_foros where id='{$_GET['foro']}'") ;
if($usuario['rango'] >= mysqli_result($con,0,0) || $es_moderador) {
	$adjuntar = true ;
	$adjuntar_titulo = '<b>Adjuntar archivo (M�x. '.$conf['adjunto_tamano'].' KB):</b><br />Anexar un archivo a tu mensaje.' ;
	$adjuntar_contenido = '<input type="file" name="m_archivo" size="50" class="eforo_formulario" />' ;
	# --> Comprobar si ya hay un archivo adjunto (s�lo al editar el mensaje)
	if($que == 3) {
		$con = $conectar->query("select archivo from eforo_adjuntos where id_mensaje='{$_GET['mensaje']}' limit 1") ;
		if(mysqli_num_rows($con)) {
			$datos = mysqli_fetch_row($con) ;
			$adjuntar_titulo = '<b>Archivo adjunto:</b><br />Nombre del archivo adjunto.' ;
			$adjuntar_contenido = "<b>$datos[0]</b><br /><br /><input type=\"button\" value=\"Borrar\" onclick=\"location='$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$url_tema$url_mensaje$u[5]'\" class=\"eforo_formulario\" />" ;
		}
	}
}
mysqli_free_result($con) ;
if($adjuntar) {
	$ePiel->variables_bloque('adjuntar',array(
	'titulo' => $adjuntar_titulo,
	'contenido' => $adjuntar_contenido
	)) ;
}
# --> Ver si las casillas han sido seleccionadas
$ePiel->variables(array(
'm_caretos_s' => $form_caretos ? ' checked="checked"' : '',
'm_codigo_s' => $form_codigo ? ' checked="checked"' : '',
'm_firma_s' => $form_firma ? ' checked="checked"' : '',
'm_importante_s' => $form_imp ? ' checked="checked"' : '',
'm_notificacion_s' => $form_not ? ' checked="checked"' : ''
)) ;
# --> Deshabilitar las casillas si no est�n permitidas en la configuraci�n o si no se van a utilizar
$ePiel->variables(array(
'm_caretos_e' => !$conf['permitir_caretos'] ? ' disabled="disabled"' : '',
'm_codigo_e' => !$conf['permitir_codigo'] ? ' disabled="disabled"' : '',
'm_firma_e' => !$conf['permitir_firma'] ? ' disabled="disabled"' : '',
'm_importante_e' => $usuario['rango'] < $p_importante && !$es_moderador ? ' disabled="disabled"' : '',
'm_notificacion_e' => $que == 2 || ($que == 3 && $_GET['tema'] != $_GET['mensaje']) || !$conf['notificacion_email'] || !$es_usuario ? ' disabled="disabled"' : '',
)) ;
# * Si el formulario est� en modo responder se muestran los �ltimos mensajes del tema
if($que == 2) {
	require_once 'eforo_funciones/codigo.php' ;
	$con = $conectar->query("select tema from eforo_mensajes where id='{$_GET['tema']}'") ;
	$titulo_tema = mysqli_result($con,0,0) ;
	mysqli_free_result($con) ;
	$ePiel->variables_bloque('ult_mensajes',array(
	'total' => $conf['max_ultimos']
	)) ;
	$estilo_num = 1 ;
	$con = $conectar->query("select * from eforo_mensajes where id_tema='{$_GET['tema']}' order by id desc limit {$conf['max_ultimos']}") ;
	while($datos = mysqli_fetch_assoc($con)) {
		if($autor_nick = usuario($datos['id_usuario'])) {
			$autor_nick = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id_usuario']}$u[5]\" target=\"_blank\" class=\"eforo_enlace\">$autor_nick</a>" ;
		}
		else {
			$autor_nick = '<i>An�nim@</i>' ;
		}
		$datos['tema'] = !$datos['tema'] ? 'RE: '.$titulo_tema : $datos['tema'] ;
		if($conf['permitir_caretos'] && $datos['o_caretos']) $datos['mensaje'] = caretos($datos['mensaje']) ;
		if($conf['permitir_codigo'] && $datos['o_codigo']) $datos['mensaje'] = codigo($datos['mensaje']) ;
		if($conf['censurar_palabras']) {
			$datos['tema'] = censurar($datos['tema']) ;
			$datos['mensaje'] = censurar($datos['mensaje']) ;
		}
		$datos['mensaje'] = nl2br($datos['mensaje']) ;
		$ePiel->variables_bloque('ult_mensajes.mensaje',array(
		'estilo_num' => $estilo_num,
		'autor_nick' => $autor_nick,
		'tema' => $datos['tema'],
		'fecha' => fecha($datos['fecha']),
		'mensaje' => $datos['mensaje']
		)) ;
		if($datos['fecha_editado'] > $datos['fecha']) {
			$ePiel->variables_bloque('ult_mensajes.mensaje.editado',array(
			'fecha' => fecha($datos['fecha_editado'])
			)) ;
		}
		$estilo_num = $estilo_num == 1 ? 2 : 1 ;
	}
}
$ePiel->mostrar('foroescribir') ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>