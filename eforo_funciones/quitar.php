<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus visitantes convivan y se sientan parte de tu web
Copyright © 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

# * Función que limpia la informaci�n enviada de formularios para evitar ataques
function quitar($texto,$comprobar = 0,$no_escapar = 0) {
	$texto = trim($texto) ;
	$texto = htmlspecialchars($texto) ;
	$texto = str_replace(chr(160),'',$texto) ; # <-- Elimina el caract�r ASCII 0160, es un espacio en blanco que no puede ser eliminado por trim()
	if(get_magic_quotes_gpc()) $texto = stripslashes($texto) ;
	# * El texto se escapa por defecto para poder procesarlo en las consultas a la base de datos
	if($no_escapar == 0) $texto = mysqli_real_escape_string($GLOBALS["conectar"],$texto) ;
	if($comprobar == 1 && empty($texto)) {
		require_once 'aviso.php' ;
		aviso('Error','<p>Debes llenar los campos correctamente.</p><p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a></p>',1) ;
	}
	return $texto ;
}
?>