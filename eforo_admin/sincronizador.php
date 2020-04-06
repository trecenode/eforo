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
$ePiel->cargar(array(
'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de administraci�n � Sincronizar',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
if(!empty($_GET['sincronizar'])) {
	require '../eforo_funciones/aviso.php' ;
	$con = $conectar->query('select id from eforo_foros order by id asc') ;
	while($datos = mysqli_fetch_row($con)) {
		$con2 = $conectar->query("select count(id) from eforo_mensajes where id=id_tema and id_foro='$datos[0]'") ;
		$total_temas = mysqli_result($con2,0,0) ;
		mysqli_free_result($con2) ;
		$con2 = $conectar->query("select count(id) from eforo_mensajes where id_foro='$datos[0]'") ;
		$total_mensajes = mysqli_result($con2,0,0) ;
		mysqli_free_result($con2) ;
		$conectar->query("update eforo_foros set num_temas='$total_temas',num_mensajes='$total_mensajes' where id='$datos[0]'") ;
	}
	mysqli_free_result($con) ;
	aviso('Sincronizaci�n finalizada','Se sincronizaron correctamente los mensajes.','','../') ;
}
else {
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="eforo_tabla_principal">
<tr>
<td class="eforo_tabla_titulo"><div class="eforo_titulo_1">Sincronizador</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto">
<p>Esta sencilla pero �til funci�n permite recontar el n�mero de temas y mensajes en cada subforo, estas estad�sticas pudieran estar
mal por las siguientes razones:</p>
<ul>
<li>Errores en versiones anteriores de eForo</li>
<li>Informaci�n perdida al momento de hacer respaldos</li>
<li>Modificaciones no contabilizadas debido a manipulaci�n directa de la base de datos</li>
</ul>
Puedes aplicar esta funci�n con toda confianza y sin riesgos de corromper tu informaci�n.<br /><br />
<div style="text-align: center"><input type="button" value="Sincronizar" onclick="location = 'sincronizador.php?sincronizar=1'" class="eforo_formulario" /></div>
</td>
</tr>
</table>
<?
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>
