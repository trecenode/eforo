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

# * C�digo Especial
# El c�digo especial sirve para formatear un texto, crear enlaces, poner im�genes, sin necesidad de usar HTML,
# estas etiquetas est�n representadas por [etiqueta]texto[/etiqueta] y se sustituyen ya sea haciendo un simple
# str_replace() hasta el uso de funciones m�s avanzadas como preg_match(), preg_replace() y otras donde el texto
# contenido entre las etiquetas es pasado por varias funciones, como la aplicaci�n de la funci�n highlight_string()
# propia de PHP que sirve para colorear c�digo PHP.

# * Sustituye el c�digo especial por su respectivo c�digo HTML
if($conf['permitir_codigo']) {
	# Agrega el prefijo http:// a una URL si es necesario
	function url($a,$b) {
		if(!preg_matchi('^http://',$a)) $a = 'http://'.$a ;
		return '<a href="'.$a.'" target="_blank" class="eforo_enlace">'.$b.'</a>' ;
	}
	function codigo($texto) {
		# --> Colorear c�digo
		# Colorea el texto contenido entre las etiquetas [cod] y [/cod] mediante la funci�n highlight_string() de PHP.
		if(strpos($texto,'[cod]')) {
			# --> Modifica los colores por defecto de PHP (sin tocar el php.ini)
			# No todos los servidores permiten accesar a la funci�n ini_set() 
			@ini_set('highlight.bg','') ; # Fondo
			@ini_set('highlight.comment','#757575') ; # Comentarios
			@ini_set('highlight.default','#0075cc') ; # Texto por defecto
			@ini_set('highlight.html','#aa7500') ; # C�digo HTML
			@ini_set('highlight.keyword','#008000') ; # Caract�res y funciones de PHP
			@ini_set('highlight.string','#0000ff') ; # Cadenas de texto
			$caracteres = array(
			'&lt;'   => '<',
			'&gt;'   => '>',
			'&quot;' => '"',
			'&amp;'  => '&'
			) ;
			preg_match_all('/\[cod\](.+)\[\/cod\]/sU',$texto,$texto_extraido) ;
			for($i = 0 ; $i < count($texto_extraido[0]) ; $i++) {
				$texto_codigo = $texto_extraido[1][$i] ;
				foreach($caracteres as $a => $b) {
					$texto_codigo = str_replace($a,$b,$texto_codigo) ;
				}
				$texto = str_replace($texto_extraido[0][$i],'<div class="eforo_tabla_codigo">'.preg_replace("\r|\n",'',highlight_string(trim($texto_codigo),1)).'</div>',$texto) ;
			}
		}
		# --> Reemplaza etiquetas [etiqueta] por <etiqueta>
		$etiquetas = array(
		'[b]'    => '<b>',
		'[/b]'   => '</b>',
		'[i]'    => '<i>',
		'[/i]'   => '</i>',
		'[u]'    => '<u>',
		'[/u]'   => '</u>'
		) ;
		foreach($etiquetas as $a => $b) {
			$texto = str_replace($a,$b,$texto) ;
		}
		# --> Reemplaza etiquetas usando tambi�n expresiones regulares
		$texto = preg_replace('/\[img\](.+)\[\/img\]/i','<img src="$1" border="0" alt="Imagen obtenida de $1" />',$texto) ;
		$texto = preg_replace('/\[color=(#?[\w]+)\]/','<span style="color: $1">',$texto) ;
		$texto = str_replace('[/color]','</span>',$texto) ;
		$texto = preg_replace('/\[url\](.+)\[\/url\]/i','url(\'$1\',\'$1\')',$texto) ;
		$texto = preg_replace('/\[url=(.+)\](.+)\[\/url\]/i','url(\'$1\',\'$2\')',$texto) ;
		$texto = preg_replace('/\[email\](.+)\[\/email\]/i','<a href="mailto:$1">$1</a>',$texto) ;
		$texto = preg_replace('/\[citar autor=(.+)\]/','<div class="eforo_tabla_codigo">Escrito originalmente por: <b>$1</b><hr class="separador" />',$texto) ;
		$texto = str_replace('[/citar]','</div>',$texto) ;
		return $texto ;
	}
}
# --> Pone caretos en los mensajes
if($conf['permitir_caretos']) {
	function caretos($texto) {
		$caretos = array(
		':D'   => 'alegre.gif',
		':P'   => 'burla.gif',
		':(1'  => 'demonio.gif',
		':?'   => 'duda.gif',
		';)'   => 'guino.gif',
		':lol' => 'lol.gif',
		':|'   => 'neutral.gif',
		':-)'  => 'sonrisa.gif',
		':O'   => 'sorprendido.gif',
		':8'   => 'asustado.gif',
		':S'   => 'confundido.gif',
		':(2'  => 'demonio2.gif',
		':-('  => 'enojado.gif',
		':\'('  => 'llorar.gif',
		':M'   => 'moda.gif',
		':)'   => 'risa.gif',
		':R'   => 'sonrojado.gif',
		':('   => 'triste.gif'
		) ;
		foreach($caretos as $a => $b) {
			$texto = str_replace($a,'<img src="eforo_imagenes/caretos/'.$b.'" border="0" width="15" height="15" align="top" />',$texto) ;
		}
		return $texto ;
	}
}
# --> Censura palabras
if($conf['censurar_palabras']) {
	function censurar($texto) {
		$palabras = array(
		'insulto1' => '*****',
		'insulto2' => '*****',
		'insulto3' => '*****'
		) ;
		foreach($palabras as $a => $b) {
			$texto = str_replace($a,$b,$texto) ;
		}
		return $texto ;
	}
}
?>