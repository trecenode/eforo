<?php
/**
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2006
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

// Conexión a la base de datos
$config = [
	'13node.me',	// Generalmente "localhost", una URL o una IP
	'',				// Usuario
	'',				// Contraseña
	'eforo'			// Base de datos
];

$conectar = new mysqli($config[0], $config[1], $config[2], $config[3]);

if($conectar->connect_errno) {
	die('Error MySQLi: '.$conectar->connect_error);
}
if(!$conectar->set_charset('utf8')) {
	die('Error MySQLi: '.$conectar->error);
}
