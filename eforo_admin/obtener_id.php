<?php
/*
*************************************************
*** eForo v3.1
*** Creado por: Electros en 2006
*** Sitio web: www.13node.com
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
$ePiel->cargar(array(
'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
)) ;
$ePiel->variables(array(
'titulo' => $conf['foro_titulo'].' � Panel de administraci�n � Obtener ID de un nick',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!empty($_POST['nick'])) {
	$con = $conectar->query("select id from usuarios where nick='{$_POST['nick']}'") ;
	if($nick_id = @mysqli_result($con,0,0)) {
		echo '<p>El ID de <b>'.$_POST['nick'].'</b> es <b>'.$nick_id.'</b></p><p><a href="javascript:opener.document.configuracion.c_administrador.value = opener.document.configuracion.c_administrador.value+\','.$nick_id.'\' ; close()" class="eforo_enlace">� Agregar al formulario</a></p>' ;
	}
	else {
		echo '<p>Este usuario no existe.</p>' ;
	}
	echo '<div style="text-align: center"><a href="obtener_id.php" class="eforo_enlace">� Reintentar</a></div>' ;
}
else {
?>
<form method="post" action="obtener_id.php" style="display: inline">
<b>Obtener el ID de este nick:</b> <input type="text" name="nick" size="10" class="eforo_formulario" />
</form>
<?
}
?>
</body>
</html>
