<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus usuarios convivan y se sientan parte de tu web
Copyright © 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

require 'foroconfig.php' ;
require 'eforo_funciones/quitar.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'forousuario' => $conf['plantilla'].'forousuario.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
require 'foromenu.php' ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'],
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
$ePiel->mostrar('menu') ;
// * Esta Página se comportar� dependiendo de lo que se haya elegido (registrar, entrar, editar y salir)
$_GET['que'] = !empty($_GET['que']) ? $_GET['que'] : '' ;
switch($_GET['que']) {
	// --> Iniciar sesi�n como usuario registrado
	case 'entrar' :
		$ePiel->variables_bloque('modo_iniciar_sesion',array(
		'url_nuevo_usuario' => "$u[0]forousuario$u[1]$u[2]que$u[4]registrar$u[5]",
		'url_rec_contrasena' => "$u[0]forousuario$u[1]$u[2]que$u[4]contrasena$u[5]"
		)) ;
	break ;
	// --> Registrar nuevo usuario
	case 'registrar' :
		$ePiel->variables_bloque('modo_nuevo_usuario') ;
	break ;
	// --> Recuperar los datos del usuario en caso de extrav�o
	case 'contrasena' :
		$ePiel->variables_bloque('modo_rec_contrasena') ;
	break ;
	// --> Editar los datos del perfil del usuario
	case 'perfil' :
		if(!$es_usuario) {
			require 'eforo_funciones/aviso.php' ;
			aviso('Error','No puedes editar el perfil.',1) ;
		}
		$con = $conectar->query("select * from $tabla_usuarios where id='$c_id'") ;
		$datos = mysqli_fetch_assoc($con) ;
		$ePiel->variables_bloque('modo_perfil',array(
		'url_regresar' => "$u[0]foro$u[1]$u[5]",
		'avatar_tamano' => $conf['avatar_tamano'],
		'avatar_largo' => $conf['avatar_largo'],
		'avatar_ancho' => $conf['avatar_ancho'],
		'u_nick' => $datos['nick'],
		'u_email' => $datos['email'],
		'u_pais' => $datos['pais'],
		'u_edad' => $datos['edad'] ? $datos['edad'] : '',
		'u_sexo_s' => $datos['sexo'] ? ' selected="selected"' : '',
		'u_descripcion' => $datos['descripcion'],
		'u_web' => $datos['web'],
		'u_firma' => $datos['firma'],
		'u_gmt' => $datos['gmt']
		)) ;
		if($datos['avatar']) {
			$ePiel->variables_bloque('modo_perfil.mostrar_avatar',array(
			'id' => $datos['id'],
			'ext' => $datos['avatar'],
			'alt' => $datos['nick'] ? 'Avatar de '.$datos['nick'] : ''
			)) ;
		}
		mysqli_free_result($con) ;
}
$ePiel->mostrar('forousuario') ;
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>