<?php
/**
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

$ePiel->cargar([
	'menu' => $conf['plantilla'].'foromenu.pta'
]);
$ePiel->variable('subforo_indice_url', $u[0].'foro'.$u[1].$u[5]);

// Limpiar el ID del foro
$idForo = @intval($_GET['foro']);

// Menú de navegación del foro
if(!empty($idForo)) {
	if($buscar = $conectar->query("SELECT `foro` FROM `eforo_foros` WHERE `id`='{$idForo}'")) {
		$datos = $buscar->fetch_assoc();
		$titulo_subforo = $datos['foro']; // Se utilizará también en los archivos forotemas.php y foromensajes.php
		$ePiel->variables_bloque('menu_subforo', [
			'url'		=> $u[0].'forotemas'.$u[1].$u[2].'foro'.$u[4].$idForo.$u[5],
			'titulo'	=> $titulo_subforo
		]);
		$buscar->free();
	}

	$ePiel->variables_bloque('nuevo_tema', [
		'nuevo_tema_url' => $u[0].'foroescribir'.$u[1].$u[2].'foro'.$u[4].$idForo.$u[5]
	]);

	//Limpiar ID Tema
	$idTema = intval($_GET['tema']);

	if(!empty($idTema)) {
		if($buscar = $conectar->query("SELECT `tema` FROM `eforo_mensajes` WHERE `id`='{$idTema}'")) {
			$datos = $buscar->fetch_assoc();
			$titulo_tema = $datos['tema']; // Se utilizará también en los archivos foromensajes.php y foroescribir.php
			$ePiel->variables_bloque('menu_subforo.menu_tema', [
				'url'		=> $u[0].'foromensajes'.$u[1].$u[2].'foro'.$u[4].$idForo.$u[3].'tema'.$u[4].$idTema.$u[5],
				'titulo'	=> $titulo_tema
			]);
			$buscar->free();
		}
		$ePiel->variables_bloque('nuevo_tema.responder', [
			'responder_url' => $u[0].'foroescribir'.$u[1].$u[2].'foro'.$u[4].$idForo.$u[3].'tema'.$u[4].$idTema.$u[5]
		]) ;
	}
}

if(!$c_id) {
	$ePiel->variables_bloque('anonimo', [
		'nuevo_usuario_e'			=> $u[0].'forousuario'.$u[1].$u[2].'que'.$u[4].'registrar',
		'recuperar_contrasena_e'	=> $u[0].'forousuario'.$u[1].$u[2].'que'.$u[4].'contrasena'
	]);
}
else {
	$url_privados = $u[0].'foroprivados'.$u[1].$u[5];
	$ePiel->variables_bloque('usuario', [
		'usuario_nick'	=> $c_nick,
		'url_privados'	=> $url_privados,
		'url_perfil'	=> $u[0].'forousuario'.$u[1].$u[2].'que'.$u[4].'perfil'.$u[5],
		'url_leidos'	=> $u[0].'foro'.$u[1].$u[2].'leidos'.$u[4].'1'.$u[5],
		'url_panel'		=> in_array($c_id, $conf['admin_id']) ? '<option value="eforo_admin/index.php">Panel de control</option>' : ''
	]);
	$p_nuevos = $conectar
		->query("SELECT COUNT(`id`) AS `num` FROM `eforo_privados` WHERE `leido`='0' AND `id_destinatario`='{$c_id}'")
		->fetch_object()
		->num;
	$ePiel->variables_bloque('usuario.nuevos_mensajes', [
		'url_privados'	=> $url_privados,
		'total'			=> $p_nuevos
	]);
}
