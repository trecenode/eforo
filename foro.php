<?php
/**
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

require 'foroconfig.php';

// Marcar los subforos como le�dos
if(!empty($_GET['leidos'])) {
	$conectar->query("DELETE FROM `eforo_recientes` WHERE `id_usuario`='{$c_id}'");
}

require 'eforo_funciones/recientes.php';

$ePiel->cargar([
	'cabecera'		=> $conf['plantilla'].'cabecera.pta',
	'foro'			=> $conf['plantilla'].'foro.pta',
	'piedepagina'	=> $conf['plantilla'].'piedepagina.pta'
]);

require 'foromenu.php';

$ePiel->variables([
	'titulo' => $conf['foro_titulo'],
	'estilo' => $conf['estilo']
]);

$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;

// Mostrar todos los subforos (sección principal)
$buscar = $conectar->query("SELECT `id`,`categoria` FROM `eforo_categorias` ORDER BY `orden` ASC");
while($cat = $buscar->fetch_assoc()) {
	$ePiel->variables_bloque('categoria', [
		'titulo' => $cat['categoria']
	]);
	$buscar2 = $conectar->query("SELECT `id`,`foro`,`descripcion`,`num_temas`,`num_mensajes` FROM `eforo_foros` WHERE `id_categoria`='{$datos['id']}' ORDER BY `orden` ASC");
	$estilo_num = 1;
	while($foros = $buscar2->fetch_assoc()) {
		// Buscar mensajes en el recordatorio (sólo usuarios)
		$ind_imagen  = 'foco_apagado.gif';
		$ind_mensaje = 'No hay mensajes nuevos';
		if($c_id) {
			$rec = $conectar
				->query("SELECT COUNT(`id_usuario`) AS `num` FROM `eforo_recientes` WHERE `id_usuario`='{$c_id}' AND `id_foro`='{$foros['id']}' LIMIT 1")
				->fetch_object()
				->num;
			if($rec) {
				$ind_imagen = 'foco_encendido.gif';
				$ind_mensaje = 'Hay mensajes nuevos';
			}
		}
		// Obtener último mensaje enviado en cada subforo
		$busUlt = $conectar->query("SELECT `id`,`id_tema`,`id_usuario`,`fecha` FROM `eforo_mensajes` WHERE `id_foro`='{$foros['id']}' ORDER BY `fecha` DESC LIMIT 1");
		if($busUlt->num_rows) {
			$ult = $busUlt->fetch_assoc();
			if($ult['id_usuario']) {
				$autor = ($autor = usuario($ult['id_usuario']))
					? "<a href=\"{$u[0]}forousuarios{$u[1]}{$u[2]}u{$u[4]}{$ult['id_usuario']}{$u[5]}\" class=\"eforo_enlace\"{>$autor}</a>"
					: '<i>Eliminad@</i>';
			}
			else {
				$autor = '<i>An�nim@</i>';
			}
			$ult_mensaje = $autor." <a href=\"{$u[0]}foromensajes{$u[1]}{$u[2]}foro{$u[4]}{$foros['id']}{$u[3]}tema{$u[4]}{$ult['id_tema']}{$u[5]}#{$ult['id']}\" class=\"eforo_enlace\">�</a><br />".fecha($ult['fecha']);
		}
		else {
			$ult_mensaje = '<b>No hay mensajes</b>';
		}
		$busUlt->free();
	
		$ePiel->variables_bloque('categoria.subforo', [
			'ind_imagen'	=> $conf['plantilla'].'imagenes/'.$ind_imagen,
			'ind_mensaje'	=> $ind_mensaje,
			'titulo_url'	=> $u[0].'forotemas'.$u[1].$u[2].'foro'.$u[4].$foros['id'].$u[5],
			'titulo'		=> $foros['foro'],
			'descripcion'	=> $foros['descripcion'],
			'ult_mensaje'	=> $ult_mensaje,
			'num_temas'		=> $foros['num_temas'],
			'num_mensajes'	=> $foros['num_mensajes'],
			'estilo_num'	=> $estilo_num
		]) ;
		$estilo_num = $estilo_num == 1 ? 2 : 1 ;
	}
	$buscar2->free();
}
$buscar->free();

$total_usuarios = $conectar
	->query("SELECT COUNT(`id`) AS `num` FROM `{$tabla_usuarios}`")
	->fetch_object()
	->num;

$buscar = $conectar->query("SELECT `id`,`nick` FROM `{$tabla_usuarios}` ORDER BY `id` DESC LIMIT 1");
$datos = $buscar->fetch_assoc();
$ePiel->variables([
	'usuarios_total'		=> $total_en_linea[0] + $total_en_linea[1],
	'usuarios_registrados'	=> $total_en_linea[1],
	'usuarios_anonimos'		=> $total_en_linea[0],
	'usuarios_reg_en_linea'	=> $reg_en_linea,
	'usuarios_reg_total'	=> $total_usuarios,
	'usuarios_reg_ultimo'	=> "<a href=\"{$u[0]}forousuarios{$u[1]}{$u[2]}u{$u[4]}{$datos['id']}{$u[5]}\" class=\"eforo_enlace\">{$datos['nick']}</a>",
	'usuarios_lista'		=> $u[0].'forousuarios'.$u[1]
]);
$buscar->free();

$ePiel->mostrar('foro') ;
$ePiel->variable('tiempo_carga', round(tiempo_carga() - $tiempo, 4));
$ePiel->mostrar('piedepagina');
