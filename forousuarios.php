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

require 'foroconfig.php';
require 'eforo_funciones/epaginas.php';

$ePiel->cargar([
	'cabecera'		=> $conf['plantilla'].'cabecera.pta',
	'forousuarios'	=> $conf['plantilla'].'forousuarios.pta',
	'piedepagina'	=> $conf['plantilla'].'piedepagina.pta'
]);

require 'foromenu.php';

$ePiel->variables([
	'titulo' => $conf['foro_titulo'],
	'estilo' => $conf['estilo']
]) ;

$ePiel->mostrar('cabecera');
$ePiel->mostrar('menu');
$url_usuarios = $u[0].'forousuarios'.$u[1].$u[5];

// Limpiar ID Usuario
$idUsuario = intval($_GET['u']);

if(empty($idUsuario)) {
	$letra = !empty($_GET['letra']) && preg_match('^[a-z]{1}$',$_GET['letra']) // Optimizar
		? " WHERE `nick` LIKE '{$_GET['letra']}%'"
		: '';
	$orden = $letra ? '`nick` ASC' : '`id` DESC' ;
	$ePaginas = new ePaginas("SELECT * FROM {$tabla_usuarios}{$letra} ORDER BY {$orden}", 30) ;
	$ePaginas->u = [
		$u[2],
		$u[3],
		$u[4],
		$u[5]
	];
	$ePaginas->e = [
		'<a href="',
		'" class="eforo_enlace">',
		'</a>'
	];
	$buscar = $ePaginas->consultar(); // Retorna POO
	$ePiel->variables_bloque('lista', [
		'url_usuarios'	=> $url_usuarios,
		'url_inicio'	=> $u[0].'forousuarios'.$u[1].$u[2].'letra'.$u[4],
		'url_fin'		=> $u[5],
		'paginas'		=> $ePaginas->paginar()
	]);
	$estilo_num = 1;
	while($datos = $buscar->fetch_assoc()) {
		$ePiel->variables_bloque('lista.usuario', [
			'url_usuario'	=> $u[0].'forousuarios'.$u[1].$u[2].'u'.$u[4].$datos['id'].$u[5],
			'nick'			=> $datos['nick'],
			'sexo'			=> !$datos['sexo'] ? 'Masculino' : 'Femenino',
			'edad'			=> $datos['edad'] ? $datos['edad'] : '&nbsp;',
			'pais'			=> $datos['pais'] ? $datos['pais'] : '&nbsp;',
			'fecha'			=> fecha($datos['fecha_registrado']),
			'estilo_num'	=> $estilo_num
		]) ;
		$estilo_num = $estilo_num == 1 ? 2 : 1 ; 
	}
	$buscar->free();
}
else {
	// Se almacenan todos los rangos en un array
	$buscar = $conectar->query('SELECT * FROM `eforo_rangos` ORDER BY `rango` ASC');
	while($datos = $buscar->fetch_assoc()) {
		$rangos[ $datos['rango'] ] = [
			$datos['minimo'],
			$datos['descripcion']
		];
	}
	$buscar->free();

	$buscar = $conectar->query("SELECT * FROM `{$tabla_usuarios}` WHERE `id`='{$idUsuario}'");
	if($buscar->num_rows) {
		$datos = $buscar->fetch_assoc();
		$datos['descripcion'] = nl2br($datos['descripcion']); // Consultar para usar Markdown!
		if($datos['rango_fijo']) {
			$usuario_rango = $rangos[$datos['rango']][1];
		}
		else {
			$usuario_rango = $rangos[1][1];
			foreach($rangos as $rango) {
				if($rango[0] != 0 && $datos['mensajes'] >= $rango[0]) {
					$usuario_rango = $rango[1];
				}
			}
		}
		if($datos['web']) { // Sanitizar enlace web*
			$datos['web'] = '<a href="'.(!preg_matchi('^http://',$datos['web']) ? 'http://'.$datos['web'] : $datos['web']).'" target="_blank" class="eforo_enlace">'.$datos['web'].'</a>';
		}
		if($conf['permitir_firma']) {
			require 'eforo_funciones/codigo.php';
			if($conf['permitir_caretos']) {
				$datos['firma'] = caretos($datos['firma']);
			}
			if($conf['permitir_codigo']) {
				$datos['firma'] = codigo($datos['firma']);
			}
			$datos['firma'] = nl2br($datos['firma']);
		}
		$ePiel->variables_bloque('perfil', [
			'nick'				=> $datos['nick'],
			'url_usuarios'		=> $url_usuarios,
			'sexo'				=> !$datos['sexo'] ? 'Masculino' : 'Femenino',
			'avatar'			=> !$datos['avatar'] ? '0.gif' : $datos['id'].'.'.$datos['avatar'],
			'edad'				=> $datos['edad'] ? $datos['edad'] : '<i>No indicada</i>',
			'pais'				=> $datos['pais'] ? $datos['pais'] : '<i>No indicado</i>',
			'descripcion'		=> $datos['descripcion'] ? $datos['descripcion'] : '<i>Sin descripción</i>',
			'web'				=> $datos['web'] ? $datos['web'] : '<i>Sin web</i>',
			'firma'				=> $datos['firma'] ? $datos['firma'] : '<i>Sin firma</i>',
			'total_mensajes'	=> $datos['mensajes'],
			'rango'				=> $usuario_rango,
			'fecha'				=> fecha($datos['fecha_registrado']),
			'fecha_conectado'	=> fecha($datos['fecha_conectado'])
		]);
	}
	else {
		$ePiel->variables_bloque('no');
	}
}
$ePiel->mostrar('forousuarios');
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo, 4));
$ePiel->mostrar('piedepagina');
