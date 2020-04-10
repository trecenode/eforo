<?php
/*
*************************************************
*** ePiel v1.0
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

ePiel - Sistema de plantillas para separar la programaci�n del dise�o
Copyright © 2006 Daniel Osorio "Electros"

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class ePiel {
	# * Cargamos las plantillas deseadas y las almacenamos
	function cargar($plantillas) {
		foreach($plantillas as $plantilla_nom => $plantilla_arc) {
			$this->plantillas[$plantilla_nom] = $plantilla_arc ;
		}
	}
	# * Almacenamos las variables que no están dentro de bloques
	function variable($variable,$valor) {
		$this->variables[$variable] = $valor ;
	}
	function variables($variables) {
		foreach($variables as $variable => $valor) {
			$this->variables[$variable] = $valor ;
		}
	}
	# * Almacenamos las variables en bloques
	function variables_bloque($bloques,$variables = array()) {
		# Si $bloques es el bloque principal se le agregan las variables junto con una nueva iteraci�n
		if(!strpos($bloques,'.')) {
			$this->variables_bloque[$bloques][] = $variables ;
		}
		else {
			# Si existen bloques anidados, se agregan las variables de la siguiente forma:
			# $this->variables_bloque['bloque1'][num_iteraciones1]['bloque2'][num_iteraciones2]...['ultimo_bloque'][] = $variables
			$bloques = explode('.',$bloques) ;
			$total = count($bloques) - 1 ;
			$pre_variable = '$this->variables_bloque' ;
			for($i = 0 ; $i < $total ; $i++) {
				$pre_variable .= '[\''.$bloques[$i].'\']' ;
				# Se obtienen las iteraciones de cada bloque
				eval('$num_iteraciones = count('.$pre_variable.') - 1 ;') ;
				$pre_variable .= '['.$num_iteraciones.']' ;
			}
			eval($pre_variable.'[$bloques[$total]][] = $variables ;') ;
		}
	}
	# * Construimos la variable $this->variables_bloque (para uso exclusivo en mostrar())
	# La variable queda como sigue: $this->variables_bloque['bloque1'][$i_bloque1]['bloque2'][$i_bloque2]...['variable']
	function pre_variable($bloques,$variable = '') {
		# Eliminamos el �ltimo punto agregado
		$bloques = rtrim($bloques,'.') ;
		$bloques = explode('.',$bloques) ;
		$total = count($bloques) - 1 ;
		$pre_variable = '$this->variables_bloque' ;
		for($i = 0 ; $i < $total ; $i++) {
			$pre_variable .= '[\''.$bloques[$i].'\'][$i_'.$bloques[$i].']' ;
		}
		$pre_variable .= '[\''.$bloques[$total].'\']' ;
		if($variable) {
			$pre_variable .= '[$i_'.$bloques[$total].'][\''.$variable.'\']' ;
		}
		return $pre_variable ;
	}
	# * Procesamos la plantilla haciendo las sustituciones necesarias y devolviendo el resultado
	function mostrar($plantilla_nom,$insertar = 0) {
		$resultado = '' ;
		$contenido = trim(file_get_contents($this->plantillas[$plantilla_nom])) ;
		# Reemplazar \ con \\ y ' con \' (para que no provoquen error al aplicar eval())
		$contenido = str_replace('\\','\\\\',$contenido) ;
		$contenido = str_replace('\'','\\\'',$contenido) ;
		# Sustituimos las variables que no están dentro de bloques
		$contenido = preg_replace('/{([\w_]+)}/','\'.(isset($this->variables[\'$1\']) ? $this->variables[\'$1\'] : \'\').\'',$contenido) ;
		# Sustituimos las variables que se encuentran dentro de bloques
		preg_match_all('/{(([\w_]+\.)+)([\w_]+)}/U',$contenido,$a) ;
		$total = count($a[3]) ;
		for($i = 0 ; $i < $total ; $i++) {
			$pre_variable = $this->pre_variable($a[1][$i],$a[3][$i]) ;
			$pre_variable = '\'.(isset('.$pre_variable.') ? '.$pre_variable.' : \'\').\'' ;
			$contenido = str_replace($a[0][$i],$pre_variable,$contenido) ;
		}
		$lineas = explode("\n",$contenido) ;
		$total = count($lineas) ;
		for($i = 0 ; $i < $total ; $i++) {
			# Esta variable Servirápara identificar si existen bloques anidados
			# Ejemplo: 'bloque1.bloque2.bloque3. ... .bloque_final.'
			static $bloques = '' ;
			$lineas[$i] = rtrim($lineas[$i]) ;
			if(strpos($lineas[$i],'[blq ') !== false && preg_match('/\[blq ([\w_]+)\]/',$lineas[$i],$a)) {
				# Se agrega el bloque actual a la variable $bloques
				$bloques = $bloques.$a[1].'.' ;
				$pre_variable = $this->pre_variable($bloques) ;
				$lineas[$i] = '$total_'.$a[1].' = isset('.$pre_variable.') ? count('.$pre_variable.') : 0 ;'."\n" ;
				$lineas[$i] .= 'for($i_'.$a[1].' = 0 ; $i_'.$a[1].' < $total_'.$a[1].' ; $i_'.$a[1].'++) {' ;
			}
			elseif(strpos($lineas[$i],'[/blq ') !== false && preg_match('/\[\/blq ([\w_]+)\]/',$lineas[$i],$a)) {
				$lineas[$i] = '}' ;
				# Se quita el bloque actual a la variable $bloques
				
				$tmp = explode(".",$bloques);

				unset($tmp[0]);
				//$bloques = preg_replace($a[1].'\.$','',$bloques) ;
				$bloques = join(".",$tmp);
				
			}
			else {
				$lineas[$i] = '$resultado .= \''.$lineas[$i].'\'."\n" ;' ;
			}
		}
		eval(implode("\n",$lineas)) ;
		$resultado = preg_replace('/\n\n+/','',trim($resultado)) ;
		# Si $insertar es 1 el resultado se devuelve para poder insertarlo en otra plantilla
		if($insertar != 1) {
			echo $resultado ;
		}
		else {
			return $resultado ;
		}
	}
}
?>
