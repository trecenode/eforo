<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2006
*** Sitio web: www.13node.com
*** Licencia: GNU General Public License
*************************************************

eForo - Comunidad de foros para que tus usuarios convivan y se sientan parte de tu web
Copyright � 2003-2006 Daniel Osorio "Electros"

This file is part of eForo.

eForo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

# PHP7
function mysqli_result($res,$row=0,$col=0){ 
    $numrows = mysqli_num_rows($res); 
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}

# * Medir el tiempo de carga del foro
function tiempo_carga() {
	list($a,$b) = explode(' ',microtime()) ;
	return $b + $a ;
}
$tiempo = tiempo_carga() ;
# * El reporte de errores mostrar� todo
@error_reporting(E_ALL) ;
# * Comprimir la p�gina (si el navegador acepta contenido comprimido y si el servidor tiene la librer�a zlib)
ob_start('ob_gzhandler') ;
# **************************
# *** Configuraci�n avanzada
# **************************
# * Nombre de las "cookies"
$c[0] = 'eforo_id' ; # --> ID del usuario
$c[1] = 'eforo_nick' ; # --> Nick del usuario
$c[2] = 'eforo_con' ; # --> Contrase�a del usuario
# * Sintaxis de URL (para integrar eForo como una secci�n de tu web o para uso con mod_rewrite)
# La URL se forma como se observa en el siguiente ejemplo:
# $u[0]foromensajes$u[1]$u[2]foro$u[4]10$u[3]tema$u[4]10$u[5]
# Si $u = array('','.php','?','&','=','') ;
# Entonces la URL ser�a:
# foromensajes.php?foro=10&tema=10
$u = array('','.php','?','&','=','') ;
# * Tabla donde se guardan los usuarios
$tabla_usuarios = 'eforo_usuarios' ;
# *** Fin configuraci�n avanzada
# * Pasar el valor de las cookies a variables
# Para simplificar el c�digo y evitarse muchas molestias.
$c_id = !empty($_COOKIE[$c[0]]) ? $_COOKIE[$c[0]] : '' ;
$c_nick = !empty($_COOKIE[$c[1]]) ? $_COOKIE[$c[1]] : '' ;
$c_con = !empty($_COOKIE[$c[2]]) ? $_COOKIE[$c[2]] : '' ;
# * Conectar a base de datos
require 'config.php' ;
# * Comprobar datos insertados mediante la URL (ejem. foro.php?foro=1&tema=1)
# Con esto se evitar�n ataques de SQL Injection y otros parecidos.
unset($error) ;
if(!empty($_GET['foro'])) {
	if(ereg('^[0-9]+$',$_GET['foro'])) {
		# --> Comprueba si existe el subforo
		$con = $conectar->query("select count(id) from eforo_foros where id='{$_GET['foro']}'") ;
		if(!mysqli_result($con,0,0)) $error = 'No existe el subforo.' ;
		mysqli_free_result($con) ;
	}
	else {
		$error = 'Intento de ataque.' ;
	}
}
if(!empty($_GET['tema'])) {
	if(ereg('^[0-9]+$',$_GET['tema'])) {
		# --> Comprueba si existe el tema
		$con = $conectar->query("select count(id) from eforo_mensajes where id_foro='{$_GET['foro']}' and id='{$_GET['tema']}'") ;
		if(!mysqli_result($con,0,0)) $error = 'No existe el tema.' ;
		mysqli_free_result($con) ;
	}
	else {
		$error = 'Intento de ataque.' ;
	}
}
if(!empty($_GET['mensaje'])) {
	if(ereg('^[0-9]+$',$_GET['mensaje'])) {
		# --> Comprueba si existe el mensaje
		$con = $conectar->query("select count(id) from eforo_mensajes where id_foro='{$_GET['foro']}' and id_tema='{$_GET['tema']}' and id='{$_GET['mensaje']}'") ;
		if(!mysqli_result($con,0,0)) $error = 'No existe el mensaje.' ;
		mysqli_free_result($con) ;
	}
	else {
		$error = 'Intento de ataque.' ;
	}
}
if(isset($error)) exit('<p><b>Error</b></p><p>'.$error.'</p><script>setTimeout(\'history.back()\',1500)</script>') ;
# * Cargar configuraci�n del foro (todo se guardar� en un array llamado $conf)
unset($conf) ;
$con = $conectar->query('select * from eforo_config limit 1') ;
$datos = mysqli_fetch_assoc($con) ;
$conf['admin_id'] = explode(',',$datos['id_administrador']) ;
$conf['admin_email'] = $datos['email'] ;
$conf['foro_url'] = $datos['foro_url'] ;
$conf['foro_titulo'] = $datos['foro_titulo'] ;
$conf['max_temas'] = $datos['temas'] ;
$conf['max_mensajes'] = $datos['mensajes'] ;
$conf['max_ultimos'] = $datos['ultimos'] ;
$conf['permitir_codigo'] = $datos['codigo'] ;
$conf['permitir_caretos'] = $datos['caretos'] ;
$conf['permitir_firma'] = $datos['firma'] ;
$conf['censurar_palabras'] = $datos['censurar'] ;
$conf['notificacion_email'] = $datos['notificacion'] ;
$conf['plantilla'] = 'eforo_plantillas/'.$datos['plantilla'].'/' ;
$conf['estilo'] = $conf['plantilla'].'estilos/'.$datos['estilo'].'.css' ;
$conf['avatar_largo'] = $datos['avatarlargo'] ;
$conf['avatar_ancho'] = $datos['avatarancho'] ;
$conf['avatar_tamano'] = $datos['avatartamano'] ;
$conf['max_privados'] = $datos['privados'] ;
$conf['adjunto_tamano'] = $datos['adjuntotamano'] ;
$conf['adjunto_ext'] = $datos['adjuntoext'] ;
$conf['adjunto_nombre'] = $datos['adjuntonombre'] ;
# * Obtener datos del usuario que est� conectado en este momento
$es_usuario = false ;
if($c_id && $c_nick && $c_con) {
	$con = $conectar->query("select mensajes,rango,rango_fijo,fecha_conectado,gmt from $tabla_usuarios where id='$c_id' and nick='$c_nick' and contrasena='$c_con'") ;
	if(mysqli_num_rows($con)) {
		$es_usuario = true ;
		$datos = mysqli_fetch_assoc($con) ;
		# Rango actual
		if($datos['rango_fijo'] == 0) {
			# Todo usuario registrado tiene rango 1, se aumenta dependiendo de su n�mero
			# de mensajes � si es designado manualmente
			$usuario['rango'] = 1 ;
			$con2 = $conectar->query("select rango,minimo from eforo_rangos where minimo!='0' order by rango asc") ;
			while($datos2 = mysqli_fetch_row($con2)) {
				if($datos['mensajes'] >= $datos2[1]) {
					$usuario['rango'] = $datos2[0] ;
					break ;
				}
			}
			mysqli_free_result($con2) ;
		}
		else {
			$usuario['rango'] = $datos['rango'] ;
		}
		# Fecha de la ultima vez que estuvo en el foro
		$usuario['ultima_vez'] = $datos['fecha_conectado'] ;
		# La zona horaria o GMT (diferencia de horas)
		$usuario['gmt'] = $datos['gmt'] ;
	}
	mysqli_free_result($con) ;
}
else {
	$usuario['rango'] = 0 ;
	$usuario['gmt'] = 0 ;
}
# * Obtener el nick del usuario a trav�s de su ID
function usuario($a) {
	$con = $conectar->query("select nick from {$GLOBALS['tabla_usuarios']} where id='$a'") ;
	$datos = mysqli_fetch_row($con) ;
	$nick = $datos[0] ;
	mysqli_free_result($con) ;
	return $nick ? $nick : false ;
}
# * La fecha que ser� usada en el foro (por defecto se usar� la fecha GMT)
# Si el usuario eligi� la zona GMT de su pa�s, se sumar� o restar� la diferencia de horas
# con respecto a la fecha GMT
$fecha = time() ;
# * Fecha (Formato: 1 Ene 2004 12:00 AM)
function fecha($a) {
	$fecha_actual = $GLOBALS['fecha'] - $a ;
	switch(true) {
		# --> De 0 a 59 minutos
		case $fecha_actual > 0 && $fecha_actual < 3600 :
			$minutos = round($fecha_actual / 60) ;
			return $minutos == 0 || $minutos == 1 ? 'Hace 1 minuto' : "Hace $minutos minutos" ;
			break ;
		# --> De 1 a 11 horas 59 minutos
		case $fecha_actual >= 3600 && $fecha_actual < 43200 :
			$horas = round($fecha_actual / 3600) ;
			return $horas == 1 ? 'Hace 1 hora' : "Hace $horas horas" ;
			break ;
		# --> De 12 horas en adelante se muestra la fecha completa
		default :
			$gmt = $a + 3600 * $GLOBALS['usuario']['gmt'] ;
			$meses = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic') ;
			return date('j',$gmt).' '.$meses[date('n',$gmt)].' '.date('Y',$gmt).' '.date('h:i A',$gmt) ;
	}
}
# * Comprobar si la variable $_SERVER['HTTP_REFERER'] est� disponible
if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = "$u[0]foro$u[1]" ;
# * Se obtienen los usuarios en l�nea en el foro
$tiempo_limite = 600 ; # <-- Tiempo en segundos en el cu�l se considerar� al usuario en l�nea
$fecha_limite = $fecha - $tiempo_limite ;
# --> Se eliminan los usuarios que superaron el tiempo l�mite
$conectar->query("delete from eforo_enlinea where fecha<'$fecha_limite'") ;
# --> Si es un usuario registrado se guarda su ID
if($c_id) {
	$con = $conectar->query("select count(fecha) from eforo_enlinea where id_usuario='$c_id'") ;
	if(mysqli_result($con,0,0)) {
		$conectar->query("update eforo_enlinea set fecha='$fecha' where id_usuario='$c_id'") ;
	}
	else {
		$conectar->query("delete from eforo_enlinea where ip='{$_SERVER['REMOTE_ADDR']}'") ;
		$conectar->query("insert into eforo_enlinea (fecha,id_usuario) values ('$fecha','$c_id')") ;
	}
	mysqli_free_result($con) ;
}
# --> Si es un usuario no registrado se guarda su IP
else {
	$con = $conectar->query("select count(fecha) from eforo_enlinea where ip='{$_SERVER['REMOTE_ADDR']}'") ;
	if(mysqli_result($con,0,0)) {
		$conectar->query("update eforo_enlinea set fecha='$fecha' where ip='{$_SERVER['REMOTE_ADDR']}'") ;
	}
	else {
		$conectar->query("insert into eforo_enlinea (fecha,ip) values ('$fecha','{$_SERVER['REMOTE_ADDR']}')") ;
	}
	mysqli_free_result($con) ;
}
# --> Se obtiene el total de usuarios an�nimos
$con = $conectar->query("select count(ip) from eforo_enlinea where ip!=''") ;
$total_en_linea[0] = mysqli_result($con,0,0) ;
mysqli_free_result($con) ;
# --> Se obtiene el total de usuarios registrados y sus nombres
$con = $conectar->query("select id_usuario from eforo_enlinea where id_usuario!='0' order by fecha asc") ;
$total_en_linea[1] = mysqli_num_rows($con) ;
$reg_en_linea = array() ;
while($datos = mysqli_fetch_row($con)) {
	$reg_en_linea[] = "<a href=\"$u[0]forousuarios$u[1]$u[2]u$u[4]$datos[0]$u[5]\" class=\"eforo_enlace\">".usuario($datos[0])."</a>" ;
}
$reg_en_linea = implode(', ',$reg_en_linea) ;
mysqli_free_result($con) ;
# * Cargar clase ePiel
require 'eforo_funciones/epiel.php' ;
$ePiel = new ePiel() ;
?>