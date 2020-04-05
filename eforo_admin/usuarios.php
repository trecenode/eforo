<?php
/*
*************************************************
*** eForo v3.0
*** Creado por: Electros <electros@electros.net>
*** Sitio web: www.13node.com
*** Licencia: GNU General Public License
*************************************************

--- P�gina: eforo_admin/usuarios.php ---

eForo - Una comunidad para tus visitantes
Copyright � 2003-2004 Daniel Osorio "Electros"

Este programa es software libre, puedes redistribuirlo y/o modificarlo bajo los t�rminos
de la GNU General Public License publicados por la Free Software Foundation; desde la
versi�n 2 de la licencia, o (si lo deseas) cualquiera m�s reciente.
*/

require '../foroconfig.php' ;
require '../eforo_funciones/sesion.php' ;
require '../eforo_funciones/aviso.php' ;
require '../eforo_funciones/epaginas.php' ;
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
# * Designar moderador
if(isset($_POST['designar'])) {
	$conectar->query("delete from eforo_moderadores where id_usuario='{$_POST['id_moderador']}'") ;
	foreach($_POST as $nombre => $valor) {
		if(preg_match('^foro_',$nombre)) $conectar->query("insert into eforo_moderadores (id_foro,id_usuario) values('$valor','{$_POST['id_moderador']}')") ;
	}
	$conectar->query("update $tabla_usuarios set rango='500',rango_fijo='1' where id='{$_POST['id_moderador']}'") ;
	aviso('Moderador designado','El usuario <b>'.usuario($_POST['id_moderador']).'</b> ha sido designado moderador.','','../') ;
}
# * Quitar moderador
if(!empty($_GET['quitar'])) {
	$conectar->query("delete from eforo_moderadores where id_usuario='{$_GET['quitar']}'") ;
	$conectar->query("update $tabla_usuarios set rango='1',rango_fijo='0' where id='{$_GET['quitar']}'") ;
	aviso('Moderador quitado','Se han quitado los privilegios de moderaci�n a <b>'.usuario($_GET['quitar']).'</b>.','','../') ;
}
# * Asignar rangos
if(isset($_POST['rango'])) {
	if($_POST['rango'] != 'defecto') {
		foreach($_POST as $a => $b) {
			if(preg_match('^id_',$a)) $conectar->query("update $tabla_usuarios set rango='{$_POST['rango']}',rango_fijo='1' where id='$b'") ;
		}
	}
	else {
		foreach($_POST as $a => $b) {
			if(preg_match('^id_',$a)) $conectar->query("update $tabla_usuarios set rango='1',rango_fijo='0' where id='$b'") ;
		}
	}
	aviso('Rango asignado','El rango ha sido asignado.','','../') ;
}
# * Borrar usuario
if(!empty($_GET['borrar'])) {
	$conectar->query("delete from eforo_enlinea where id_usuario='{$_GET['borrar']}'") ;
	$conectar->query("delete from eforo_recientes where id_usuario='{$_GET['borrar']}'") ;
	$conectar->query("delete from eforo_moderadores where id_usuario='{$_GET['borrar']}'") ;
	$conectar->query("delete from eforo_privados where id_destinario='{$_GET['borrar']}'") ;
	$con = $conectar->query("select id from eforo_mensajes where id=id_tema and id_usuario='{$_GET['borrar']}'") ;
	while($datos = mysqli_fetch_row($con)) {
		$conectar->query("delete from eforo_mensajes where id_tema='$datos[0]'") ;
	}
	mysqli_free_result($con) ;
	$conectar->query("delete from eforo_mensajes where id_usuario='{$_GET['borrar']}'") ;
	aviso('Usuario borrado','El usuario <b>'.usuario($_GET['borrar']).'</b> ha sido borrado.','','../') ;
	$conectar->query("delete from $tabla_usuarios where id='{$_GET['borrar']}'") ;
}
# * Se almacenan todos los rangos en un array
$con = $conectar->query('select * from eforo_rangos order by rango asc') ;
while($datos = mysqli_fetch_assoc($con)) {
	$rangos[$datos['rango']] = array($datos['minimo'],$datos['descripcion']) ;
}
mysqli_free_result($con) ;
# --> N�mero de columnas
$columnas = 3 ;
if(!empty($_GET['letra'])) {
	switch(true) {
		case $_GET['letra'] == 'num' :
			$letra = " where nick regexp '^[0-9]+'" ;
			break ;
		case preg_match('^[a-z]{1}$',$_GET['letra']) :
			$letra = " where nick like '{$_GET['letra']}%'" ;
	}
}
else {
	$_GET['letra'] = '' ;
	$letra = '' ;
}
$por = array('id','nick') ;
$b = !empty($_GET['por']) && preg_match('^[0-9]+$',$_GET['por']) ? $_GET['por'] - 1 : 0 ;
$orden = array('desc','asc') ;
$c = !empty($_GET['orden']) && preg_match('^[0-9]+$',$_GET['orden']) ? $_GET['orden'] - 1 : 0 ;
$ePaginas = new ePaginas("select * from $tabla_usuarios$letra order by $por[$b] $orden[$c]",90) ;
$ePaginas->u = array($u[2],$u[3],$u[4],$u[5]) ;
$ePaginas->e = array('<a href="','" class="eforo_enlace">','</a>') ;
$con = $ePaginas->consultar() ;
?>
<style type="text/css">
.nota {
border: #000000 1px solid ;
background-color: #fff ;
position: absolute ;
width: 275px ;
padding: 3px ;
display: none
}
</style>
<script type="text/javascript">
b = '' ;
document.onmousemove = mover_nota ;
function mostrar_nota(a) {
	b = document.getElementById(a) ;
	b.style.display = 'block' ;
}
function mover_nota(e) {
	if (b) {
		b.style.left = ((document.all ? window.event.x + document.body.scrollLeft : e.pageX) + 20)+"px" ;
		b.style.top	= ((document.all ? window.event.y + document.body.scrollTop  : e.pageY) + 20)+"px" ;
	}
}
function ocultar_nota() {
	b.style.display = 'none' ;
}
</script>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal">
<tr>
<td colspan="12" class="eforo_tabla_titulo"><div class="eforo_titulo_1">Usuarios</div></td>
</tr>
<?
if(empty($_GET['moderador'])) {
?>
<tr>
<td colspan="12" class="eforo_tabla_defecto">
<form method="get" action="usuarios.php" style="display: inline">
<b>Ver usuarios que empiecen por:</b>
<select name="letra" class="eforo_formulario">
<option value="">Cualquier caract�r</option>
<option value="num"<? if($_GET['letra'] == 'num') echo ' selected="selected"' ?>>N�mero</option>
<option value="a"<? if($_GET['letra'] == 'a') echo ' selected="selected"' ?>>A</option>
<option value="b"<? if($_GET['letra'] == 'b') echo ' selected="selected"' ?>>B</option>
<option value="c"<? if($_GET['letra'] == 'c') echo ' selected="selected"' ?>>C</option>
<option value="d"<? if($_GET['letra'] == 'd') echo ' selected="selected"' ?>>D</option>
<option value="e"<? if($_GET['letra'] == 'e') echo ' selected="selected"' ?>>E</option>
<option value="f"<? if($_GET['letra'] == 'f') echo ' selected="selected"' ?>>F</option>
<option value="g"<? if($_GET['letra'] == 'g') echo ' selected="selected"' ?>>G</option>
<option value="h"<? if($_GET['letra'] == 'h') echo ' selected="selected"' ?>>H</option>
<option value="i"<? if($_GET['letra'] == 'i') echo ' selected="selected"' ?>>I</option>
<option value="j"<? if($_GET['letra'] == 'j') echo ' selected="selected"' ?>>J</option>
<option value="k"<? if($_GET['letra'] == 'k') echo ' selected="selected"' ?>>K</option>
<option value="l"<? if($_GET['letra'] == 'l') echo ' selected="selected"' ?>>L</option>
<option value="m"<? if($_GET['letra'] == 'm') echo ' selected="selected"' ?>>M</option>
<option value="n"<? if($_GET['letra'] == 'n') echo ' selected="selected"' ?>>N</option>
<option value="o"<? if($_GET['letra'] == 'o') echo ' selected="selected"' ?>>O</option>
<option value="p"<? if($_GET['letra'] == 'p') echo ' selected="selected"' ?>>P</option>
<option value="q"<? if($_GET['letra'] == 'q') echo ' selected="selected"' ?>>Q</option>
<option value="r"<? if($_GET['letra'] == 'r') echo ' selected="selected"' ?>>R</option>
<option value="s"<? if($_GET['letra'] == 's') echo ' selected="selected"' ?>>S</option>
<option value="t"<? if($_GET['letra'] == 't') echo ' selected="selected"' ?>>T</option>
<option value="u"<? if($_GET['letra'] == 'u') echo ' selected="selected"' ?>>U</option>
<option value="v"<? if($_GET['letra'] == 'v') echo ' selected="selected"' ?>>V</option>
<option value="w"<? if($_GET['letra'] == 'w') echo ' selected="selected"' ?>>W</option>
<option value="x"<? if($_GET['letra'] == 'x') echo ' selected="selected"' ?>>X</option>
<option value="y"<? if($_GET['letra'] == 'y') echo ' selected="selected"' ?>>Y</option>
<option value="z"<? if($_GET['letra'] == 'z') echo ' selected="selected"' ?>>Z</option>
</select>
<b>Por:</b>
<select name="por" class="eforo_formulario">
<option value="1">M�s recientes</option>
<option value="2"<? if(!empty($_GET['por']) && $_GET['por'] == 2) echo ' selected="selected"' ?>>Orden alfab�tico</option>
</select>
<b>En orden:</b>
<select name="orden" class="eforo_formulario">
<option value="1">Descendente</option>
<option value="2"<? if(!empty($_GET['orden']) && $_GET['orden'] == 2) echo ' selected="selected"' ?>>Ascendente</option>
</select>
<input type="submit" value="Ver" />
</form>
<br /><br />
<b>Asignar rangos:</b>
<form method="post" action="usuarios.php?<?=$_SERVER['QUERY_STRING']?>">
<select name="rango" onchange="if(value) submit()" class="eforo_formulario">
<option value="">...</option>
<?
foreach($rangos as $a => $b) {
	echo "<option value=\"$a\">$a $b[1]</option>\n" ;
}
?>
<option value="defecto">Asignar por defecto</option>
</select>
</td>
</tr>
<tr>
<td colspan="12" class="eforo_tabla_defecto"><?=$ePaginas->paginar()?></td>
<?
	$estilo_num = 1 ;
	for($i = 0 ; $datos = mysqli_fetch_assoc($con) ; $i++) {
		if($i % $columnas == 0) {
?>
</tr>
<tr>
<?
		}
	if($datos['rango_fijo']) {
		$usuario_rango = $rangos[$datos['rango']][1] ;
	}
	else {
		$usuario_rango = $rangos[1][1] ;
		foreach($rangos as $rango) {
			if($rango[0] != 0 && $datos['mensajes'] >= $rango[0]) $usuario_rango = $rango[1] ;
		}
	}
?>
<td class="eforo_tabla_mensaje_<?=$estilo_num?>"><input type="checkbox" name="id_<?=$datos['id']?>" value="<?=$datos['id']?>" /></td>
<td class="eforo_tabla_mensaje_<?=$estilo_num?>"><a href="../<?="$u[0]forousuarios$u[1]$u[2]u$u[4]{$datos['id']}$u[5]"?>" target="_blank" onmouseover="mostrar_nota('nota_<?=$datos['id']?>')" onmouseout="ocultar_nota('nota_<?=$datos['id']?>')" class="eforo_enlace"><?=$datos['nick'] ? $datos['nick'] : '&nbsp;'?></a>
<div id="nota_<?=$datos['id']?>" class="nota">
ID: <?=$datos['id']?><br />
Rango: <?=$usuario_rango?><br />
Email: <?=$datos['email']?><br />
IP: <?=$datos['ip']?>
</div>
</td>
<td class="eforo_tabla_mensaje_<?=$estilo_num?>"><input type="button" value=" M " onclick="location='usuarios.php?moderador=<?=$datos['id']?>'" class="eforo_formulario" /></td>
<td class="eforo_tabla_mensaje_<?=$estilo_num?>"><input type="button" value=" B " onclick="if(confirm('�Deseas borrar a este usuario junto con todos sus mensajes?')) location='usuarios.php?borrar=<?=$datos['id']?>'" class="eforo_formulario" /></td>
<?
		$estilo_num = $estilo_num == 1 ? 2 : 1 ;
	}
?>
</tr>
<tr>
<td colspan="12" class="eforo_tabla_defecto"><?=$ePaginas->paginar()?></td>
</tr>
<tr>
<td colspan="12" class="eforo_tabla_titulo"><div class="eforo_titulo_1">Ayuda</div></td>
</tr>
<tr>
<td colspan="12" class="eforo_tabla_defecto">
<script type="text/javascript">
a = 0 ;
function ayuda() {
	if(a == 0) {
		document.getElementById('ayuda_enlace').value = 'Ocultar >>' ;
		document.getElementById('ayuda_texto').style.display = 'block' ;
		a++ ;
	}
	else {
		document.getElementById('ayuda_enlace').value = 'Ver m�s >>' ;
		document.getElementById('ayuda_texto').style.display = 'none' ;
		a-- ;
	}
}
</script>
<input type="button" id="ayuda_enlace" value="Ver m�s >>" onclick="ayuda()" class="eforo_formulario" />
<div id="ayuda_texto" style="display: none">
<p><b>�Como se designan moderadores?</b><br />
Para designar a un moderador haz clic en el bot�n M y luego selecciona los subforos en donde tendr�
privilegios de moderaci�n. Para quitar estos privilegios a un usuario que ya es moderador haz click
en M y despu�s en la opci�n Quitar Moderador.</p>
<p><b>�Como se asignan rangos fijos?</b><br />
Para asignar un rango fijo selecciona las casillas al lado de cada usuario y luego selecciona de la
lista el rango deseado. Este rango no variar� con el n�mero de mensajes (si es un rango normal s�lo
ser� fijo para los usuarios seleccionados). Para que su rango sea normal de nuevo haz click en Asignar por defecto.</p>
</div>
</td>
</tr>
<?
}
else {
# * Funci�n para designar moderadores en el foro
?>
<tr>
<td class="eforo_tabla_subtitulo"><div class="eforo_titulo_2">Designar moderador</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto">
<p><a href="usuarios.php" class="eforo_enlace">� Regresar a Usuarios</a></p>
<?
	$con = $conectar->query("select nick from $tabla_usuarios where id='{$_GET['moderador']}'") ;
	$datos = mysqli_fetch_row($con) ;
	$nick_moderador = $datos[0] ;
	mysqli_free_result($con) ;
?>
<p>Debes seleccionar los subforos en donde desees que <b><?=$nick_moderador?></b> sea moderador.</p>
<form method="post" action="usuarios.php">
<input type="hidden" name="id_moderador" value="<?=$_GET['moderador']?>" />
<?
	$con = $conectar->query('select id,categoria from eforo_categorias order by orden asc') ;
	while($datos = mysqli_fetch_row($con)) {
		echo '<b>'.$datos[1].'</b><br />' ;
		$con2 = $conectar->query("select id,foro from eforo_foros where id_categoria='$datos[0]' order by orden asc") ;
		while($datos2 = mysqli_fetch_row($con2)) {
			$con3 = $conectar->query("select count(id) from eforo_moderadores where id_foro='$datos2[0]' and id_usuario='{$_GET['moderador']}'") ;
			$sel = mysqli_result($con3,0,0) ? ' checked="checked"' : '' ;
			mysqli_free_result($con3) ;
?>
<input type="checkbox" name="foro_<?=$datos2[0]?>" value="<?=$datos2[0]?>"<?=$sel?> /> <?=$datos2[1]?><br />
<?
		}
		mysqli_free_result($con2) ;
	}
	mysqli_free_result($con) ;
?>
<br>
<center>
<input type="submit" name="designar" value="Designar Moderador" class="eforo_formulario" />
<input type="button" value="Quitar Moderador" onclick="if(confirm('�Deseas quitar los privilegios de moderaci�n a <?=$nick_moderador?>?')) location = 'usuarios.php?quitar=<?=$_GET['moderador']?>'" class="eforo_formulario" />
</center>
</td>
</tr>
<?
}
?>
</table>
</form>
<?
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>
