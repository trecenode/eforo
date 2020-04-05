<?php
/*
*************************************************
*** ePaginas v1.0
*** Creado por: Electros en 2006
*** Sitio web: www.13node.com
*** Licencia: GNU General Public License
*************************************************

ePaginas - Clase para paginar resultados MySQL
Copyright � 2005-2006 Daniel Osorio "Electros"

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class ePaginas {
	function ePaginas($a,$b) {
		$this->consulta = $a ;
		# Obtener el total de resultados
		$con = $conectar->query(eregi_replace('select (.+) from','select count(*) from',$this->consulta)) ;
		$this->total_res = mysqli_result($con,0,0) ;
		# Resultados a mostrar por p�gina
		$this->resultados = $b ;
		# Total de p�ginas
		$this->total_pag = ceil($this->total_res/$this->resultados) ;
	}
	# Si las siguientes variables no fueron modificadas se aplican sus valores por defecto
	function variables() {
		if(empty($this->u)) $this->u = array('?','&','=','') ; # <-- Sintaxis de URL (para su uso con mod_rewrite)
		if(empty($this->p)) $this->p = 'pag' ; # <-- Variable de p�gina
		if(empty($this->e)) $this->e = array('<a href="','">','</a>') ; # <-- Formato de enlace
		if(empty($this->m)) $this->m = 9 ; # <-- M�ximo de p�ginas a mostrar (n�mero impar)
	}
	# Procesar la consulta SQL
	function consultar() {
		$this->variables() ; # <-- Comprobar variables
		if(empty($_GET[$this->p]) || !ereg('^[0-9]+$',$_GET[$this->p])) $_GET[$this->p] = 1 ;
		elseif($this->total_pag > 0 && $_GET[$this->p] > $this->total_pag) $_GET[$this->p] = $this->total_pag ;
		$desde = ($_GET[$this->p] - 1) * $this->resultados ;
		return $conectar->query($this->consulta." limit $desde,$this->resultados") ;
	}
	# Obtener los datos de la URL
	function datos_url() {
		$url = '' ;
		foreach ($_GET as $variable => $valor) {
			# Se ignora la variable de p�gina para evitar que se repita
			if ($variable != $this->p) $url .= $variable.$this->u[2].urlencode($valor).$this->u[1] ;
		}
		return $url ;
	}
	function paginar() {
		$paginas = array() ;
		$datos_url = $this->datos_url() ;
		# Si se est� despu�s de la primera p�gina se muestra la flecha de retroceder y el enlace a la primera p�gina
		$pag_anterior = $_GET[$this->p] - 1 ;
		if($pag_anterior >= 1) {
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].'1'.$this->u[3].$this->e[1].'Primera'.$this->e[2] ;
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$pag_anterior.$this->u[3].$this->e[1].'�'.$this->e[2] ;
		}
		# Se muestran los enlaces hacia las dem�s p�ginas
		$pag_desde = $_GET[$this->p] - ($this->m - 1) / 2 ;
		if($pag_desde < 1) $pag_desde = 1 ;
		$pag_hasta = $_GET[$this->p] + ($this->m - 1) / 2 ;
		if($pag_hasta > $this->total_pag) $pag_hasta = $this->total_pag ;
		for($a = $pag_desde ; $a <= $pag_hasta ; $a++) {
			# Si se visita una p�gina se le quita el enlace
			$paginas[] = ($a != $_GET[$this->p]) ? $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$a.$this->u[3].$this->e[1].$a.$this->e[2] : $a ;
		}
		# Si se est� antes de la �ltima p�gina se muestra la flecha de avanzar y el enlace a la �ltima p�gina
		$pag_siguiente = $_GET[$this->p] + 1 ;
		if($pag_siguiente <= $this->total_pag) {
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$pag_siguiente.$this->u[3].$this->e[1].'�'.$this->e[2] ;
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$this->total_pag.$this->u[3].$this->e[1].'Ultima'.$this->e[2] ;
		}
		$paginas =
'<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Resultados: <b>'.$this->total_res.'</b> P�ginas: <b>'.$this->total_pag.'</b></td>
<td><div align="right">'.implode(', ',$paginas).'</div></td>
</tr>
</table>' ;
		return $paginas ;
	}
}
?>
