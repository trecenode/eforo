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

# * Conexión a la base de datos
$config = array() ;
$config[0] = 'localhost' ; # Generalmente "localhost", una URL o una IP
$config[1] = '' ; # Usuario
$config[2] = '' ; # Contraseña
$config[3] = 'eforo' ; # Nombre

$conectar = new mysqli($config[0],$config[1],$config[2],$config[3]) ;

if ($conectar->connect_errno) {
    echo "<br>Error: Fallo al conectarse a MySQL debido a: \n";
    echo "<br><br>Errno: " . $conectar->connect_errno . "\n";
    echo "<br>Error: " . $conectar->connect_error . "\n";
    exit;
}
?>