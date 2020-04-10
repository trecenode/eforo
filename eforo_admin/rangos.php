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

require '../foroconfig.php' ;
require '../eforo_funciones/sesion.php' ;
require '../eforo_funciones/aviso.php' ;
$ePiel->cargar(array(
'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de administraci�n � Men�',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
switch(true) {
	case isset($_POST['agregar']) :
		$con = $conectar->query("select count(rango) from eforo_rangos where rango='{$_POST['r_rango']}'") ;
		if(mysqli_result($con,0,0)) {
			aviso('Error','Ya existe este rango.','','../') ;
		}
		else {
			$conectar->query("insert into eforo_rangos (rango,minimo,descripcion) values ('{$_POST['r_rango']}','{$_POST['r_minimo']}','{$_POST['r_descripcion']}')") ;
			aviso('Rango agregado','El rango <b>'.$_POST['r_rango'].'</b> ha sido agregado con �xito.','','../') ;
		}
		break ;
	case isset($_POST['modificar']) :
		$_POST['r_minimo'] = !empty($_POST['r_minimo']) ? $_POST['r_minimo'] : 0 ;
		$conectar->query("update eforo_rangos set minimo='{$_POST['r_minimo']}',descripcion='{$_POST['r_descripcion']}' where rango='{$_GET['rango']}'") ;
		aviso('Rango modificado','El rango <b>'.$_GET['rango'].'</b> ha sido modificado con �xito.','','../') ;
		break ;
	case !empty($_GET['borrar']) :
		$conectar->query("delete from eforo_rangos where rango='{$_GET['borrar']}'") ;
		aviso('Rango borrado','El rango <b>'.$_GET['borrar'].'</b> ha sido borrado con �xito.','','../') ;
}
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal">
<tr>
<td colspan="4" class="tabla_titulo"><div class="eforo_titulo_1">Rangos</div></td>
</tr>
<tr>
<td width="15%" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Rango</div></td>
<td width="15%" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">M�nimo</div></td>
<td width="50%" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Descripci�n</div></td>
<td width="20%" class="eforo_tabla_subtitulo">&nbsp;</td>
</tr>
<?
$con = $conectar->query("select * from eforo_rangos order by rango asc") ;
while($datos = mysql_fetch_array($con)) {
	$bloquear = false ;
	if($datos['rango'] == -1 || $datos['rango'] == 0 || $datos['rango'] == 1 || $datos['rango'] == 500 || $datos['rango'] == 999) {
		$bloquear = ' disabled="disabled"' ;
	}
?>
<tr>
<td class="eforo_tabla_defecto"><form method="post" action="rangos.php?rango=<?=$datos['rango']?>"><?=$datos['rango']?></td>
<td class="eforo_tabla_defecto"><input type="text" name="r_minimo" size="5" maxlength="5" value="<?=$datos['minimo']?>" class="eforo_formulario"<?=$bloquear?> /></td>
<td class="eforo_tabla_defecto"><input type="text" name="r_descripcion" size="25" maxlength="100" value="<?=$datos['descripcion']?>" class="eforo_formulario" /> <input type="submit" name="modificar" value="Modificar" class="eforo_formulario" /></td>
<td class="eforo_tabla_defecto"></form><? if(!$bloquear) { ?><div style="text-align: center"><a href="javascript:if(confirm('¿Deseas borrar este rango?')) location = 'rangos.php?borrar=<?=$datos['rango']?>'" class="eforo_enlace">Borrar</a></div><? } else { ?>&nbsp;<? } ?></td>
</tr>
<?
}
mysqli_free_result($con)
?>
<tr>
<td colspan="4" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Agregar nuevo rango</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><form method="post" action="rangos.php"><input type="text" name="r_rango" size="3" maxlength="3" class="eforo_formulario" /></td>
<td class="eforo_tabla_defecto"><input type="text" name="r_minimo" size="5" maxlength="5" class="eforo_formulario" /></td>
<td class="eforo_tabla_defecto"><input type="text" name="r_descripcion" size="25" maxlength="100" class="eforo_formulario" /> <input type="submit" name="agregar" value="Agregar" class="eforo_formulario" /></td>
<td class="eforo_tabla_defecto"></form>&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Ayuda</div></td>
</tr>
<tr>
<td colspan="4" class="eforo_tabla_defecto">
<script type="text/javascript">
a = 0 ;
function ayuda() {
	if(a == 0) {
		document.getElementById('ayuda_enlace').value = 'Ocultar >>' ;
		document.getElementById('ayuda_texto').style.display = 'block' ;
		a++ ;
	}
	else {
		document.getElementById('ayuda_enlace').value = 'Ver más >>' ;
		document.getElementById('ayuda_texto').style.display = 'none' ;
		a-- ;
	}
}
</script>
<input type="button" id="ayuda_enlace" value="Ver más >>" onclick="ayuda()" class="eforo_formulario">
<div id="ayuda_texto" style="display: none">
<br />
<b>� Qu� son los rangos ?</b><br />
Los rangos permiten que tus usuarios puedan adquirir ciertos niveles ya sea al llegar a un determinado
número de mensajes � al ser designados manualmente. Los rangos te permiten crear restricciones en cada
subforo, por ejemplo que Sólo usuarios con nivel 100 puedan tener acceso a un subforo, entre otras más.<br /><br />
<b>� Como se agrega un rango ?</b><br />
Debes escribir un número de rango entre 1 y 999 y un número m�nimo de mensajes para alcanzar este rango. Si deseas crear
rangos fijos debes poner un m�nimo de cero, pero estos Sólo podr�n ser asignados manualmente. Los rangos normales tambi�n
pueden asignarse como rangos fijos y funcionar tambi�n como rangos en base al número de mensajes para los demás usuarios.<br /><br />
<b>Aviso:</b> El nivel m�nimo de mensajes siempre debe estar en forma proporcional al rango definido, por ejemplo lo siguiente
no es v�lido:<br /><br />
<b>10 - 125 - Intermedio<br />
<span style="color: #aa0000">20 - 175 - Avanzado</span><br />
30 - 150 - Experto</b><br /><br />
Si te fijas en este caso al llegar a 175 mensajes, el usuario pasar�a del rango 30 al rango 20 con lo que ser�a un error.<br /><br />
<b>� El rango 500 o 999 hace moderador o administrador a un usuario ?</b><br />
No, el rango 500 y 999 son Sólo descriptivos y se asignan autom�ticamente al usuario que es nombrado moderador o administrador.
Para asignar moderadores y administradores debes hacerlo desde la opci�n <b>Usuarios</b>.<br /><br />
<b>� C�mo se comportan los rangos ?</b><br />
-1 - Usuarios que han sido expulsados - Pueden leer mensajes.<br />
0 - Usuarios no registrados - Pueden leer, escribir nuevos temas y responder mensajes.<br />
1 al 999 - Usuarios registrados - Pueden leer, escribir nuevos temas, responder mensajes, editar y borrar sus propios mensajes.<br />
Este es el permiso máximo que pueden tener los usuarios que tengan esos rangos, pero tambi�n depende del permiso que hayas
elegido por subforo.
</div>
</td>
</tr>
</table>
<?
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>
