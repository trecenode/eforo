<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
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
require 'eforo_funciones/aviso.php' ;
require 'eforo_funciones/epaginas.php' ;
require 'eforo_funciones/sesion.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'foroprivados' => $conf['plantilla'].'foroprivados.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
if(!$c_id) aviso('Error','Necesitas iniciar sesi�n para poder revisar tus mensajes privados. Intenta iniciar sesi�n desde el men�.',1) ;
if(isset($_POST['enviar'])) {
	require 'eforo_funciones/quitar.php' ;
	$_POST['p_destinatario'] = quitar($_POST['p_destinatario'],1) ;
	$_POST['p_mensaje'] = quitar($_POST['p_mensaje'],1) ;
	$con = $conectar->query("select id from $tabla_usuarios where nick='{$_POST['p_destinatario']}'") ;
	if(mysqli_num_rows($con)) {
		$datos = mysqli_fetch_row($con) ;
		$con2 = $conectar->query("select count(id) from eforo_privados where id_destinatario='$datos[0]'") ;
		if(mysqli_result($con2,0,0) < $conf['max_privados']) {
			$conectar->query("insert into eforo_privados (fecha,id_remitente,id_destinatario,mensaje) values ('$fecha','$c_id','$datos[0]','{$_POST['p_mensaje']}')") ;
			aviso('Mensaje enviado','El mensaje ha sido enviado a <b>'.$_POST['p_destinatario'].'</b>') ;
		}
		else {
			aviso('Error','La bandeja de <b>'.$_POST['p_destinatario'].'</b> est� llena. No se pudo enviar el mensaje.') ;
		}
		mysqli_free_result($con2) ;
	}
	else {
		aviso('Error','El destinatario <b>'.$_POST['p_destinatario'].'</b> no existe.') ;
	}
	mysqli_free_result($con) ;
}
if(!empty($_GET['borrar']) && preg_match('^[0-9]+$',$_GET['borrar'])) {
	$conectar->query("delete from eforo_privados where id='{$_GET['borrar']}'") ;
	aviso('Mensaje eliminado','El mensaje ha sido eliminado.') ;
}
$ePaginas = new ePaginas("select * from eforo_privados where id_destinatario='$c_id' order by id desc",15) ;
$ePaginas->u = array($u[2],$u[3],$u[4],$u[5]) ;
$ePaginas->e = array('<a href="','" class="eforo_enlace">','</a>') ;
$con = $ePaginas->consultar() ;
$barra_porcentaje = round($ePaginas->total_res / $conf['max_privados'],2) * 100 ;
$ePiel->variables(array(
'url_regresar' => "$u[0]foro$u[1]$u[5]",
'url_privados' => "$u[0]foroprivados$u[1]$u[5]",
'p_destinatario' => !empty($_POST['p_destinatario']) ? $_POST['p_destinatario'] : '',
'p_mensaje' => !empty($_POST['p_mensaje']) ? $_POST['p_mensaje'] : '',
'total_res' => $ePaginas->total_res,
'max_privados' => $conf['max_privados'],
'paginas' => $ePaginas->paginar(),
'barra_porcentaje' => $barra_porcentaje,
'barra_color' => $barra_porcentaje < 90 ? '#00c000' : '#c00000'
)) ;
$estilo_num = 1 ;
while($datos = mysqli_fetch_assoc($con)) {
	$con2 = $conectar->query("select nick,avatar from $tabla_usuarios where id='{$datos['id_remitente']}'") ;
	$datos2 = mysqli_fetch_row($con2) ;
	$ePiel->variables_bloque('mensaje',array(
	'url_remitente' => "$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id_remitente']}$u[5]",
	'remitente' => $datos2[0],
	'fecha' => fecha($datos['fecha']),
	'contenido' => nl2br($datos['mensaje']),
	'url_borrar' => "$u[0]foroprivados$u[1]$u[2]borrar$u[4]{$datos['id']}$u[5]",
	'estilo_num' => $estilo_num
	)) ;
	if($datos2[1]) {
		$ePiel->variables_bloque('mensaje.avatar',array(
		'id' => $datos['id_remitente'],
		'ext' => $datos2[1],
		'alt' => 'Avatar de '.$datos2[0]
		)) ;
	}
	mysqli_free_result($con2) ;
	if(!$datos['leido']) $conectar->query("update eforo_privados set leido='1' where id='$datos[id]'") ;
	$estilo_num = ($estilo_num == 1) ? 2 : 1 ;
}
mysqli_free_result($con) ;
$ePiel->mostrar('foroprivados') ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>