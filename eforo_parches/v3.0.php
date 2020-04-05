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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>eForo v3.1 - Actualizaci�n desde la versi�n v3.0</title>
<style type="text/css">
body {
font-family: verdana ;
font-size: 10pt ;
color: #000000 ;
margin: 100px ;
}
</style>
</head>
<body>
<h3>eForo v3.1 - Actualizaci�n desde la versi�n v3.0</h3>
<?
require '../config.php' ;
if(!isset($_POST['enviar'])) {
?>
<div style="border: #000000 1px solid ; background-color: #cddff0 ; padding: 5px">
Comprobando configuraci�n de <b>config.php</b>...<br /><br />
<?
if($conectar) echo 'El archivo <b>config.php</b> est� configurado correctamente.' ;
?>
</div>
<p>Completa los siguientes datos:</p>
<form method="post" action="v3.0.php">
<b>URL donde est� instalado eForo (ej. http://www.pagina.com/carpeta/):</b><br />
<input type="text" size="50" name="foro_url" value="http://<?=$_SERVER['HTTP_HOST'].str_replace('eforo_parches/v3.0.php','',$_SERVER['PHP_SELF'])?>" /><br />
<b>Tabla de usuarios:</b><br />
<input type="text" size="20" name="tabla_usuarios" value="eforo_usuarios" /><br />
Modifica este campo s�lo si est�s usando eForo integrado con el sistema de usuarios de <b>www.electros.net</b> (eUsuarios v1.0, Registro de usuarios v1.2 � cualquier versi�n reciente).<br /><br />
<b>Aviso:</b> Siempre has un respaldo de tu base de datos antes de aplicar cualquier actualizaci�n, ya que cualquier
error ser� irreversible, no me hago responsable por p�rdida de datos y/o malfuncionamiento del foro en caso de no
seguir correctamente las instrucciones.
Si todo est� correcto procedemos con la actualizaci�n.<br /><br />
<input type="submit" name="enviar" value="Actualizar" />
</form>
</p>
<?
}
else {
$tabla_usuarios = &$_POST['tabla_usuarios'] ;
$codigo =
'alter table eforo_adjuntos change id_mensaje id_mensaje mediumint(8) unsigned not null
;
alter table eforo_adjuntos change archivo archivo varchar(100) not null
;
alter table eforo_adjuntos change descargas descargas mediumint(8) unsigned not null
;
alter table eforo_categorias change orden orden smallint(5) unsigned not null
;
alter table eforo_config change administrador id_administrador varchar(100) not null
;
alter table eforo_config change urlforo foro_url varchar(100) not null
;
alter table eforo_config change titulo foro_titulo varchar(100) not null
;
alter table eforo_config change codigo codigo tinyint(1) unsigned not null
;
update eforo_config set codigo=codigo-1 where id=1
;
alter table eforo_config change caretos caretos tinyint(1) unsigned not null
;
update eforo_config set caretos=caretos-1 where id=1
;
alter table eforo_config drop url
;
alter table eforo_config change firma firma tinyint(1) unsigned not null
;
update eforo_config set firma=firma-1 where id=1
;
alter table eforo_config change notificacion notificacion tinyint(1) unsigned not null
;
update eforo_config set notificacion=notificacion-1 where id=1
;
alter table eforo_config add plantilla varchar(100) not null after notificacion
;
alter table eforo_config change privados privados smallint(5) unsigned not null
;
alter table eforo_enlinea change id_usuario id_usuario mediumint(8) unsigned not null
;
alter table eforo_foros change orden orden smallint(5) unsigned not null
;
alter table eforo_foros change descripcion descripcion text not null
;
alter table eforo_foros change num_temas num_temas mediumint(8) unsigned not null
;
alter table eforo_foros change num_mensajes num_mensajes mediumint(8) unsigned not null
;
alter table eforo_foros drop index categoria
;
alter table eforo_foros add index (id_categoria)
;
alter table eforo_mensajes change id id mediumint(8) unsigned not null auto_increment
;
alter table eforo_mensajes change id_tema id_tema mediumint(8) unsigned not null auto_increment
;
alter table eforo_mensajes change num_visitas num_visitas mediumint(8) unsigned not null
;
alter table eforo_mensajes change num_respuestas num_respuestas mediumint(8) unsigned not null
;
alter table eforo_mensajes change id_usuario id_usuario mediumint(8) unsigned not null
;
alter table eforo_mensajes change o_caretos o_caretos tinyint(1) unsigned not null
;
update eforo_mensajes set o_caretos=o_caretos-1
;
alter table eforo_mensajes change o_codigo o_codigo tinyint(1) unsigned not null
;
update eforo_mensajes set o_codigo=o_codigo-1
;
alter table eforo_mensajes drop o_url
;
alter table eforo_mensajes change o_firma o_firma tinyint(1) unsigned not null
;
update eforo_mensajes set o_firma=o_firma-1
;
alter table eforo_mensajes change o_importante o_importante tinyint(1) unsigned not null
;
update eforo_mensajes set o_importante=o_importante-1
;
alter table eforo_mensajes change o_notificacion o_notificacion tinyint(1) unsigned not null
;
update eforo_mensajes set o_notificacion=o_notificacion-1
;
alter table eforo_mensajes change o_notificacion_email o_notificacion_email tinyint(1) unsigned not null
;
update eforo_mensajes set o_notificacion_email=o_notificacion_email-1
;
alter table eforo_mensajes add cerrado tinyint(1) unsigned not null
;
alter table eforo_mensajes drop index foro
;
alter table eforo_mensajes drop index id_indice
;
alter table eforo_mensajes add index (id_foro)
;
alter table eforo_mensajes add index (id_tema)
;
alter table eforo_moderadores change id_usuario id_usuario mediumint(8) unsigned not null
;
alter table eforo_privados change id id mediumint(8) unsigned not null auto_increment
;
alter table eforo_privados change id_remitente id_remitente mediumint(8) unsigned not null
;
alter table eforo_privados change id_destinatario id_destinatario mediumint(8) unsigned not null
;
alter table eforo_privados drop index destinatario
;
alter table eforo_privados add index (id_destinatario)
;
alter table eforo_recientes change id_usuario id_usuario mediumint(8) unsigned not null
;
alter table eforo_recientes change id_mensaje id_mensaje mediumint(8) unsigned not null
;
alter table '.$tabla_usuarios.' change id id mediumint(8) unsigned not null auto_increment
;
alter table '.$tabla_usuarios.' change email email varchar(50) not null
;
alter table '.$tabla_usuarios.' change fecha fecha_registrado int(10) unsigned not null
;
alter table '.$tabla_usuarios.' change sexo sexo tinyint(1) unsigned not null
;
update '.$tabla_usuarios.' set sexo=sexo-1
;
alter table '.$tabla_usuarios.' change descripcion descripcion text not null
;
alter table '.$tabla_usuarios.' change conectado fecha_conectado int(10) unsigned not null
;
alter table '.$tabla_usuarios.' add fecha_rec_contrasena int(10) unsigned not null
;
alter table '.$tabla_usuarios.' change rango_fijo rango_fijo tinyint(1) unsigned not null
;
update '.$tabla_usuarios.' set rango_fijo=rango_fijo-1
;
alter table '.$tabla_usuarios.' change gmt gmt tinyint(2) not null
;
alter table '.$tabla_usuarios.' drop index nick
;
alter table '.$tabla_usuarios.' add index (nick)
;
alter table '.$tabla_usuarios.' add index (contrasena)
;
update eforo_config set foro_url=\''.$_POST['foro_url'].'\',plantilla=\'electros\',estilo=\'electros\' where id=1
;
truncate table eforo_recientes
;
alter table eforo_recientes drop index usuarios
;
alter table eforo_recientes drop index id_usuario
;
alter table eforo_recientes add index (id_usuario)
' ;
$error = false ;
$codigo = explode(';',$codigo) ;
echo '<p><b>Actualizaciones realizadas:</b></p>' ;
foreach($codigo as $valor) {
	$valor = trim($valor) ;
	if(@$conectar->query($valor)) {
		echo "$valor<br />\n" ;
	}
	else {
		if(empty($error)) $error = true ;
		echo "<span style=\"border: #ee0000 1px solid ; background-color: #c00000 ; color: #fff\"><b>Error en:</b> $valor</span><br />\n" ;
	}
}
if(!$error) {
?>
<p>Listo, la actualizaci�n se ha finalizado con �xito.</p>
<input type="button" onclick="location = '../foro.php'" value="Finalizar" />
<?
}
else {
?>
<p>Hubo un error durante la actualizaci�n. Si el error es parecido a este "alter table nombre_tabla drop index nombre_indice" no te
preocupes, estas modificaciones varian dependiendo de la versi�n de eForo y por tanto pueden o no mostrar un error, sin
embargo el funcionamiento de eForo no es afectado. Si es un error distinto y eForo no est� funcionando, restaura la base de datos e
intenta de nuevo. Cualquier error cons�ltanos en nuestro foro en <a href="http://www.electros.net">http://www.electros.net</a>.</p>
<?
}
?>
<p>Si has elegido actualizar el eForo utilizando la compatibilidad con el sistema de usuarios, recuerda cambiar el nombre de la
variable <b>$tabla_usuarios</b> por el nombre de la tabla donde se guardan tus usuarios, para esto abre el archivo
<b>foroconfig.php</b> con un editor de texto puro como el Bloc de notas de Windows (notepad.exe).</p>
<input type="button" onclick="location = '../foro.php'" value="Finalizar" />
<?
}
?>
</body>
</html>
