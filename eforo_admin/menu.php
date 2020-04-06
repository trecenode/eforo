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
'titulo' => $conf['foro_titulo'].' � Panel de administraci�n � Men�',
'estilo' => '../'.$conf['estilo']
)) ;
$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
?>
<p><a href="../<?="$u[0]foro$u[1]$u[5]"?>" target="_top" class="eforo_enlace">� Regresar al foro</a></p>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="eforo_tabla_principal">
<tr>
<td class="eforo_tabla_titulo"><div class="eforo_titulo_1">Men�</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="foros.php" target="contenido" class="eforo_enlace">Foros</a></div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="configuracion.php" target="contenido" class="eforo_enlace">Configuraci�n</a></div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="rangos.php" target="contenido" class="eforo_enlace">Rangos</a></div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="permisos.php" target="contenido" class="eforo_enlace">Permisos</a></div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="usuarios.php" target="contenido" class="eforo_enlace">Usuarios</a></div></td>
</tr>
<tr>
<td class="eforo_tabla_titulo"><div class="eforo_titulo_1">Herramientas</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><div style="text-align: center"><a href="sincronizador.php" target="contenido" class="eforo_enlace">Sincronizar</a></div></td>
</tr>
</table>
</body>
</html>
