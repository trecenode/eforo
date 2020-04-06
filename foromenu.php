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

$ePiel->cargar(array(
'menu' => $conf['plantilla'].'foromenu.pta'
)) ;
# * Men� de navegaci�n del foro
$ePiel->variable('subforo_indice_url',"$u[0]foro$u[1]$u[5]") ;
if(!empty($_GET['foro'])) {
	$con = $conectar->query("select foro from eforo_foros where id='{$_GET['foro']}'") ;
	$datos = mysqli_fetch_row($con) ;
	$titulo_subforo = $datos[0] ; # --> Se utilizar� tambi�n en los archivos forotemas.php y foromensajes.php
	$ePiel->variables_bloque('menu_subforo',array(
	'url' => "$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]",
	'titulo' => $titulo_subforo
	)) ;
	mysqli_free_result($con) ;
	$ePiel->variables_bloque('nuevo_tema',array(
	'nuevo_tema_url' => "$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]"
	)) ;
	if(!empty($_GET['tema'])) {
		$con = $conectar->query("select tema from eforo_mensajes where id='{$_GET['tema']}'") ;
		$datos = mysqli_fetch_row($con) ;
		$titulo_tema = $datos[0] ; # --> Se utilizar� tambi�n en los archivos foromensajes.php y foroescribir.php
		$ePiel->variables_bloque('menu_subforo.menu_tema',array(
		'url' => "$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[5]",
		'titulo' => $titulo_tema
		)) ;
		mysqli_free_result($con) ;
		$ePiel->variables_bloque('nuevo_tema.responder',array(
		'responder_url' => "$u[0]foroescribir$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[5]"
		)) ;
	}
}
if(!$c_id) {
	$ePiel->variables_bloque('anonimo',array(
	'nuevo_usuario_e' => "$u[0]forousuario$u[1]$u[2]que$u[4]registrar",
	'recuperar_contrasena_e' => "$u[0]forousuario$u[1]$u[2]que$u[4]contrasena"
	)) ;
}
else {
	$url_privados = "$u[0]foroprivados$u[1]$u[5]" ;
	$ePiel->variables_bloque('usuario',array(
	'usuario_nick' => $c_nick,
	'url_privados' => $url_privados,
	'url_perfil' => "$u[0]forousuario$u[1]$u[2]que$u[4]perfil$u[5]",
	'url_leidos' => "$u[0]foro$u[1]$u[2]leidos$u[4]1$u[5]",
	'url_panel' => in_array($c_id,$conf['admin_id']) ? "<option value=\"eforo_admin/index.php\">Panel de control</option>" : ''
	)) ;
	$con = $conectar->query("select count(id) from eforo_privados where leido='0' and id_destinatario='$c_id'") ;
	if($p_nuevos = mysqli_result($con,0,0)) {
		$ePiel->variables_bloque('usuario.nuevos_mensajes',array(
		'url_privados' => $url_privados,
		'total' => $p_nuevos
		)) ;
	}
	mysqli_free_result($con) ;
}
?>