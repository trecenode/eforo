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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>eForo v3.1 - Instalación</title>
<style type="text/css">
body {
font-family: verdana, sans-serif ;
font-size: 10pt ;
color: #000 ;
margin: 100px ;
}
a {
font-weight: bold ;
color: #000 ;
text-decoration: none ;
}
a:hover {
text-decoration: underline ;
}
</style>
</head>
<body>
<h3>eForo v3.1 - Instalación</h3>
<?php
require 'config.php' ;
if(!isset($_POST['enviar'])) {
?>
<p><b>Aviso:</b> Si estás actualizando eForo desde una versión anterior, entra <a href="actualizar.php">aquí</a>.
<p>Gracias por tu interés en eForo, antes de comenzar con la instalación recuerda haber configurado el archivo <b>config.php</b> con tus datos
de conexión a la base de datos. Ahora se comprobará la configuración de <b>config.php</b>.</p>
<div style="border: #000000 1px solid ; background-color: #cddff0 ; padding: 5px">
Comprobando configuración de <b>config.php</b>...<br /><br />
<?php
if($conectar) echo 'El archivo <b>config.php</b> está configurado correctamente.' ;
?>
</div>
<p>A continuación deberás completar la configuración previa a la instalación.</p>
<script type="text/javascript">
function comprobar(a) {
	if(a.administrador.value == '') {
		alert('Debes escribir un nick.') ;
		return false ;
	}
	if(a.contrasena.value == '') {
		alert('Debes escribir una contraseña.') ;
		return false ;
	}
	if(a.contrasena.value != a.contrasena_c.value) {
		alert('Las contraseñas son diferentes.') ;
		return false ;
	}
}
</script>
<form method="post" action="instalar.php" onsubmit="return comprobar(this)">
<fieldset>
<legend>Configuración</legend><br />
<b>Nick del administrador:</b><br />
<input type="text" size="25" name="administrador" /><br />
<b>contraseña del administrador:</b><br />
<input type="password" size="25" name="contrasena" /><br />
<b>Confirmar contraseña:</b><br />
<input type="password" size="25" name="contrasena_c" /><br />
<b>URL donde está instalado eForo (ej. https://www.tuweb.com/eforo/):</b><br />
<input type="text" size="50" name="foro_url" value="http://<?=$_SERVER['HTTP_HOST'].str_replace('instalar.php','',$_SERVER['PHP_SELF'])?>" /><br />
<b>Tipo de instalación:</b><br />
<input type="radio" id="instalacion1" name="instalacion" value="1" onclick="tabla_usuarios.disabled = true ; tabla_usuarios.value = 'eforo_usuarios'" checked="checked" /><label for="instalacion1">Instalación típica</label><br />
<input type="radio" id="instalacion2" name="instalacion" value="2" onclick="tabla_usuarios.disabled = false ; tabla_usuarios.value = ''" /><label for="instalacion2">Compatible con script de sistema de usuarios de www.electros.net</label><br />
<b>Tabla de usuarios:</b><br />
<input type="text" size="20" name="tabla_usuarios" value="eforo_usuarios" disabled="disabled" />
</fieldset><br /><br />
<input type="submit" name="enviar" value="Comenzar Instalación" />
</form>
<?php
}
else {
$actualDate = date_create();
$fecha =  "NOW()"; //date_timestamp_get($actualDate);

$admin_contrasena = md5(md5($_POST['contrasena'])) ;
switch($_POST['instalacion']) {
	case 1 :
		$tabla_usuarios = 'eforo_usuarios' ;
		$usuario = true ;
		break ;
	case 2 :
		$tabla_usuarios = $_POST['tabla_usuarios'] ;
		$con = $conectar->query("select id,contrasena from $tabla_usuarios where nick='{$_POST['administrador']}'") ;
		if(mysqli_num_rows($con,0,0)) {
			$datos = mysqli_fetch_row($con) ;
			$usuario = false ;
			$admin_id = $datos[0] ;
			if($admin_contrasena != $datos[1]) $conectar->query("update $tabla_usuarios set contrasena='$admin_contrasena' where id='$admin_id'") ;
		}
		else {
			$usuario = "insert into $tabla_usuarios (fecha_registrado,nick,contrasena,fecha_conectado) values (".$fecha.",'{$_POST['administrador']}','".$admin_contrasena."',".$fecha.")" ;
		}
}
$mensaje_titulo = 'Gracias por usar eForo v3.1' ;
$mensaje_contenido =
'eForo v3.1 es el resultado de muchos meses de trabajo. Su código ha
sido reescrito desde cero con lo que se ha conseguido reducirse
hasta en un 60% en comparación con la versión v.2.2.1. Esta versión
incorpora un sistema de plantillas, ePiel v1.0 que permitirá poder
separar el código PHP del diseño del foro, consiguiendo que éste se
pueda modificar fácilmente, además de poder crear varios diseños e
intercambiarlos con sólo seleccionarlo en la configuración en el panel
de control.

eForo ha sido publicado bajo la licencia GNU General Public License,
lo que te permite realizar modificaciones en su código y redistribuirlo,
prohibiéndose incluirlo ya sea parcial o totalmente en un software
privativo, para más información visita www.gnu.org.

Por favor, no elimines el enlace a nuestra web, puedes moverlo a otra
sección de tu web si lo deseas, siempre y cuando sea visible tanto para
la gente como para los buscadores, así permitirás que el uso de eForo
crezca y más personas puedan utilizar eForo.

Si tienes un foro phpBB, vBulletin o similar y deseas transferir
tus mensajes a eForo, escríbenos en nuestro foro en electros.net/foro.

Agradezco tu interés en usar eForo v3.1, espero que te sea de utilidad
y que lo disfrutes.

Electros
' ;
$codigo =
"create table eforo_adjuntos (
id smallint(5) unsigned not null auto_increment,
id_mensaje mediumint(8) unsigned not null,
archivo varchar(100) not null,
descargas mediumint(8) unsigned not null,
primary key (id)
)
;
create table eforo_categorias (
id tinyint(3) unsigned not null auto_increment,
orden smallint(5) unsigned not null,
categoria varchar(100) not null,
primary key (id),
key (orden)
)
;
create table eforo_config (
id tinyint(3) unsigned not null auto_increment,
id_administrador varchar(100) not null,
email varchar(100) not null,
foro_url varchar(100) not null,
foro_titulo varchar(100) not null DEFAULT 'eForo',
temas tinyint(3) unsigned not null,
mensajes tinyint(3) unsigned not null,
ultimos tinyint(3) unsigned not null,
codigo tinyint(1) unsigned not null,
caretos tinyint(1) unsigned not null,
url tinyint(1) unsigned not null,
firma tinyint(1) unsigned not null,
censurar tinyint(1) unsigned not null,
notificacion tinyint(1) unsigned not null,
plantilla varchar(100) not null,
estilo varchar(100) not null,
avatarlargo smallint(5) unsigned not null,
avatarancho smallint(5) unsigned not null,
avatartamano smallint(5) unsigned not null,
privados smallint(5) unsigned not null,
adjuntotamano smallint(5) unsigned not null,
adjuntoext text not null,
adjuntonombre tinyint(3) unsigned not null,
primary key (id)
)
;
create table eforo_enlinea (
fecha datetime,
ip varchar(15) not null,
id_usuario mediumint(8) not null,
key (fecha)
)
;
create table eforo_foros (
id smallint(5) unsigned not null auto_increment,
orden smallint(5) unsigned not null,
id_categoria tinyint(3) unsigned not null,
foro varchar(100) not null,
descripcion text not null,
num_temas mediumint(8) unsigned not null,
num_mensajes mediumint(8) unsigned not null,
p_leer smallint(5),
p_nuevo smallint(5),
p_responder smallint(5),
p_editar smallint(5),
p_borrar smallint(5),
p_importante smallint(5),
p_adjuntar smallint(5),
primary key (id),
key (orden),
key (id_categoria)
)
;
create table eforo_mensajes (
id mediumint(8) unsigned not null auto_increment,
id_foro tinyint(3) unsigned not null,
id_tema mediumint(8) unsigned not null,
num_visitas mediumint(8) unsigned not null DEFAULT '0',
num_respuestas mediumint(8) unsigned not null DEFAULT '0',
fecha datetime,
id_usuario mediumint(8) unsigned not null DEFAULT '1',
tema varchar(100) not null,
mensaje text not null,
o_caretos tinyint(1) unsigned,
o_codigo tinyint(1) unsigned,
o_url tinyint(1) unsigned,
o_firma tinyint(1) unsigned,
o_importante tinyint(1) unsigned,
o_notificacion tinyint(1) unsigned,
o_notificacion_email tinyint(1) unsigned,
fecha_editado datetime,
fecha_ultimo datetime,
cerrado tinyint(1) unsigned not null DEFAULT '1',
primary key (id),
key (id_foro),
key (id_tema)
)
;
create table eforo_moderadores (
id smallint(5) unsigned not null auto_increment,
id_foro smallint(5) unsigned not null,
id_usuario mediumint(8) unsigned not null,
primary key (id)
)
;
create table eforo_privados (
id mediumint(8) unsigned not null auto_increment,
leido tinyint(1) unsigned not null,
fecha datetime,
id_remitente mediumint(8) not null,
id_destinatario mediumint(8) not null,
mensaje text not null,
primary key (id),
key (id_destinatario)
)
;
create table eforo_rangos (
rango smallint(5) not null,
minimo smallint(5) unsigned not null DEFAULT '1',
descripcion varchar(100) not null,
primary key (rango)
)
;
create table eforo_recientes (
id_usuario mediumint(8) unsigned not null,
fecha datetime,
id_foro smallint(5) unsigned not null,
id_mensaje mediumint(8) unsigned not null,
primary key (id_mensaje),
key (id_usuario)
)
" ;
if($_POST['instalacion'] == 1) {
$codigo .=
";
create table eforo_usuarios (
id mediumint(8) unsigned not null auto_increment,
fecha_registrado datetime not null,
nick varchar(20) not null,
contrasena varchar(32) not null,
email varchar(50) not null default '',
pais varchar(20) not null default '',
edad tinyint(2) unsigned not null default '18',
sexo tinyint(1) unsigned not null default '0',
descripcion text,
web varchar(100),
ip varchar(15),
firma text,
mensajes smallint(5) unsigned,
rango smallint(5),
rango_fijo tinyint(1) unsigned,
fecha_conectado datetime,
fecha_rec_contrasena datetime,
gmt tinyint(2),
avatar char(3),
primary key (id),
key (nick),
key (contrasena)
)
" ;
}
$codigo .=
";
insert into $tabla_usuarios (fecha_registrado,nick,contrasena,fecha_conectado) values (".$fecha.",'{$_POST['administrador']}','".$admin_contrasena."',".$fecha.")
;
insert into eforo_categorias (orden,categoria) values ('10','Categoría de ejemplo')
;
insert into eforo_foros (orden,id_categoria,foro,descripcion,num_temas,num_mensajes) values ('10','1','Foro de ejemplo','Descripción.','1','1')
;
insert into eforo_mensajes (id_foro,id_tema,fecha,tema,mensaje,fecha_editado,fecha_ultimo) values ('1','1',".$fecha.",'".$mensaje_titulo."','".$mensaje_contenido."',".$fecha.",".$fecha.")
;
insert into eforo_rangos (rango,descripcion) values ('-1','Banead@')
;
insert into eforo_rangos (rango,descripcion) values ('0','Anónim@')
;
insert into eforo_rangos (rango,descripcion) values ('1','Nuev@')
;
insert into eforo_rangos (rango,descripcion) values ('500','Moderador')
;
insert into eforo_rangos (rango,descripcion) values ('999','Administrador')
" ;
if($_POST['instalacion'] == 2) {
$codigo .=
";
alter table $tabla_usuarios change fecha fecha_registrado int(10) unsigned not null
;
alter table $tabla_usuarios add firma text not null
;
alter table $tabla_usuarios add mensajes smallint(5) unsigned not null
;
alter table $tabla_usuarios add rango smallint(5) not null
;
alter table $tabla_usuarios add rango_fijo tinyint(1) unsigned not null
;
alter table $tabla_usuarios add fecha_conectado int(10) unsigned not null
;
alter table $tabla_usuarios add fecha_rec_contrasena int(10) unsigned not null
;
alter table $tabla_usuarios add gmt tinyint(2) not null
;
alter table $tabla_usuarios add avatar char(3) not null
;
update $tabla_usuarios set rango_fijo='1' where rango='-1'
;
update $tabla_usuarios set rango_fijo='1' where rango='500'
;
update $tabla_usuarios set rango_fijo='1' where rango='999'
" ;
}
$codigo = explode(';',$codigo) ;
foreach($codigo as $linea) {
$linea = trim($linea) ;
//$conectar->query($linea) ;

if (mysqli_query($conectar, $linea)) {
	echo "<br><span style='color:green;'>New record created successfully</span><br>" . $linea . "<br>";
} else {
	echo "<br><span style='color:red;'>Error:</span>" . $linea . "<br><span style='color:red;'>" . mysqli_error($conectar) . "</span><br>";
}
}
if($usuario) {
	$conectar->query($usuario);
	$admin_id = $conectar->insert_id;
}

$latestQuery = "insert into eforo_config
(id_administrador,email,foro_url,temas,mensajes,ultimos,codigo,caretos,url,firma,censurar,notificacion,plantilla,estilo,avatarlargo,avatarancho,avatartamano,privados,adjuntotamano,adjuntoext,adjuntonombre)
values
('".$admin_id."','nombre@email.com','{$_POST['foro_url']}','25','20','20','1','1','1','1','0','1','electros','electros','150','150','30','100','512','zip\r\nrar\r\ntxt\r\nrtf\r\ngif\r\njpg\r\njpeg\r\npng\r\ndoc\r\nxls\r\nppt\r\npps\r\npdf\r\nmid\r\nswf\r\nmpg\r\nmpeg\r\navi\r\nwma\r\nwmv','32')
";

if (mysqli_query($conectar, $latestQuery)) {
	echo "<br><span style='color:green;'>New record created successfully</span><br>" . $latestQuery . "<br>";
} else {
	echo "<br><span style='color:red;'>Error:</span>" . $latestQuery . "<br><span style='color:red;'>" . mysqli_error($conectar) . "</span><br>";
}
?>
<p style="font-size: 12pt"><b>Instalación completada</b>
<p>La instalación se ha completado. Ya puedes disfrutar de eForo.
<p>Recuerda darle permiso CHMOD 777 a la carpeta <b>avatares</b> que se encuentra dentro de <b>eforo_imagenes</b>, para esto entra desde cualquier
programa FTP, haz click derecho sobre la carpeta y busca una opción que diga CHMOD, Permisos o Propiedades.
<?php
if($_POST['instalacion'] == 1) {
?>
<p>Se ha creado un nuevo usuario llamado <b><?php echo $_POST['administrador']?></b> con la contraseña <b><?php echo $_POST['contrasena']?></b>
el cuál será administrador. Para administrar eForo inicia sesión con este usuario y en el menú selecciona la opción <b>Panel de
control</b>.</p>
<?php
}
else {
?>
<p>Se ha seleccionado al usuario <b><?php echo $_POST['administrador']?></b> como administrador. Para administrar eForo inicia sesión
con este usuario y en el menú selecciona la opción <b>Panel de control</b>.</p>
<?php
}
?>
<p>Si has elegido actualizar el eForo utilizando la compatibilidad con el sistema de usuarios, recuerda cambiar el valor de
la variable <b>$tabla_usuarios</b> por el nombre de la tabla donde se guardan tus usuarios, para esto abre el archivo
<b>foroconfig.php</b> con un editor de texto puro como el Bloc de notas de Windows (notepad.exe) y vuelve a subir el archivo.</p>
<p><span style="font-size: 12pt ; color: #aa0000"><b>¡No olvides eliminar los archivos <b>instalar.php</b>, <b>actualizar.php</b>
y la carpeta <b>eforo_parches</b> al terminar la instalación!</b></span></p>
<input type="button" value="Finalizar" onclick="location = 'foro.php'">
<?php
}
?>
</body>
</html>