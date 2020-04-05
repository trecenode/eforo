<?php
// *** Convertir de v.2.2.x a v3.0
// Este parche actualizar� la base de datos de tu antiguo foro para que sea compatible con la nueva versi�n.
include '../config.php' ;
?>
<style type="text/css">
body {
font-family: verdana,sans-serif ;
font-size: 10pt ;
margin: 100px ;
margin-left: 250px ;
margin-right: 250px
}
</style>
<?
if(!isset($_POST['enviar'])) {
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<p align="center"><b>Actualizaci�n de v.2.2.x a v3.0</b>
<p><span style="color: #aa0000"><b>Advertencia:</b></span> Se recomienda tener un respaldo reciente de la base de datos antes de comenzar con la actualizaci�n ya que algunos de los cambios efectuados no tendr�n forma de revertirse.
<p><b>� Compatibilidad con sistema "Registro de usuarios"</b>
<p>Si usaste alguna vez eForo en conjunto con el sistema "Registro de usuarios", deber�s indicar el nombre de la tabla en donde se almacenan tus usuarios, de otra forma d�jalo como est�.
<p><b>Tabla de usuarios:</b><br>
<input type="text" name="tabla_usuarios" value="eforo_usuarios"><br>
<p><b>� Encriptaci�n de contrase�as con md5()</b>
<p>Esta versi�n de eForo utiliza el sistema de usuarios con contrase�as encriptadas en md5(), as� que deber�s indicar si deseas que el parche encripte
las contrase�as.
<p>Si las contrase�as ya est�n encriptadas se crear� una nueva encriptaci�n la cu�l ser� irreversible, para evitar
esto observa si tus contrase�as se componen de 32 caract�res entre ellos letras y n�meros, por ejemplo:
8b87d55d6m32m2919811dib9slamn3ka.
<p><b>Encriptar contrasenas en md5():</b><br>
<input type="radio" name="contrasenas_md5" value="0" id="a" checked><label for="a">No</label> <input type="radio" name="contrasenas_md5" value="1" id="b"><label for="b">S�</label>
<p>Por �ltimo debes escribir el nick del administrador del foro, una vez finalizada la actualizaci�n podr�s designar a m�s de un administrador.
<p><b>Nick del administrador:</b><br>
<input type="text" name="administrador"><br><br>
<center><input type="submit" name="enviar" value="Actualizar"></center>
</form>
<?
}
else {
// * Cambiar los nick de usuario por su respectiva ID
// --> Mensajes (eforo_mensajes)
$con = $conectar->query("select id,usuario from eforo_mensajes order by id asc") ;
while($datos = mysql_fetch_array($con)) {
$con2 = $conectar->query("select id from $_POST[tabla_usuarios] where nick='$datos[usuario]'") ;
$datos2 = mysql_fetch_array($con2) ;
$conectar->query("update eforo_mensajes set usuario='$datos2[id]' where id='$datos[id]'") ;
mysqli_free_result($con2) ;
}
mysqli_free_result($con) ;
// --> Moderadores (eforo_moderadores)
$con = $conectar->query("select id,moderador from eforo_moderadores order by id asc") ;
while($datos = mysql_fetch_array($con)) {
$con2 = $conectar->query("select id from $_POST[tabla_usuarios] where nick='$datos[moderador]'") ;
$datos2 = mysql_fetch_array($con2) ;
$conectar->query("update eforo_moderadores set moderador='$datos2[id]' where id='$datos[id]'") ;
mysqli_free_result($con2) ;
}
mysqli_free_result($con) ;
// --> Privados (eforo_privados)
$con = $conectar->query("select remitente,destinatario from eforo_privados order by id asc") ;
while($datos = mysql_fetch_array($con)) {
$con2 = $conectar->query("select nick from $_POST[tabla_usuarios] where nick='$datos[remitente]'") ;
$datos2 = mysql_fetch_array($con2) ;
$con3 = $conectar->query("select nick from $_POST[tabla_usuarios] where nick='$datos[destinatario]'") ;
$datos3 = mysql_fetch_array($con3) ;
$conectar->query("update eforo_privados set remitente='$datos2[id]',destinatario='$datos3[id]' where id='$datos[id]'") ;
mysqli_free_result($con2) ;
mysqli_free_result($con3) ;
}
mysqli_free_result($con) ;

// * Transformar [codigo] en [cod]
$con = $conectar->query("select id,mensaje from eforo_mensajes order by id asc") ;
while($datos = mysql_fetch_array($con)) {
$mensaje = $datos[mensaje] ;
$mensaje = str_replace('[codigo]','[cod]',$mensaje) ;
$mensaje = str_replace('[/codigo]','[/cod]',$mensaje) ;
$mensaje = mysql_real_escape_string($mensaje) ;
$conectar->query("update eforo_mensajes set mensaje='$mensaje' where id='$datos[id]'") ;
}
mysqli_free_result($con) ;

$codigo =
"create table eforo_adjuntos (
id smallint(5) unsigned not null auto_increment,
id_mensaje smallint(5) unsigned not null,
archivo varchar(64) not null,
descargas smallint(5) unsigned not null,
primary key (id)
)
;
alter table eforo_config add id smallint(5) unsigned not null auto_increment primary key first
;
alter table eforo_config change administrador administrador varchar(100) not null
;
alter table eforo_config add urlforo varchar(100) not null after email
;
alter table eforo_config change codigo codigo enum('0','1') not null
;
alter table eforo_config change caretos caretos enum('0','1') not null
;
alter table eforo_config change url url enum('0','1') not null
;
alter table eforo_config add firma enum('0','1') not null
;
alter table eforo_config change censurar censurar enum('0','1') not null
;
alter table eforo_config add notificacion enum('0','1') not null after censurar
;
alter table eforo_config change estilo estilo varchar(100) not null
;
alter table eforo_config drop htmlcab
;
alter table eforo_config drop htmlpie
;
alter table eforo_config add adjuntotamano smallint(5) unsigned not null
;
alter table eforo_config add adjuntoext text not null
;
alter table eforo_config add adjuntonombre tinyint(3) unsigned not null
;
alter table eforo_enlinea change usuario id_usuario smallint(5) unsigned not null
;
alter table eforo_foros change categoria id_categoria tinyint(3) unsigned not null
;
alter table eforo_foros change temas num_temas smallint(5) unsigned not null
;
alter table eforo_foros change mensajes num_mensajes smallint(5) unsigned not null
;
alter table eforo_foros change leer p_leer smallint(5) not null
;
alter table eforo_foros change nuevo p_nuevo smallint(5) not null
;
alter table eforo_foros change responder p_responder smallint(5) not null
;
alter table eforo_foros change editar p_editar smallint(5) not null
;
alter table eforo_foros change borrar p_borrar smallint(5) not null
;
alter table eforo_foros add p_importante smallint(5) not null
;
alter table eforo_foros add p_adjuntar smallint(5) not null
;
alter table eforo_mensajes change id id mediumint(8) unsigned not null auto_increment
;
alter table eforo_mensajes change foro id_foro smallint(5) unsigned not null
;
alter table eforo_mensajes change forotema id_tema smallint(5) unsigned not null
;
alter table eforo_mensajes drop foromostrar
;
alter table eforo_mensajes change visitas num_visitas smallint(5) unsigned not null
;
alter table eforo_mensajes change mensajes num_respuestas smallint(5) unsigned not null
;
alter table eforo_mensajes change usuario id_usuario smallint(5) unsigned not null
;
alter table eforo_mensajes change caretos o_caretos enum('0','1') not null
;
alter table eforo_mensajes change codigo o_codigo enum('0','1') not null
;
alter table eforo_mensajes change url o_url enum('0','1') not null
;
alter table eforo_mensajes change firma o_firma enum('0','1') not null
;
alter table eforo_mensajes add o_importante enum('0','1') not null after o_firma
;
alter table eforo_mensajes change aviso o_notificacion enum('0','1') not null
;
alter table eforo_mensajes add o_notificacion_email enum('0','1') not null after o_notificacion
;
alter table eforo_mensajes change editado fecha_editado int(10) unsigned not null
;
alter table eforo_mensajes change ultimo fecha_ultimo int(10) unsigned not null
;
alter table eforo_moderadores change foro id_foro smallint(5) unsigned not null
;
alter table eforo_moderadores change moderador id_usuario smallint(5) not null
;
alter table eforo_privados change nuevo leido enum('0','1') not null
;
alter table eforo_privados change remitente id_remitente smallint(5) unsigned not null
;
alter table eforo_privados change destinatario id_destinatario smallint(5) unsigned not null
;
alter table eforo_recientes change usuario id_usuario smallint(5) unsigned not null
;
alter table eforo_recientes change foro id_foro smallint(5) unsigned not null
;
alter table eforo_recientes change mensaje id_mensaje smallint(5) unsigned not null
;
alter table $_POST[tabla_usuarios] change contrasena contrasena varchar(32) not null
;
alter table $_POST[tabla_usuarios] change rango rango smallint(5) unsigned not null
;
alter table $_POST[tabla_usuarios] add mensajes smallint(5) unsigned not null
;
alter table $_POST[tabla_usuarios] add rango smallint(5) unsigned not null
;
alter table $_POST[tabla_usuarios] add avatar char(3) not null
;
alter table $_POST[tabla_usuarios] add conectado int(10) unsigned not null
;
alter table $_POST[tabla_usuarios] add gmt tinyint(3) not null
;
alter table $_POST[tabla_usuarios] add rango_fijo enum('0','1') not null
;
update eforo_config set estilo='electros',codigo='1',caretos='1',url='1',firma='1',censurar='0'
;
update eforo_config set adjuntotamano='512',adjuntoext='zip\r\nrar\r\ntxt\r\nrtf\r\ngif\r\njpg\r\njpeg\r\npng\r\ndoc\r\nxls\r\nppt\r\npps\r\npdf\r\nmid\r\nswf\r\nmpg\r\nmpeg\r\navi\r\nwma\r\nwmv',adjuntonombre='32'
;
update $_POST[tabla_usuarios] set rango_fijo='1' where rango='-1'
;
update $_POST[tabla_usuarios] set rango_fijo='1' where rango='500'
;
update $_POST[tabla_usuarios] set rango_fijo='1' where rango='999'
" ;
$codigo = explode(';',$codigo) ;
foreach($codigo as $linea) {
@$conectar->query($linea) ;
}

// * Conversi�n de contrase�as a md5()
if($_POST[contrasenas_md5]) {
$conectar->query("update $_POST[tabla_usuarios] set contrasena=md5(md5(contrasena))") ;
}

//
$con = $conectar->query("select id from $_POST[tabla_usuarios] where nick='$_POST[administrador]'") ;
$datos = mysql_fetch_array($con) ;
$conectar->query("update eforo_config set administrador='$datos[id]'") ;
mysqli_free_result($con) ;
?>
<p align="center"><b>Actualizaci�n de la base de datos completada</b>
<p><b>Importante:</b> No te olvides de entrar al panel de control que se encuentra en el men� desplegable una vez que has iniciado sesi�n, deber�s llenar correctamente todos los campos y as� finalizar la actualizaci�n de eForo.
<p><b>Notas adicionales:</b> El parche <b>sincronizar.php</b> comprueba si las estad�sticas de n�mero de temas y mensajes son correctos, esto es porque en anteriores versiones pudieron haberse corrompido las estad�sticas y estas muestren datos err�neos.
De cualquier forma el usarlo no afectar� ni corromper� la base de datos as� que puedes aplicarlo con toda confianza.
<p>Para empezar a usar eForo da click en el siguiente bot�n.
<p>
<center><input type="button" value="Ir al foro" onclick="location='../foro.php'"></center>
<?
}
?>
