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
# * Marcar los subforos como le�dos
if(!empty($_GET['leidos'])) $conectar->query("delete from eforo_recientes where id_usuario='$c_id'") ;
require 'eforo_funciones/recientes.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'foro' => $conf['plantilla'].'foro.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
# ***************************************************
# *** Mostrar todos los subforos (secci�n principal)
# ***************************************************
$con = $conectar->query("select id,categoria from eforo_categorias order by orden asc") ;
while($datos = mysqli_fetch_row($con)) {
	$ePiel->variables_bloque('categoria',array(
	'titulo' => $datos[1]
	)) ;
	$con2 = $conectar->query("select id,foro,descripcion,num_temas,num_mensajes from eforo_foros where id_categoria='$datos[0]' order by orden asc") ;
	$estilo_num = 1 ;
	while($datos2 = mysqli_fetch_assoc($con2)) {
		# Buscar mensajes en el recordatorio (s�lo usuarios)
		$ind_imagen = 'foco_apagado.gif' ;
		$ind_mensaje = 'No hay mensajes nuevos' ;
		if($c_id) {
			$con3 = $conectar->query("select count(id_usuario) from eforo_recientes where id_usuario='$c_id' and id_foro='{$datos2['id']}' limit 1") ;
			if(mysqli_result($con3,0,0)) {
				$ind_imagen = 'foco_encendido.gif' ;
				$ind_mensaje = 'Hay mensajes nuevos' ;
			}
			mysqli_free_result($con3) ;
		}
		# Obtener �ltimo mensaje enviado en cada subforo
		$con3 = $conectar->query("select id,id_tema,id_usuario,fecha from eforo_mensajes where id_foro='{$datos2['id']}' order by fecha desc limit 1") ;
		if(mysqli_num_rows($con3)) {
			$datos3 = mysqli_fetch_assoc($con3) ;
			if($datos3['id_usuario']) {
				$autor = ($autor = usuario($datos3['id_usuario'])) ? "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos3['id_usuario']}$u[5]\" class=\"eforo_enlace\">$autor</a>" : '<i>Eliminad@</i>' ;
			}
			else {
				$autor = '<i>An�nim@</i>' ;
			}
			$ult_mensaje = $autor." <a href=\"$u[0]foromensajes$u[1]$u[2]foro$u[4]{$datos2['id']}$u[3]tema$u[4]{$datos3['id_tema']}$u[5]#{$datos3['id']}\" class=\"eforo_enlace\">�</a><br />".fecha($datos3['fecha']) ;
		}
		else {
			$ult_mensaje = '<b>No hay mensajes</b>' ;
		}
		mysqli_free_result($con3) ;
		$ePiel->variables_bloque('categoria.subforo',array(
		'ind_imagen' => $conf['plantilla'].'imagenes/'.$ind_imagen,
		'ind_mensaje' => $ind_mensaje,
		'titulo_url' => "$u[0]forotemas$u[1]$u[2]foro$u[4]{$datos2['id']}$u[5]",
		'titulo' => $datos2['foro'],
		'descripcion' => $datos2['descripcion'],
		'ult_mensaje' => $ult_mensaje,
		'num_temas' => $datos2['num_temas'],
		'num_mensajes' => $datos2['num_mensajes'],
		'estilo_num' => $estilo_num
		)) ;
		$estilo_num = $estilo_num == 1 ? 2 : 1 ;
	}
	mysqli_free_result($con2) ;
}
mysqli_free_result($con) ;
$con = $conectar->query("select count(id) from $tabla_usuarios") ;
$total_usuarios = mysqli_result($con,0,0) ;
mysqli_free_result($con) ;
$con = $conectar->query("select id,nick from $tabla_usuarios order by id desc limit 1") ;
$datos = mysqli_fetch_row($con) ;
$ePiel->variables(array(
'usuarios_total' => $total_en_linea[0] + $total_en_linea[1],
'usuarios_registrados' => $total_en_linea[1],
'usuarios_anonimos' => $total_en_linea[0],
'usuarios_reg_en_linea' => $reg_en_linea,
'usuarios_reg_total' => $total_usuarios,
'usuarios_reg_ultimo' => "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]$datos[0]$u[5]\" class=\"eforo_enlace\">$datos[1]</a>",
'usuarios_lista' => "$u[0]forousuarios$u[1]"
)) ;
mysqli_free_result($con) ;
$ePiel->mostrar('foro') ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>