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

$ePiel->cargar(array(
'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' · Panel de administración · Foros',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
// * Establecer los permisos
if(isset($_POST['enviar'])) {
	require '../eforo_funciones/aviso.php' ;
	foreach($_POST as $nombre => $valor) {
		if(preg_match('^p_',$nombre)) {
			list(,$permiso,$id_foro) = explode('_',$nombre) ;
			$conectar->query("update eforo_foros set p_$permiso='$valor' where id='$id_foro'") ;
		}
	}
	aviso('Permisos modificados','Los permisos han sido modificados con éxito.','','../') ;
}
# * Se almacenan todos los rangos en un array
$con = $conectar->query('select rango,descripcion from eforo_rangos order by rango asc') ;
while($datos = mysqli_fetch_row($con)) {
	$rangos[$datos[0]] = $datos[1] ;
}
mysqli_free_result($con) ;
?>
<form method="post" action="permisos.php">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal" align="center">
<tr>
<td colspan="7" class="eforo_tabla_titulo"><div class="eforo_titulo_1">Permisos</div></td>
</tr>
<tr>
<td colspan="7" class="eforo_tabla_defecto"><div style="text-align: center"><input type="submit" name="enviar" value="Modificar los permisos" class="eforo_formulario"></div></td>
</tr>
<tr>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Leer</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Nuevo tema</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Responder</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Editar</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Borrar</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Importante</div></td>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Adjuntar</div></td>
</tr>
<?php
# * Se muestran los foros de cada categoria
$con = $conectar->query("select id,categoria from eforo_categorias order by orden asc") ;
while($datos = mysqli_fetch_row($con)) {
?>
<tr>
<td class="eforo_tabla_subtitulo" colspan="7"><div class="eforo_titulo_1"><?php echo $datos[1]?></div></td>
</tr>
<?php
	$con2 = $conectar->query("select * from eforo_foros where id_categoria='$datos[0]' order by orden asc") ;
	while($datos2 = $con2->fetch_array()) {
		# --> Leer
		$rangos_leer = false ;
		foreach($rangos as $rango => $descripcion) {
			$sel = $rango == $datos2['p_leer'] ? ' selected="selected"' : '' ;
			$rangos_leer .= "<option value=\"$rango\"$sel>$rango\n" ;
		}
		# --> Nuevo
		$rangos_nuevo = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1) {
				$sel = $rango == $datos2['p_nuevo'] ? ' selected="selected"' : '' ;
				$rangos_nuevo .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
		# --> Responder
		$rangos_responder = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1) {
				$sel = $rango == $datos2['p_responder'] ? ' selected="selected"' : '' ;
				$rangos_responder .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
		// --> Editar
		$rangos_editar = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1 && $rango != 0) {
				$sel = $rango == $datos2['p_editar'] ? ' selected="selected"' : '' ;
				$rangos_editar .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
		// --> Borrar
		$rangos_borrar = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1 && $rango != 0) {
				$sel = $rango == $datos2['p_borrar'] ? ' selected="selected"' : '' ;
				$rangos_borrar .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
		// --> Importante
		$rangos_importante = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1 && $rango != 0) {
				$sel = $rango == $datos2['p_importante'] ? ' selected="selected"' : '' ;
				$rangos_importante .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
		// --> Adjuntar
		$rangos_adjuntar = false ;
		foreach($rangos as $rango => $descripcion) {
			if($rango != -1 && $rango != 0) {
				$sel = $rango == $datos2['p_adjuntar'] ? ' selected="selected"' : '' ;
				$rangos_adjuntar .= "<option value=\"$rango\"$sel>$rango\n" ;
			}
		}
?>
<tr>
<td class="eforo_tabla_defecto" colspan="7"><b><?php echo $datos2['foro']?></b></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><select name="p_leer_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_leer?></select></td>
<td class="eforo_tabla_defecto"><select name="p_nuevo_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_nuevo?></select></td>
<td class="eforo_tabla_defecto"><select name="p_responder_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_responder?></select></td>
<td class="eforo_tabla_defecto"><select name="p_editar_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_editar?></select></td>
<td class="eforo_tabla_defecto"><select name="p_borrar_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_borrar?></select></td>
<td class="eforo_tabla_defecto"><select name="p_importante_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_importante?></select></td>
<td class="eforo_tabla_defecto"><select name="p_adjuntar_<?php echo $datos2['id']?>" class="eforo_formulario" style="font-size: 7pt"><?php echo $rangos_adjuntar?></select></td>
</tr>
<?php
	}
	mysqli_free_result($con2) ;
}
mysqli_free_result($con) ;
?>
<tr>
<td colspan="7" class="eforo_tabla_defecto"><div style="text-align: center"><input type="submit" name="enviar" value="Modificar los permisos" class="eforo_formulario"></div></td>
</tr>
</table>
</form>
<?php
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>