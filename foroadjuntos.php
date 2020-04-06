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

require 'config.php' ;
# * Mostrar siempre un aviso de confirmaci�n antes de descargar el adjunto (no abrir autom�ticamente el archivo)
if(empty($_GET['id']) && !preg_match('^[0-9]+$',$_GET['id'])) exit('<p>No se indic� archivo adjunto para descargar.</p><p><a href="'.$_SERVER['HTTP_REFERER'].'" class="eforo">� Regresar al mensaje</a></p>') ;
# --> Se suma una descarga al adjunto
$conectar->query("update eforo_adjuntos set descargas=descargas+1 where id='{$_GET['id']}'") ;
# --> Se obtiene el nombre real del archivo y se renombra el actual al momento de descargarlo
$con = $conectar->query("select archivo from eforo_adjuntos where id='{$_GET['id']}'") ;
$datos = mysqli_fetch_row($con) ;
$archivo = 'eforo_adjuntos/'.$_GET['id'].'.dat' ;
if(file_exists($archivo)) {
	header("content-type: application/octet-stream") ;
	header("content-disposition: attachment ; filename=$datos[0]") ;
	header("content-length: ".filesize($archivo)) ;
	readfile($archivo) ;
}
else {
	exit('<p>El archivo adjunto no se subi� correctamente.</p><p><a href="'.$_SERVER['HTTP_REFERER'].'" class="eforo">� Regresar al mensaje</a></p>') ;
}
mysql_close($conectar) ;
?>