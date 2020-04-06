<?php
/*
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

require '../foroconfig.php' ;
require '../eforo_funciones/sesion.php' ;
require '../eforo_funciones/aviso.php' ;
$ePiel->cargar(array(
'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de administraci�n � Foros',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
if(isset($_POST['agregar'])) {
	switch($_POST['agregar']) {
		case 1 :
			# * Agregar categor�a
			$conectar->query("insert into eforo_categorias (categoria) values ('{$_POST['categoria']}')") ;
			aviso('Categor�a agregada','La categor�a <b>'.$_POST['categoria'].'</b> ha sido agregada.','','../') ;
			break ;
		case 2 :
			# * Agregar foro
			$conectar->query("insert into eforo_foros (id_categoria,foro,descripcion) values ('{$_POST['categoria']}','{$_POST['foro']}','{$_POST['descripcion']}')") ;
			aviso('Foro agregado','El foro <b>'.$_POST['foro'].'</b> ha sido agregado.','','../') ;
	}
}
if(!empty($_GET['ordenar'])) {
	switch($_GET['ordenar']) {
		case 1 :
			# * Cambiar categor�a de orden 
			!$_GET['cambiar'] ? $conectar->query("update eforo_categorias set orden=orden+15 where id='{$_GET['id']}'") : $conectar->query("update eforo_categorias set orden=orden-15 where id='{$_GET['id']}'") ;
			$con = $conectar->query("select id from eforo_categorias order by orden asc") ;
			for($a = 10 ; $datos = mysqli_fetch_row($con) ; $a += 10) {
				$conectar->query("update eforo_categorias set orden='$a' where id='$datos[0]'") ;
			}
			mysqli_free_result($con) ;
			aviso('Categor�a movida','La categor�a ha sido cambiada de orden.','','../') ;
			break ;
		case 2 :
			# * Cambiar foro de orden 
			!$_GET['cambiar'] ? $conectar->query("update eforo_foros set orden=orden+15 where id='{$_GET['id']}'") : $conectar->query("update eforo_foros set orden=orden-15 where id='{$_GET['id']}'") ;
			$con = $conectar->query("select id from eforo_foros where id_categoria='{$_GET['c']}' order by orden asc") ;
			for($a = 10 ; $datos = mysqli_fetch_row($con) ; $a += 10) {
				$conectar->query("update eforo_foros set orden='$a' where id='$datos[0]'") ;
			}
			mysqli_free_result($con) ;
			aviso('Foro cambiado','El foro ha sido cambiado de orden.','','../') ;
	}
}
if(!empty($_GET['borrar'])) {
	switch($_GET['borrar']) {
		case 1 :
			# * Borrar categor�a
			$con = $conectar->query("select count(id) from eforo_foros where id_categoria='{$_GET['id']}'") ;
			if(!mysqli_result($con,0,0)) {
				$conectar->query("delete from eforo_categorias where id='{$_GET['id']}'") ;
				aviso('Categor�a borrada','La categor�a ha sido borrada.','','../') ;
			}
			else {
				aviso('Error','Debes eliminar todos los foros de esta categor�a.','','../') ;
			}
			break ;
		case 2 :
			# * Borrar foro
			$conectar->query("delete from eforo_mensajes where id_foro='{$_GET['id']}'") ;
			$conectar->query("delete from eforo_foros where id='{$_GET['id']}'") ;
			aviso('Foro borrado','El foro y todos sus mensajes han sido borrados.','','../') ;
	}
}
# * Editar t�tulo y descripci�n de categor�as y foros
if(isset($_POST['editar'])) {
	foreach($_POST as $nombre => $valor) {
		switch(true) {
			case preg_match('^cat_',$nombre) :
				list(,$id) = explode('_',$nombre) ;
				$conectar->query("update eforo_categorias set categoria='$valor' where id='$id'") ;
				break ;
			case preg_match('^foro_',$nombre) :
				list(,$id) = explode('_',$nombre) ;
				$conectar->query("update eforo_foros set foro='$valor' where id='$id'") ;
				break ;
			case preg_match('^des_',$nombre) :
				list(,$id) = explode('_',$nombre) ;
				$conectar->query("update eforo_foros set descripcion='$valor' where id='$id'") ;
		}
	}
}
if(!empty($_GET['mover'])) {
	list($id_categoria,$id_foro) = explode('_',$_GET['mover']) ;
	$conectar->query("update eforo_foros set id_categoria='$id_categoria' where id='$id_foro'") ;
	aviso('Foro movido','El foro ha sido movido a la categor�a seleccionada.','','../') ;
}
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal">
<tr>
<td colspan="2" class="eforo_tabla_titulo"><div class="eforo_titulo_1">Agregar</div></td>
</tr>
<tr>
<td width="50%" class="eforo_tabla_subtitulo"><div class="eforo_titulo_2">Categor�a</div></td>
<td width="50%" class="eforo_tabla_subtitulo"><div class="eforo_titulo_2">Foro</div></td>
</tr>
<tr>
<td valign="top" class="eforo_tabla_defecto">
<form method="post" action="foros.php" style="display: inline">
<input type="hidden" name="agregar" value="1" />
<b>Categor�a:</b><br />
<input type="text" name="categoria" maxlength="100" size="30" class="eforo_formulario" /><br /><br />
<input type="submit" value="Agregar Categor�a" class="eforo_formulario" />
</form>
</td>
<td valign="top" class="eforo_tabla_defecto">
<form method="post" action="foros.php" style="display: inline">
<input type="hidden" name="agregar" value="2" />
<b>Foro:</b><br />
<input type="text" name="foro" maxlength="100" size="30" class="eforo_formulario" /><br />
<b>Categor�a:</b><br />
<select name="categoria" class="eforo_formulario">
<?
$con = $conectar->query("select id,categoria from eforo_categorias order by orden asc") ;
while($datos = mysqli_fetch_row($con)) {
	echo "<option value=\"$datos[0]\">$datos[1]</option>\n" ;
}
mysqli_free_result($con) ;
?>
</select><br />
<b>Descripci�n:</b><br />
<textarea name="descripcion" cols="30" rows="5" class="eforo_formulario"></textarea><br /><br />
<input type="submit" value="Agregar Foro" class="eforo_formulario" />
</form>
</td>
</tr>
</table><br />
<form method="post" action="foros.php">
<input type="hidden" name="editar" value="1">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal">
<tr>
<td width="20%" class="eforo_tabla_titulo"><div class="eforo_titulo_1" align="center">Orden</div></td>
<td width="65%" class="eforo_tabla_titulo"><div class="eforo_titulo_1" align="center">Categor�a/Subforo</div></td>
<td width="15%" class="eforo_tabla_titulo">&nbsp;</td>
</tr>
<tr>
<td colspan="3" class="eforo_tabla_defecto"><center><input type="submit" value="Guardar Modificaciones" class="eforo_formulario"></center></td>
</tr>
<?
# * Almacena el id y el nombre de las categorias en un array
$con = $conectar->query('select id,categoria from eforo_categorias order by orden asc') ;
while($datos = mysqli_fetch_row($con)) {
	$categorias[$datos[0]] = $datos[1] ;
}
mysqli_free_result($con) ;
foreach($categorias as $categoria_id => $categoria_nom) {
?>
<tr>
</td>
<td class="eforo_tabla_titulo">
<center>
<input type="button" value="Bajar" onclick="location='foros.php?id=<?=$categoria_id?>&ordenar=1&cambiar=0'" class="eforo_formulario">
<input type="button" value="Subir" onclick="location='foros.php?id=<?=$categoria_id?>&ordenar=1&cambiar=1'" class="eforo_formulario">
</center>
</td>
<td class="eforo_tabla_titulo"><input type="text" name="cat_<?=$categoria_id?>" value="<?=$categoria_nom?>" size="30" maxlength="100" class="eforo_formulario"></td>
<td class="eforo_tabla_titulo"><center><input type="button" value="Borrar" onclick="if(confirm('�Deseas borrar la categor�a?')) location = 'foros.php?id=<?=$categoria_id?>&borrar=1'" class="eforo_formulario"></center></td>
</tr>
<?
	$con = $conectar->query("select * from eforo_foros where id_categoria='$categoria_id' order by orden asc") ;
	while($datos = mysqli_fetch_assoc($con)) {
?>
<tr>
<td class="eforo_tabla_defecto">
<center>
<input type="button" value="Bajar" onclick="location='foros.php?id=<?=$datos['id']?>&c=<?=$categoria_id?>&ordenar=2&cambiar=0'" class="eforo_formulario">
<input type="button" value="Subir" onclick="location='foros.php?id=<?=$datos['id']?>&c=<?=$categoria_id?>&ordenar=2&cambiar=1'" class="eforo_formulario">
<br><br>
<select onchange="if(value) location = 'foros.php?mover='+options[selectedIndex].value" class="eforo_formulario">
<option>Mover a ...</option>
<?
		foreach($categorias as $a => $b) {
			echo "<option value=\"{$a}_{$datos['id']}\">$b</option>\n" ;
		}
?>
</select>
</center>
</td>
<td class="eforo_tabla_defecto">
<input type="text" name="foro_<?=$datos['id']?>" size="30" maxlength="100" value="<?=$datos['foro']?>" class="eforo_formulario">
<br><br>
<textarea name="des_<?=$datos['id']?>" cols="30" rows="3" class="eforo_formulario"><?=$datos['descripcion']?></textarea>
</td>
<td class="eforo_tabla_defecto"><center><input type="button" value="Borrar" onclick="if(confirm('�Deseas borrar el foro y todos sus mensajes?')) location='foros.php?id=<?=$datos['id']?>&borrar=2'" class="eforo_formulario"></center></td>
</tr>
<?
	}
	mysqli_free_result($con) ;
}
?>
<tr>
<td colspan="3" class="eforo_tabla_defecto"><center><input type="submit" value="Guardar Modificaciones" class="eforo_formulario"></center></td>
</tr>
</table>
</form>
<?
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>
