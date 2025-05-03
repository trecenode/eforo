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

require '../foroconfig.php';

if(!empty($_POST['nick'])) {
	$con = $conectar->query("select id from {$tabla_usuarios} where nick='{$_POST['nick']}'") ;
	if($nick_id = @mysqli_result($con,0,0)) {
		echo '
		<div style="text-align: center; padding: 20px;">
			<p>El ID de <b>'.$_POST['nick'].'</b> es <b>'.$nick_id.'</b></p>
			<button onclick="addIdToForm('.$nick_id.')" style="padding: 8px 16px; background: #3a506b; color: white; border: none; border-radius: 4px; cursor: pointer;">
				Agregar al formulario
			</button>
			<p><a href="obtener_id.php" style="text-decoration: none; color: #3a506b;">← Buscar otro</a></p>
		</div>
		<script>
		function addIdToForm(id) {
			window.parent.document.configuracion.c_administrador.value = 
				window.parent.document.configuracion.c_administrador.value + 
				(window.parent.document.configuracion.c_administrador.value ? "," : "") + 
				id;
			window.parent.closeModal();
		}
		</script>';
	}
	else {
		echo '
		<div style="text-align: center; padding: 20px;">
			<p>Este usuario no existe.</p>
			<p><a href="obtener_id.php" style="text-decoration: none; color: #3a506b;">← Reintentar</a></p>
		</div>';
	}
}
else {
	echo '
	<div style="text-align: center; padding: 20px;">
		<h3 style="margin-bottom: 20px;">Obtener ID de usuario</h3>
		<form method="post" action="obtener_id.php" style="margin-bottom: 20px;">
			<label for="nick" style="display: block; margin-bottom: 10px;"><b>Ingrese el nick del usuario:</b></label>
			<input type="text" name="nick" id="nick" style="padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 15px;" autofocus />
			<br>
			<button type="submit" style="padding: 8px 16px; background: #3a506b; color: white; border: none; border-radius: 4px; cursor: pointer;">Buscar</button>
		</form>
	</div>';
}
?>
</body>
</html>
