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
require 'eforo_funciones/epaginas.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'forousuarios' => $conf['plantilla'].'forousuarios.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
$url_usuarios = "$u[0]forousuarios$u[1]$u[5]" ;
if(empty($_GET['u']) || !preg_match('^[0-9]+$',$_GET['u'])) {
	$letra = !empty($_GET['letra']) && preg_match('^[a-z]{1}$',$_GET['letra']) ? " where nick like '{$_GET['letra']}%'" : '' ;
	$orden = $letra ? 'nick asc' : 'id desc' ;
	$ePaginas = new ePaginas("select * from $tabla_usuarios$letra order by $orden",30) ;
	$ePaginas->u = array($u[2],$u[3],$u[4],$u[5]) ;
	$ePaginas->e = array('<a href="','" class="eforo_enlace">','</a>') ;
	$con = $ePaginas->consultar() ;
	$ePiel->variables_bloque('lista',array(
	'url_usuarios' => $url_usuarios,
	'url_inicio' => "$u[0]forousuarios$u[1]$u[2]letra$u[4]",
	'url_fin' => "$u[5]",
	'paginas' => $ePaginas->paginar()
	)) ;
	$estilo_num = 1 ;
	while($datos = mysqli_fetch_assoc($con)) {
		$ePiel->variables_bloque('lista.usuario',array(
		'url_usuario' => "$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id']}$u[5]",
		'nick' => $datos['nick'],
		'sexo' => !$datos['sexo'] ? 'Masculino' : 'Femenino',
		'edad' => $datos['edad'] ? $datos['edad'] : '&nbsp;',
		'pais' => $datos['pais'] ? $datos['pais'] : '&nbsp;',
		'fecha' => fecha($datos['fecha_registrado']),
		'estilo_num' => $estilo_num
		)) ;
		$estilo_num = $estilo_num == 1 ? 2 : 1 ; 
	}
	mysqli_free_result($con) ;
}
else {
	# * Se almacenan todos los rangos en un array
	$con = $conectar->query("select * from eforo_rangos order by rango asc") ;
	while($datos = mysqli_fetch_assoc($con)) {
		$rangos[$datos['rango']] = array($datos['minimo'],$datos['descripcion']) ;
	}
	mysqli_free_result($con) ;
	$con = $conectar->query("select * from $tabla_usuarios where id='{$_GET['u']}'") ;
	if(mysqli_num_rows($con)) {
		$datos = mysqli_fetch_assoc($con) ;
		$datos['descripcion'] = nl2br($datos['descripcion']) ;
		if($datos['rango_fijo']) {
			$usuario_rango = $rangos[$datos['rango']][1] ;
		}
		else {
			$usuario_rango = $rangos[1][1] ;
			foreach($rangos as $rango) {
				if($rango[0] != 0 && $datos['mensajes'] >= $rango[0]) $usuario_rango = $rango[1] ;
			}
		}
		if($datos['web']) {
			$datos['web'] = '<a href="'.(!preg_matchi('^http://',$datos['web']) ? 'http://'.$datos['web'] : $datos['web']).'" target="_blank" class="eforo_enlace">'.$datos['web'].'</a>' ;
		}
		if($conf['permitir_firma']) {
			require 'eforo_funciones/codigo.php' ;
			if($conf['permitir_caretos']) $datos['firma'] = caretos($datos['firma']) ;
			if($conf['permitir_codigo']) $datos['firma'] = codigo($datos['firma']) ;
			$datos['firma'] = nl2br($datos['firma']) ;
		}
		$ePiel->variables_bloque('perfil',array(
		'nick' => $datos['nick'],
		'url_usuarios' => $url_usuarios,
		'sexo' => !$datos['sexo'] ? 'Masculino' : 'Femenino',
		'avatar' => !$datos['avatar'] ? '0.gif' : $datos['id'].'.'.$datos['avatar'],
		'edad' => $datos['edad'] ? $datos['edad'] : '<i>No indicada</i>',
		'pais' => $datos['pais'] ? $datos['pais'] : '<i>No indicado</i>',
		'descripcion' => $datos['descripcion'] ? $datos['descripcion'] : '<i>Sin descripci�n</i>',
		'web' => $datos['web'] ? $datos['web'] : '<i>Sin web</i>',
		'firma' => $datos['firma'] ? $datos['firma'] : '<i>Sin firma</i>',
		'total_mensajes' => $datos['mensajes'],
		'rango' => $usuario_rango,
		'fecha' => fecha($datos['fecha_registrado']),
		'fecha_conectado' => fecha($datos['fecha_conectado'])
		)) ;
	}
	else {
		$ePiel->variables_bloque('no') ;
	}
}
$ePiel->mostrar('forousuarios') ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>