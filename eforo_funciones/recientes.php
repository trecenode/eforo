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

# * Revisar si hay mensajes nuevos desde la �ltima visita del usuario
if($c_id) {
	# Borrar mensajes del recordatorio (mensajes nuevos sin leer)
	$max_tiempo = 86400 ; # <-- Tiempo en segundos durante el cu�l se recordar�n los mensajes (por defecto 24 horas)
	$max_tiempo = $fechaTime - $max_tiempo ;
	$conectar->query("delete from eforo_recientes where fecha<'$max_tiempo'") ;
	# Guardar los mensajes nuevos en el recordatorio
	$con = $conectar->query("select id from eforo_foros order by id asc") ;
	while($datos = mysqli_fetch_row($con)) {
		$con2 = $conectar->query("select id from eforo_mensajes where id=id_tema and id_foro='$datos[0]' and fecha_ultimo>'{$usuario['ultima_vez']}' order by fecha_ultimo desc limit 20") ;
		while($datos2 = mysqli_fetch_row($con2)) {
			$conectar->query("insert into eforo_recientes (id_usuario,fecha,id_foro,id_mensaje) values ('$c_id','$fecha','$datos[0]','$datos2[0]')") ;
		}
		mysqli_free_result($con2) ;
	}
	mysqli_free_result($con) ;
	$conectar->query("update $tabla_usuarios set fecha_conectado='$fecha' where id='$c_id'") ;
}
?>
