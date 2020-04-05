<?php
/*
*************************************************
*** eForo v3.0
*** Creado por: Electros <electros@electros.net>
*** Sitio web: www.13node.com
*** Licencia: GNU General Public License
*************************************************

--- P�gina: foroescribirpro.php ---

eForo - Una comunidad para que tus visitantes se comuniquen y se sientan parte de tu web
Copyright � 2003-2005 Daniel Osorio "Electros"

Este programa es software libre, puedes redistribuirlo y/o modificarlo bajo los t�rminos
de la GNU General Public License publicados por la Free Software Foundation; desde la
versi�n 2 de la licencia, o (si lo deseas) cualquiera m�s reciente.
*/

require 'foroconfig.php' ;
require 'eforo_funciones/aviso.php' ;
require 'eforo_funciones/sesion.php' ;
$ePiel->cargar(array(
'cabecera' => $conf['plantilla'].'cabecera.pta',
'piedepagina' => $conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de moderaci�n � Mover',
'estilo' => $conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
require 'foromenu.php' ;
switch(true) {
	case !empty($_GET['foro']) && empty($_GET['tema']) && empty($_GET['mensaje']) :
		$que = 1 ;
		$permiso = 'p_nuevo' ;
		break ;
	case !empty($_GET['foro']) && !empty($_GET['tema']) && empty($_GET['mensaje']) :
		$que = 2 ;
		$permiso = 'p_responder' ;
		break ;
	case !empty($_GET['foro']) && !empty($_GET['tema']) && !empty($_GET['mensaje']) :
		$que = 3 ;
		$permiso = 'p_editar' ;
		break ;
	default :
		aviso('Error','No se ha escrito ning�n mensaje.',1) ;
}
# * Comprobar si el tema est� cerrado
if($que != 1) {
	$con = $conectar->query("select count(id) from eforo_mensajes where id='{$_GET['tema']}' and cerrado='1'") ;
	if(mysqli_result($con,0,0)) aviso('Error','El tema est� cerrado y no se puede responder ni editar mensajes.',1) ;
}
# * Comprobar permiso de usuario
if(!$es_moderador) permiso($permiso) ;
# * El mensaje se guardar� dependiendo de lo que se haya elegido (escribir, responder o editar el mensaje)
if(isset($_POST['enviar'])) {
	# Funci�n para adjuntar archivos a los mensajes
	if(!empty($_FILES['m_archivo'])) {
		# --> Se comprueba el tama�o del archivo adjunto
		$tamano_max = @ini_get('upload_max_filesize') ? str_replace('M','',ini_get('upload_max_filesize')) * 1024 : 2048 ;
		if($tamano_max < $conf['adjunto_tamano']) $conf['adjunto_tamano'] = $tamano_max ;
		if(!$_FILES['m_archivo']['size'] || $_FILES['m_archivo']['size'] > ($conf['adjunto_tamano'] * 1024)) {
			aviso('Error al subir el archivo','<p>El archivo debe ser menor de <b>'.$conf['adjunto_tamano'].' KB</b>.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
		}
		# --> Se comprueba si la extensi�n est� permitida
		preg_match('/(.+)\.([\w_]+)/i',$_FILES['m_archivo']['name'],$parte) ;
		$nombre_archivo = $parte[1] ;
		$extension_archivo = strtolower($parte[2]) ;
		if(!in_array($extension_archivo,$conf['adjunto_ext'])) {
			aviso('Error al subir el archivo','<p>La extensi�n <b>'.$extension_archivo.'</b> no est� permitida.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
		}
		# --> Se comprueba el n�mero de caract�res en el nombre de archivo
		if(strlen($nombre_archivo) > $conf['adjunto_nombre']) {
			aviso('Error al subir el archivo','<p>El nombre de archivo debe ser menor de <b>'.$conf['adjunto_nombre'].'</b> caract�res.<p><a href="javascript:history.back()" class="eforo_enlace">� Regresar</a>',1) ;
		}
		# --> Se guarda el nombre real del archivo en la base de datos
		$conectar->query("insert into eforo_adjuntos (archivo) values ('{$_FILES['m_archivo']['name']}')") ;
		# --> El archivo se guardar� con el n�mero del �ltimo registro en la base de datos
		$id_adjunto = mysql_insert_id() ;
		move_uploaded_file($_FILES['m_archivo']['tmp_name'],"eforo_adjuntos/$id_adjunto.dat") ;
	}
	require 'eforo_funciones/quitar.php' ;
	$_POST['m_caretos'] = !empty($_POST['m_caretos']) ? quitar($_POST['m_caretos']) : 0 ;
	$_POST['m_codigo'] = !empty($_POST['m_codigo']) ? quitar($_POST['m_codigo']) : 0 ;
	$_POST['m_firma'] = !empty($_POST['m_firma']) ? quitar($_POST['m_firma']) : 0 ;
	$_POST['m_notificacion'] = !empty($_POST['m_notificacion']) ? quitar($_POST['m_notificacion']) : 0 ;
	$_POST['m_importante'] = !empty($_POST['m_importante']) ? quitar($_POST['m_importante']) : 0 ;
	if($que == 3 && $_GET['tema'] != $_GET['mensaje']) $_POST['m_notificacion'] = 0 ;
	# * Si el rango del usuario es menor al requerido para marcar temas como importantes entonces se desactiva esta opci�n
	$con = $conectar->query("select p_importante from eforo_foros where id='{$_GET['foro']}'") ;
	if($usuario['rango'] < mysqli_result($con,0,0) && !$es_moderador) $_POST['m_importante'] = 0 ;
	mysqli_free_result($con) ;
	switch($que) {
		# --> Escribir un nuevo tema
		case 1 :
			$_POST['m_tema'] = quitar($_POST['m_tema'],1) ;
			$_POST['m_mensaje'] = quitar($_POST['m_mensaje'],1) ;
			$conectar->query("insert into eforo_mensajes (id_foro,fecha,id_usuario,tema,mensaje,o_caretos,o_codigo,o_firma,o_importante,o_notificacion,fecha_ultimo) values ('{$_GET['foro']}','$fecha','$c_id','{$_POST['m_tema']}','{$_POST['m_mensaje']}','{$_POST['m_caretos']}','{$_POST['m_codigo']}','{$_POST['m_firma']}','{$_POST['m_importante']}','{$_POST['m_notificacion']}','$fecha')") ;
			$id_ultimo = mysql_insert_id() ;
			$conectar->query("update eforo_mensajes set id_tema='$id_ultimo' where id='$id_ultimo'") ;
			$conectar->query("update eforo_foros set num_temas=num_temas+1,num_mensajes=num_mensajes+1 where id='{$_GET['foro']}'") ;
			if($c_id) $conectar->query("update $tabla_usuarios set mensajes=mensajes+1 where id='$c_id'") ;
			aviso('Confirmaci�n',"<p>Tu mensaje ha sido publicado.<p><a href=\"$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]$id_ultimo$u[5]\" class=\"eforo_enlace\">� Ir al mensaje</a>\n<p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
			break ;
		# --> Responder al tema
		case 2 :
			if(!empty($_POST['m_tema'])) $_POST['m_tema'] = quitar($_POST['m_tema']) ;
			$_POST['m_mensaje'] = quitar($_POST['m_mensaje'],1) ;
			$conectar->query("insert into eforo_mensajes (id_foro,id_tema,fecha,id_usuario,mensaje,o_caretos,o_codigo,o_firma,o_importante) values ('{$_GET['foro']}','{$_GET['tema']}','$fecha','$c_id','{$_POST['m_mensaje']}','{$_POST['m_caretos']}','{$_POST['m_codigo']}','{$_POST['m_firma']}','{$_POST['m_importante']}')") ;
			$id_ultimo = mysql_insert_id() ;
			$conectar->query("update eforo_foros set num_mensajes=num_mensajes+1 where id='{$_GET['foro']}'") ;
			$conectar->query("update eforo_mensajes set num_respuestas=num_respuestas+1,fecha_ultimo='$fecha' where id='{$_GET['tema']}'") ;
			if($c_id) $conectar->query("update $tabla_usuarios set mensajes=mensajes+1 where id='$c_id'") ;
			# Obtener el n�mero de la �ltima p�gina
			$con = $conectar->query("select count(id) from eforo_mensajes where id_tema='{$_GET['tema']}'") ;
			$ult_pagina = ceil(mysqli_result($con,0,0) / $conf['max_mensajes']) ;
			mysqli_free_result($con) ;
			aviso('Confirmaci�n',"<p>Tu mensaje ha sido publicado.<p><a href=\"$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]pag$u[4]$ult_pagina$u[5]#$id_ultimo\" class=\"eforo_enlace\">� Ir al mensaje</a>\n<p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[5]\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
			break ;
		# --> Editar el mensaje
		case 3 :
			# Revisamos si el mensaje a editar se trata de un tema o de una respuesta
			$con = $conectar->query("select count(id) from eforo_mensajes where id=id_tema and id='{$_GET['mensaje']}'") ;
			if(mysqli_result($con,0,0)) {
				$_POST['m_tema'] = quitar($_POST['m_tema'],1) ;
			}
			else {
				$_POST['m_tema'] = quitar($_POST['m_tema']) ;
			}
			$_POST['m_mensaje'] = quitar($_POST['m_mensaje'],1) ;
			$id_ultimo = $_GET['mensaje'] ;
			$conectar->query("update eforo_mensajes set tema='{$_POST['m_tema']}',mensaje='{$_POST['m_mensaje']}',o_caretos='{$_POST['m_caretos']}',o_codigo='{$_POST['m_codigo']}',o_firma='{$_POST['m_firma']}',o_importante='{$_POST['m_importante']}',o_notificacion='{$_POST['m_notificacion']}',fecha_editado='$fecha' where id='{$_GET['mensaje']}'") ;
			aviso('Confirmaci�n',"Tu mensaje ha sido editado.<p><a href=\"$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[3]pag$u[4]{$_GET['pag']}$u[5]#{$_GET['mensaje']}\" class=\"eforo_enlace\">� Ir al mensaje</a><p><a href=\"$u[0]forotemas$u[1]$u[2]foro$u[4]{$_GET['foro']}\" class=\"eforo_enlace\">� Regresar al foro</a>") ;
	}
	if($_POST['m_notificacion']) $conectar->query("update eforo_mensajes set o_notificacion_email='1' where id='$id_ultimo'") ;
	if(!empty($_FILES['m_archivo'])) $conectar->query("update eforo_adjuntos set id_mensaje='$id_ultimo' where id='$id_adjunto'") ;
	# * Notificaci�n por email
	if($conf['notificacion_email'] && $que == 2) {
		$con = $conectar->query("select id_usuario,tema,o_notificacion_email from eforo_mensajes where id='{$_GET['tema']}'") ;
		$datos = mysqli_fetch_assoc($con) ;
		if($datos['o_notificacion_email'] && $datos['id_usuario'] != 0 && $datos['id_usuario'] != $c_id) {
			$con2 = $conectar->query("select nick,email from $tabla_usuarios where id='{$datos['id_usuario']}'") ;
			if(mysqli_num_rows($con2)) {
				$datos2 = mysqli_fetch_row($con2) ;
				$mensaje =
"<style>
body { font-family: verdana ; font-size: 10pt }
a { color: #000000 ; font-weight: bold ; text-decoration: none }
</style>
<body>
<p>Saludos <b>$datos2[0]</b>
<p>Han respondido a tu mensaje <b>{$datos['tema']}</b>
<p>Puedes visitarlo en la siguiente direcci�n:
<p><a href=\"{$conf['foro_url']}$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[5]#$id_ultimo\" target=\"_blank\">{$conf['foro_url']}$u[0]foromensajes$u[1]$u[2]foro$u[4]{$_GET['foro']}$u[3]tema$u[4]{$_GET['tema']}$u[5]#$id_ultimo</a>
<p>No recibir�s m�s notificaciones hasta que visites tu mensaje. Para desactivar esta opci�n edita
tu mensaje y desactiva la casilla <b>Notificar por email cuando haya respuestas</b>.
</body>
" ;
				if(!@mail($datos2[1],"Saludos $datos2[0] han respondido a tu mensaje",$mensaje,"from: {$conf['admin_email']}\ncontent-type: text/html")) {
					aviso('Error','No se pudo enviar la notificaci�n. El servidor est� mal configurado o no soporta env�os de email a trav�s de SMTP.') ;
				}
				# --> Se desactivan la notificaciones hasta que el usuario revise su mensaje
				$conectar->query("update eforo_mensajes set o_notificacion_email='0' where id='{$_GET['tema']}'") ;
			}
			mysqli_free_result($con2) ;
		}
		mysqli_free_result($con) ;
	}
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>