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

# * Si el usuario es un moderador o administrador se ignora su rango o nivel
$es_moderador = false ;
if(!empty($_GET['foro'])) {
	$con = $conectar->query("select count(id) from eforo_moderadores where id_foro='{$_GET['foro']}' and id_usuario='$c_id'") ;
	foreach($conf['admin_id'] as $id_admin) {
		if(mysqli_result($con,0,0) != 0 || $id_admin == $c_id) {
			$es_moderador = true ;
			break ;
		}
	}
	mysqli_free_result($con) ;
}
# * Se comprueba si el usuario tiene el permiso suficiente para realizar una determinada acci�n
if(!$es_moderador) {
	function permiso($permiso) {
		$con = $conectar->query("select $permiso from eforo_foros where id='{$_GET['foro']}'") ;
		$datos = mysql_fetch_array($con) ;
		if($GLOBALS['usuario']['rango'] < $datos[$permiso]) aviso('Nivel m�nimo insuficiente','<p>No tienes suficiente nivel. Intenta iniciar sesi�n desde el men�.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
		mysqli_free_result($con) ;
	}
}
# * Si el usuario es un administrador se le otorgar�n privilegios de administraci�n
$es_administrador = false ;
foreach($conf['admin_id'] as $id_admin) {
	if($id_admin == $c_id) {
		$es_administrador = true ;
		break ;
	}
}
?>