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
require 'eforo_funciones/recientes.php' ;
require 'eforo_funciones/epaginas.php' ;
require 'eforo_funciones/aviso.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'forotemas' => $conf['plantilla'].'forotemas.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � '.$titulo_subforo,
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$menu = $ePiel->mostrar('menu',1) ;
echo $menu ;
# * Se comprueba si el usuario tiene permiso para entrar al subforo seleccionado
$con = $conectar->query("select p_leer from eforo_foros where id='{$_GET['foro']}' limit 1") ;
$datos = mysqli_fetch_assoc($con) ;
if($usuario['rango'] < $datos['p_leer']) {
	aviso('Nivel m�nimo insuficiente','<p>No tienes suficiente nivel para entrar a este subforo. Intenta iniciar sesi�n desde el men�.</p><p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a></p>',1) ;
}
# **********************************************
# *** Mostrar los temas del subforo seleccionado
# **********************************************
# * Se muestran los moderadores del subforo seleccionado
$moderadores = array() ;
$moderar = false ;
$total = count($conf['admin_id']) ;
for($i = 0 ; $i < $total ; $i++) {
	# Se agregan primero a los administradores
	$moderadores['id'][] = $conf['admin_id'][$i] ;
	$moderadores['nick'][] = usuario($conf['admin_id'][$i]) ;
	if($c_id == $conf['admin_id'][$i]) $moderar = true ;
}
$con = $conectar->query("select id_usuario from eforo_moderadores where id_foro='{$_GET['foro']}' order by id asc") ;
for($i = 0 ; $datos = mysqli_fetch_assoc($con) ; $i++) {
	# Ahora se agregan a los moderadores
	$moderadores['id'][] = $datos['id_usuario'] ;
	$moderadores['nick'][] = usuario($datos['id_usuario']) ;
	if($c_id == $datos['id_usuario']) $moderar = true ;
}
mysqli_free_result($con) ;
$total = count($moderadores['id']) ;
$ePiel->variable('moderadores_total',$total) ;
for($i = 0 ; $i < $total ; $i++) {
	$moderadores_lista[] = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]{$moderadores['id'][$i]}$u[5]\" class=\"eforo_enlace\">{$moderadores['nick'][$i]}</a>" ;
}
$ePiel->variable('moderadores_lista',implode(', ',$moderadores_lista)) ;
# * Panel de moderadores (para mover y borrar varios temas a la vez)
if($moderar) {
	$ePiel->variables_bloque('moderadores_panel',array(
	'url_mover' => "$u[0]foromover$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]",
	'url_borrar' => "$u[0]foroborrar$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]"
	)) ;
	$con = $conectar->query('select * from eforo_categorias order by orden asc') ;
	while($datos = mysqli_fetch_assoc($con)) {
		$categorias[$datos['id']] = $datos['categoria'] ;
	}
	mysqli_free_result($con) ;
	foreach($categorias as $categoria_id => $categoria_nom) {
		$ePiel->variables_bloque('moderadores_panel.categoria',array(
		'titulo' => $categoria_nom
		)) ;
		$con = $conectar->query("select id,foro from eforo_foros where id_categoria='$categoria_id' order by orden asc") ;
		while($datos = mysqli_fetch_assoc($con)) {
			$ePiel->variables_bloque('moderadores_panel.categoria.subforo',array(
			'id' => $datos['id'],
			'titulo' => $datos['foro']
			)) ;
		}
		mysqli_free_result($con) ;
	}
}
# Se muestran los nuevos temas o reci�n respondidos del subforo seleccionado
$ePaginas = new ePaginas("select * from eforo_mensajes where id=id_tema and id_foro='{$_GET['foro']}' order by o_importante desc, fecha_ultimo desc",$conf['max_temas']) ;
$ePaginas->u = array($u[2],$u[3],$u[4],$u[5]) ;
$ePaginas->e = array('<a href="','" class="eforo_enlace">','</a>') ;
$con = $ePaginas->consultar() ;
$ePiel->variable('paginas',$ePaginas->paginar()) ;
$estilo_num = 1 ;
if(mysqli_num_rows($con)) {
	while($datos = mysqli_fetch_assoc($con)) {
		# Se buscan mensajes que est�n en el recordatorio
		$ind_imagen = 'foco_apagado.gif' ;
		$ind_mensaje = 'No hay mensajes nuevos' ;
		if($c_id) {
			$con2 = $conectar->query("select count(id_usuario) from eforo_recientes where id_usuario='$c_id' and id_mensaje='{$datos['id']}' limit 1") ;
			if(@mysqli_result($con2,0,0)) {
				$ind_imagen = 'foco_encendido.gif' ;
				$ind_mensaje = 'Hay mensajes nuevos' ;
			}
			mysqli_free_result($con2) ;
		}
		# Se obtiene el nombre del autor de cada tema
		if($datos['id_usuario']) {
			if($tema_autor = usuario($datos['id_usuario'])) {
				$tema_autor = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id_usuario']}$u[5]\" class=\"eforo_enlace\">$tema_autor</a>" ;
			}
			else {
				$tema_autor = '<i>Eliminad@</i>' ;
			}
		}
		else {
			$tema_autor = '<i>An�nim@</i>' ;
		}
		$tema_autor .= '<br />'.fecha($datos['fecha']) ;
		# Se obtiene el nombre del autor del �ltimo mensaje
		if($datos['num_respuestas']) {
			$con2 = $conectar->query("select id_usuario from eforo_mensajes where id_tema='{$datos['id']}' order by id desc limit 1") ;
			$datos2 = mysqli_fetch_row($con2) ;
			if($datos2[0]) {
				if($nick_autor = usuario($datos2[0])) {
					$ult_men_autor = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]$datos2[0]$u[5]\" class=\"eforo_enlace\">$nick_autor</a>" ;
				}
				else {
					$ult_men_autor = '<i>Eliminad@</i>' ;
				}
			}
			else {
				$ult_men_autor = '<i>An�nim@</i>' ;
			}
			$ult_men_autor .= '<br />'.fecha($datos['fecha_ultimo']) ;
			mysqli_free_result($con2) ;
		}
		else {
			$ult_men_autor = '<b>Sin respuestas</b>' ;
		}
		$ePiel->variables_bloque('tema',array(
		'ind_imagen' => $conf['plantilla'].'imagenes/'.$ind_imagen,
		'ind_mensaje' => $ind_mensaje,
		'moderar' => $moderar ? "<input type=\"checkbox\" name=\"id_tema{$datos['id']}\" value=\"{$datos['id']}\" /> " : '',
		'url' => "$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$datos['id']}$u[5]",
		'importante' => $datos['o_importante'] ? '<img src="'.$conf['plantilla'].'imagenes/importante.gif" border="0" align="top" alt="Tema importante" /> ' : '',
		'titulo' => $datos['tema'],
		'tema_autor' => $tema_autor,
		'ult_men_autor' => $ult_men_autor,
		'num_visitas' => $datos['num_visitas'],
		'num_respuestas' => $datos['num_respuestas'],
		'estilo_num' => $estilo_num
		)) ;
		$estilo_num = $estilo_num == 1 ? 2 : 1 ;
	}
}
else {
	$ePiel->variables_bloque('no_hay_mensajes') ;
}
mysqli_free_result($con) ;
$ePiel->mostrar('forotemas') ;
echo $menu ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>