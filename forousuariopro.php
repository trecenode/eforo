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

ob_start() ;
require 'foroconfig.php' ;
require 'eforo_funciones/quitar.php' ;
require 'eforo_funciones/aviso.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Usuario',
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
function email($email) {
	if(!preg_match('/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})$/',$email)) aviso('Error','Debes escribir un email v�lido. Haz click <a href="javascript:history.back()" class="eforo_enlace">aqu�</a> para regresar.',1) ;
	return $email ;
}
$_GET['que'] = !empty($_GET['que']) ? $_GET['que'] : '' ;
switch($_GET['que']) {
	case 'entrar' :
		if(isset($_POST['enviar'])) {
			$nick = quitar($_POST['u_nick'],1) ;
			$contrasena = md5(md5(quitar($_POST['u_contrasena'],1))) ;
			$con = $conectar->query("select id,contrasena from $tabla_usuarios where nick='$nick'") ;
			if(mysqli_num_rows($con)) {
				$datos = mysqli_fetch_row($con) ;
				if($datos[1] == $contrasena) {
					setcookie($c[0],$datos[0],time()+604800) ;
					setcookie($c[1],$nick,time()+604800) ;
					setcookie($c[2],$contrasena,time()+604800) ;
					if(!empty($_POST['url_regresar'])) {
						header('location: '.$_POST['url_regresar']) ;
					}
					else {
						header('location: '.$_SERVER['HTTP_REFERER']) ;
					}
				}
				else {
					aviso('Contrase�a incorrecta','La contrase�a es incorrecta. Haz click <a href="javascript:history.back()" class="eforo_enlace">aqu�</a> para regresar.') ;
				}
			}
			else {
				aviso('Usuario no encontrado','Este usuario no existe en la base de datos. Haz click <a href="javascript:history.back()" class="eforo_enlace">aqu�</a> para regresar.') ;
			}
			mysqli_free_result($con) ;
		}
		else {
			header('location: '.$_SERVER['HTTP_REFERER']) ;
		}
		break ;
	case 'salir' :
		setcookie($c[0]) ;
		setcookie($c[1]) ;
		setcookie($c[2]) ;
		$conectar->query("delete from eforo_enlinea where id_usuario='$c_id'") ;
		header("location: {$conf['foro_url']}$u[0]foro$u[1]$u[5]") ;
		break ;
	case 'perfil' :
		if(!$es_usuario) aviso('Error','Necesitas iniciar sesi�n para poder editar tu perfil. Intenta iniciar sesi�n desde el men�.',1) ;
		$avatar = '' ;
		$contrasena = '' ;
		if(isset($_POST['enviar'])) {
			# * Subir el avatar
			if($_FILES['u_archivo']['name'] && empty($_POST['borrar'])) {
				# --> Se revisa que la extensi�n del archivo sea correcta
				$extensiones = array('gif','jpg','png') ;
				preg_match('/\.(\w+)$/i',$_FILES['u_archivo']['name'],$a) ;
				if(!in_array($a[1],$extensiones)) {
					aviso('Error','<p>La extensi�n '.$a[1].' no est� permitida.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
				}
				# --> Se comprueba el tama�o del archivo
				$tam_actual = round($_FILES['u_archivo']['size'] / 1024) ;
				if(!$tam_actual || $tam_actual > $conf['avatar_tamano']) {
					aviso('Error',"<p>El archivo debe ser menor de {$conf['avatar_tamano']} KB.<p><a href=\"javascript:history.back()\" class=\"eforo_enlace\">� Regresar</a>",1) ;
				}
				# --> Se comprueba el tama�o de la imagen en pixeles
				move_uploaded_file($_FILES['u_archivo']['tmp_name'],'eforo_imagenes/avatares/defecto.'.$a[1]) ;
				if(!list($largo,$ancho) = getimagesize('eforo_imagenes/avatares/defecto.'.$a[1])) {
					aviso('Error','La imagen no es v�lida.',1) ;
				}
				if($largo > $conf['avatar_largo'] || $ancho > $conf['avatar_ancho']) {
					unlink('eforo_imagenes/avatares/defecto.'.$a[1]) ;
					aviso('Error',"<p>El tama�o de la imagen debe ser menor de {$conf['avatar_largo']} x {$conf['avatar_ancho']} pixeles.<p><a href=\"javascript:history.back()\" class=\"eforo_enlace\">� Regresar</a>",1) ;
				}
				# --> Se elimina el avatar anterior
				$con = $conectar->query("select avatar from $tabla_usuarios where id='$c_id'") ;
				$datos = mysqli_fetch_row($con) ;
				if($datos[0]) unlink("eforo_imagenes/avatares/$c_id.$datos[0]") ;
				copy('eforo_imagenes/avatares/defecto.'.$a[1],"eforo_imagenes/avatares/$c_id.$a[1]") ;
				unlink('eforo_imagenes/avatares/defecto.'.$a[1]) ;
				$avatar = ",avatar='$a[1]'" ;
			}
			if(!empty($_POST['borrar'])) {
				$con = $conectar->query("select id,avatar from $tabla_usuarios where id='$c_id'") ;
				$datos = mysqli_fetch_row($con) ;
				unlink("eforo_imagenes/avatares/$datos[0].$datos[1]") ;
				$conectar->query("update $tabla_usuarios set avatar='' where id='$datos[0]'") ;
				mysqli_free_result($con) ;
			}
			if(!empty($_POST['u_contrasena'])) {
				$contrasena = md5(md5(quitar($_POST['u_contrasena'],1))) ;
				setcookie($c[2],$contrasena,time()+604800) ;
				$contrasena = ",contrasena='$contrasena'" ;
			}
			$nick = quitar($_POST['u_nick'],1) ;
			$con = $conectar->query("select count(id) from $tabla_usuarios where nick='$nick' limit 1") ;
			if(mysqli_result($con,0,0) && $c_nick != $nick) {
				aviso('Error','<p>El nick <b>'.$nick.'</b> ya existe.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
			}
			else {
				setcookie($c[1],$nick,time()+604800) ;
			}
			mysqli_free_result($con) ;
			$email = email(quitar($_POST['u_email'],1)) ;
			$pais = quitar($_POST['u_pais']) ;
			$edad = quitar($_POST['u_edad']) ;
			$sexo = quitar($_POST['u_sexo']) ;
			$descripcion = quitar($_POST['u_descripcion']) ;
			$web = quitar($_POST['u_web']) ;
			$firma = quitar($_POST['u_firma']) ;
			$gmt = quitar($_POST['u_gmt']) ;
			$conectar->query("update $tabla_usuarios set nick='$nick',email='$email',pais='$pais',edad='$edad',sexo='$sexo',descripcion='$descripcion',web='$web',firma='$firma',gmt='$gmt'$avatar$contrasena where id='$c_id'") ;
		}
		mysql_close($conectar) ;
		aviso('Perfil editado',"<p>Tu perfil ha sido editado.<p><a href=\"$u[0]forousuario$u[1]$u[2]que$u[4]perfil$u[5]\" class=\"eforo_enlace\">� Regresar al perfil</a><p><a href=\"$u[0]foro$u[1]$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
		break ;
	case 'registrar' :
		if(isset($_POST['enviar'])) {
			$nick = quitar($_POST['u_nick'],1) ;
			$email = email(quitar($_POST['u_email'],1)) ;
			$sexo = quitar($_POST['u_sexo']) ;
			$con = $conectar->query("select id from $tabla_usuarios where nick='$nick' or email='$email'") ;
			if(mysqli_num_rows($con)) {
				aviso('Error','<p>Este usuario ya existe en la base de datos o ya hay un usuario con este email. Haz click <a href="javascript:history.back()" class="eforo_enlace">aqu�</a> para regresar.') ;
			}
			else {
				$contrasena = md5(md5(quitar($_POST['u_contrasena'],1))) ;
				$conectar->query("insert into $tabla_usuarios (fecha_registrado,nick,contrasena,email,sexo,ip,rango,fecha_conectado) values ('$fecha','$nick','$contrasena','$email','$sexo','{$_SERVER['REMOTE_ADDR']}','1','$fecha')") ;
				$aviso_titulo = 'Bienvenid@ '.$nick ;
				$aviso_mensaje =
				"<p>Ya eres miembro de este foro, ahora podr�s tener tu propio perfil de usuario, escribir mensajes con tu nick, editar y borrar tus mensajes
				y muchas cosas m�s. Espero que te la pases bien por aqu� y que participes mucho.
				<p>Webmaster
				<p><a href=\"$u[0]foro$u[1]$u[5]\" class=\"eforo_enlace\">� Ir al foro</a>
				" ;
				aviso($aviso_titulo,$aviso_mensaje) ;
			}
		}
		break ;
	case 'contrasena' :
		# * Tiempo en el que se inhabilitar� la recuperaci�n de datos una vez que �stos se han enviado
		$tiempo_contrasena = 1800 ; # <-- Por defecto 30 minutos (1800 segundos)
		// * Generador de contrase�as
		$longitud = 8 ; # <-- N�mero de caract�res de la contrase�a
		$caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789' ;
		$contrasena = substr(str_shuffle($caracteres),0,$longitud - 1) ;
		$_POST['u_email'] = email(quitar($_POST['u_email'],1)) ;
		$con = $conectar->query("select id,nick,fecha_rec_contrasena from $tabla_usuarios where email='{$_POST['u_email']}' limit 1") ;
		if(mysqli_num_rows($con)) {
			$datos = mysqli_fetch_row($con) ;
			if($datos[2] < ($fecha - $tiempo_contrasena)) {
				$mensaje =
"<style type=\"text/css\">
body {
font-family: verdana, sans-serif ;
font-size: 10pt
}
a {
color: #000000 ;
font-weight: bold ;
text-decoration: none
}
</style>
<body>
<p>Estos son tus datos de registro:
<p>Nick: <b>$datos[2]</b><br>Contrase�a: <b>$contrasena</b>
<p>Debido a que la contrase�a se guarda encriptada no se pudo recuperar, por eso se te ha generado una nueva,
puedes cambiarla en cualquier momento en tu perfil. Para entrar al foro haz clic en la siguiente direcci�n:
<a href=\"{$conf['foro_url']}$u[0]foro$u[1]$u[5]\" target=\"_blank\">{$conf['foro_url']}/$u[0]foro$u[1]$u[5]</a>.
</body>
" ;
				mail($_POST['u_email'],"{$conf['foro_titulo']} � Recuperaci�n de contrase�a",$mensaje,"from: {$conf['admin_email']}\ncontent-type: text/html") ;
				$contrasena = md5(md5($contrasena)) ;
				$conectar->query("update $tabla_usuarios set contrasena='$contrasena',fecha_rec_contrasena='$fecha' where id='$datos[0]'") ;
				aviso('Datos enviados',"<p>Los datos han sido enviados al email indicado.</p><p><a href=\"$u[0]foro$u[1]$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a></p>") ;
			}
			else {
				aviso('Error','<p>S�lo puedes solicitar tus datos cada 30 minutos.</p><p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a></p>') ;
			}
		}
		else {
			aviso('Error','<p>Este email no existe en la base de datos.</p><p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a></p>') ;
		}
		mysqli_free_result($con) ;
	default :
		aviso('Error','No has seleccionado ninguna opci�n.') ;
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
ob_end_flush() ;
?>