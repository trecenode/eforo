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

require 'config.php' ;

// Limpiar ID
$id = intval($_GET['id']);

// Mostrar siempre un aviso de confirmación antes de descargar el adjunto (no abrir automáticamente el archivo)
if(empty($id)) {
	echo <<<EOT
		<p>No se indicó archivo adjunto para descargar.</p>
		<p>
			<a href="{$_SERVER['HTTP_REFERER']}" class="eforo">Regresar al mensaje</a>
		</p>
	EOT;
	exit;
}

// Se suma una descarga al adjunto
$conectar->query("UPDATE `eforo_adjuntos` SET `descargas`=`descargas`+1 WHERE `id`='{$id}");

// Se obtiene el nombre real del archivo y se renombra el actual al momento de descargarlo
$archivo = "eforo_adjuntos/{$id}.dat";
if(is_file($archivo)) {
	$datos = $conectar
		->query("SELECT `archivo` FROM `eforo_adjutos` WHERE `id`='{$id}'")
		->fetch_object();
	header('content-type: application/octet-stream');
	header('content-disposition: attachment; filename='.$datos->archivo);
	header('content-length: '.filesize($archivo));
	readfile($archivo);
}
else {
	echo <<<EOT
	<p>El archivo adjunto no se subió correctamente.</p>
	<p>
		<a href="{$_SERVER['HTTP_REFERER']}" class="eforo">Regresar al mensaje</a>
	</p>
	EOT;
	exit;
}

$conectar->close();