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

$ePiel->mostrar('cabecera') ;
if(!$es_administrador) exit("<script type=\"text/javascript\">top.location='../$u[0]foro$u[1]$u[5]'</script>") ;
// --> Configuración
if(isset($_POST['enviar'])) {
	$conectar->query("update eforo_config set
	id_administrador='{$_POST['c_administrador']}',
	email='{$_POST['c_administrador_email']}',
	foro_url='{$_POST['c_foro_url']}',
	foro_titulo='{$_POST['c_foro_titulo']}',
	temas='{$_POST['c_temas']}',
	mensajes='{$_POST['c_mensajes']}',
	ultimos='{$_POST['c_ultimos']}',
	codigo='{$_POST['c_codigo']}',
	caretos='{$_POST['c_caretos']}',
	firma='{$_POST['c_firma']}',
	censurar='{$_POST['c_censurar']}',
	notificacion='{$_POST['c_notificacion']}',
	estilo='{$_POST['c_estilo']}',
	plantilla='{$_POST['c_plantilla']}',
	privados='{$_POST['c_privados']}',
	avatarlargo='{$_POST['c_avatarlargo']}',
	avatarancho='{$_POST['c_avatarancho']}',
	avatartamano='{$_POST['c_avatartamano']}',
	adjuntotamano='{$_POST['c_adjuntotamano']}',
	adjuntoext='{$_POST['c_adjuntoext']}',
	adjuntonombre='{$_POST['c_adjuntonombre']}'") ;
	aviso('Configuración guardada','La Configuración ha sido guardada. Para regresar haz click <a href="?page=configuracion" class="eforo_enlace">aquí</a>.','','../') ;
}
else {
?>
<form name="configuracion" method="post" action="?page=configuracion">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tabla_principal">
<tr>
<td colspan="2" class="eforo_tabla_titulo"><div class="eforo_titulo_1">Configuración</div></td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_defecto"><div style="text-align: center"><input type="submit" name="enviar" value="Guardar Configuración" class="eforo_formulario"></div></td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">General</div></td>
</tr>
<tr>
<td width="50%" class="eforo_tabla_defecto">
<b>Administrador:</b><br />ID del administrador (ej. 5. Para 2 o más separa por comas ej. 5,12,150).<br />
<script type="text/javascript">
function openModal(url, modalTitle) {
    // Create modal container if it doesn't exist
    if (!document.getElementById('eforo-modal-container')) {
        const modalContainer = document.createElement('div');
        modalContainer.id = 'eforo-modal-container';
        modalContainer.style.display = 'none';
        modalContainer.style.position = 'fixed';
        modalContainer.style.zIndex = '1000';
        modalContainer.style.left = '0';
        modalContainer.style.top = '0';
        modalContainer.style.width = '100%';
        modalContainer.style.height = '100%';
        modalContainer.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modalContainer.style.display = 'flex';
        modalContainer.style.alignItems = 'center';
        modalContainer.style.justifyContent = 'center';
        
        // Create the modal content
        const modalContent = document.createElement('div');
        modalContent.id = 'eforo-modal-content';
        modalContent.style.backgroundColor = '#fff';
        modalContent.style.borderRadius = '8px';
        modalContent.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        modalContent.style.width = '500px';
        modalContent.style.maxWidth = '90%';
        modalContent.style.maxHeight = '90%';
        modalContent.style.overflowY = 'auto';
        modalContent.style.display = 'flex';
        modalContent.style.flexDirection = 'column';
        
        // Create the modal header
        const modalHeader = document.createElement('div');
        modalHeader.id = 'eforo-modal-header';
        modalHeader.style.padding = '1rem';
        modalHeader.style.borderBottom = '1px solid #e9ecef';
        modalHeader.style.display = 'flex';
        modalHeader.style.justifyContent = 'space-between';
        modalHeader.style.alignItems = 'center';
        
        // Create the title element
        const modalTitle = document.createElement('h3');
        modalTitle.id = 'eforo-modal-title';
        modalTitle.style.margin = '0';
        modalTitle.style.fontSize = '1.25rem';
        
        // Create the close button
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.style.border = 'none';
        closeButton.style.background = 'none';
        closeButton.style.fontSize = '1.5rem';
        closeButton.style.fontWeight = 'bold';
        closeButton.style.cursor = 'pointer';
        closeButton.onclick = closeModal;
        
        // Create the modal body
        const modalBody = document.createElement('div');
        modalBody.id = 'eforo-modal-body';
        modalBody.style.padding = '1rem';
        modalBody.style.flex = '1';
        
        // Create the iframe for content
        const iframe = document.createElement('iframe');
        iframe.id = 'eforo-modal-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '300px';
        iframe.style.border = 'none';
        
        // Assemble the modal
        modalHeader.appendChild(modalTitle);
        modalHeader.appendChild(closeButton);
        modalBody.appendChild(iframe);
        modalContent.appendChild(modalHeader);
        modalContent.appendChild(modalBody);
        modalContainer.appendChild(modalContent);
        document.body.appendChild(modalContainer);
    }
    
    // Show the modal with content
    const container = document.getElementById('eforo-modal-container');
    const titleElement = document.getElementById('eforo-modal-title');
    const iframe = document.getElementById('eforo-modal-iframe');
    
    titleElement.textContent = modalTitle || 'Modal Window';
    iframe.src = url;
    container.style.display = 'flex';
    
    // Prevent scrolling on the main page
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const container = document.getElementById('eforo-modal-container');
    if (container) {
        container.style.display = 'none';
        document.getElementById('eforo-modal-iframe').src = 'about:blank';
        // Restore scrolling
        document.body.style.overflow = 'auto';
    }
}
</script>
<a href="javascript:openModal('obtener_id.php','Obtener ID de un nick')" class="eforo_enlace">→ Obtener ID de un nick</a>
</td>
<td width="50%" class="eforo_tabla_defecto"><input type="text" name="c_administrador" value="<?php echo implode(',',$conf['admin_id'])?>" maxlength="20" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Email:</b><br>Este email se usará como firma en algunas funciones del foro.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_administrador_email" value="<?php echo $conf['admin_email']?>" maxlength="100" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>URL del foro:</b><br>Dirección web del foro (ej. https://www.pagina.com/, https://www.pagina.com/carpeta/).</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_foro_url" value="<?php echo $conf['foro_url']?>" maxlength="100" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Título:</b><br>Título del foro.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_foro_titulo" value="<?php echo $conf['foro_titulo']?>" maxlength="100" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Temas:</b><br>Numero de temas a mostrar.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_temas" value="<?php echo $conf['max_temas']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Mensajes:</b><br>Mensajes a mostrar por tema.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_mensajes" value="<?php echo $conf['max_mensajes']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Ultimos mensajes:</b><br>Al querer responder un tema se verán los últimos mensajes.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_ultimos" value="<?php echo $conf['max_ultimos']?>" maxlength="3"class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Permitir código especial:</b><br>El código especial sirve para personalizar los mensajes sin necesidad de usar HTML.</td>
<td class="eforo_tabla_defecto">
<input type="radio" name="c_codigo" value="0"<?php if(!$conf['permitir_codigo']) echo ' checked' ; ?>>No
<input type="radio" name="c_codigo" value="1"<?php if($conf['permitir_codigo']) echo ' checked' ; ?>>Sí
</td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Permitir caretos:</b><br>Permitir uso de caretos en los mensajes.</td>
<td class="eforo_tabla_defecto">
<input type="radio" name="c_caretos" value="0"<?php if(!$conf['permitir_caretos']) echo ' checked' ; ?>>No
<input type="radio" name="c_caretos" value="1"<?php if($conf['permitir_caretos']) echo ' checked' ; ?>>Sí
</td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Permitir firma en los mensajes:</b><br>Permite que se muestre una firma al final de cada mensaje.</td>
<td class="eforo_tabla_defecto">
<input type="radio" name="c_firma" value="0"<?php if(!$conf['permitir_firma']) echo ' checked' ; ?>>No
<input type="radio" name="c_firma" value="1"<?php if($conf['permitir_firma']) echo ' checked' ; ?>>Sí
</td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Censurar palabras:</b><br>Sustituye las palabras censuradas por otras palabras. Puedes definir éstas modificando el archivo <b>eforo_funciones/codigo.php</b>.</td>
<td class="eforo_tabla_defecto">
<input type="radio" name="c_censurar" value="0"<?php if(!$conf['censurar_palabras']) echo ' checked' ; ?>>No
<input type="radio" name="c_censurar" value="1"<?php if($conf['censurar_palabras']) echo ' checked' ; ?>>Sí
</td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Notificación por email:</b><br>Permite que un usuario pueda pedir notificación por email cuando
haya respuestas a su tema.</td>
<td class="eforo_tabla_defecto">
<input type="radio" name="c_notificacion" value="0"<?php if(!$conf['notificacion_email']) echo ' checked' ; ?>>No
<input type="radio" name="c_notificacion" value="1"<?php if($conf['notificacion_email']) echo ' checked' ; ?>>Sí
</td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Estilo del foro:</b><br>Selecciona el estilo del foro (tipo de letra, colores, formularios).</td>
<td class="eforo_tabla_defecto">
<input type="hidden" name="c_plantilla" value="<?php echo $conf['plantilla']; ?>" />
<select name="c_estilo" class="eforo_formulario">
<?php
$con = $conectar->query("select plantilla,estilo from eforo_config limit 1") ;
$datos = mysqli_fetch_row($con) ;
$templates_dir = '../eforo_plantillas/';
$all_styles = array();

$templates = array_diff(scandir($templates_dir), array('.', '..', 'index.html'));
foreach($templates as $template) {
    if(is_dir($templates_dir . $template)) {
        $styles_dir = $templates_dir . $template . '/estilos/';
        if(is_dir($styles_dir)) {
            $styles = array_diff(scandir($styles_dir), array('.', '..', 'index.html'));
            foreach($styles as $style) {
                if(preg_match('/\.css$/i', $style)) {
                    $style_name = preg_replace('/\.css$/i', '', $style);
                    $all_styles[$style_name] = $template;
                }
            }
        }
    }
}

ksort($all_styles);

foreach($all_styles as $style => $template) {
    if($style == $datos[1]) {
        echo '<option value="'.$style.'" data-template="'.$template.'" selected>'.$style.' ('.$template.')</option>';
    } else {
        echo '<option value="'.$style.'" data-template="'.$template.'">'.$style.' ('.$template.')</option>';
    }
}

mysqli_free_result($con) ;
?>
</select>
</td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Mensajes privados</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>máximo de mensajes privados:</b><br>Es el número máximo de mensajes privados que cada usuario podrá recibir.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_privados" value="<?php echo $conf['max_privados']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Avatares</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Tamaño de largo:</b><br>El largo en pixeles de la imagen.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_avatarlargo" value="<?php echo $conf['avatar_largo']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Tamaño de ancho:</b><br>El ancho en pixeles de la imagen.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_avatarancho" value="<?php echo $conf['avatar_ancho']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Tamaño del archivo</b><br>Tamaño del archivo en KB.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_avatartamano" value="<?php echo $conf['avatar_tamano']?>" maxlength="3" class="eforo_formulario"></td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_subtitulo"><div class="eforo_titulo_1">Adjuntos</div></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Tamaño del archivo:</b><br>Tamaño del archivo adjunto en KB. El valor máximo permitido por
el servidor es de <b><?php echo @ini_get('upload_max_filesize') ? str_replace('M','',ini_get('upload_max_filesize')) * 1024 : 'un valor desconocido, aunque por lo general es de 2048'?> KB</b>. Este Sólo podrá ser modificado
desde el archivo de Configuración de PHP php.ini.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_adjuntotamano" value="<?php echo $conf['adjunto_tamano']?>" maxlength="5" class="eforo_formulario"></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Extensiones:</b><br>Extensiones permitidas (escríbelas en minúsculas y separadas por saltos de línea).</td>
<td class="eforo_tabla_defecto"><textarea name="c_adjuntoext" cols="25" rows="5" class="eforo_formulario"><?php echo $conf['adjunto_ext']?></textarea></td>
</tr>
<tr>
<td class="eforo_tabla_defecto"><b>Longitud del nombre:</b><br>Longitud máxima de caractéres en el nombre de archivo.</td>
<td class="eforo_tabla_defecto"><input type="text" name="c_adjuntonombre" value="<?php echo $conf['adjunto_nombre']?>" maxlength="2" class="eforo_formulario"></td>
</tr>
<tr>
<td colspan="2" class="eforo_tabla_defecto"><div style="text-align: center"><input type="submit" name="enviar" value="Guardar Configuración" class="eforo_formulario"></div></td>
</tr>
</table>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const styleSelect = document.querySelector('select[name="c_estilo"]');
    const templateInput = document.querySelector('input[name="c_plantilla"]');
    
    styleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        templateInput.value = selectedOption.getAttribute('data-template');
    });
});
</script>
<?php
}
$ePiel->variable('tiempo_carga',round(tiempo_carga() - $tiempo,4)) ;
$ePiel->mostrar('piedepagina') ;
?>
