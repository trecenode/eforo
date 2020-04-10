<?php
/*
*************************************************
*** ePaginas v1.0
*** Creado por: Electros en 2004-2006
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************

ePaginas - Clase para paginar resultados MySQL
Copyright © 2005-2006 Daniel Osorio "Electros"

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class ePaginas {

	public $resultados;
	public $consulta;
	public $total_pag;
	public $total_res;

	public function __construct($a,$b) {
		require 'config.php' ;
		$this->consulta = $a ;
		# Obtener el total de resultados
		$con = $conectar->query(preg_replace('/select ([a-zA-Z0-9\_\-]]+) from/i','select count(*) from',$this->consulta)) ;
		$this->total_res = mysqli_result($con,0,0) ;
		# Resultados a mostrar por Página
		$this->resultados = $b ;
		# Total de Páginas
		$this->total_pag = ceil($this->total_res/$this->resultados) ;
	}
	# Si las siguientes variables no fueron modificadas se aplican sus valores por defecto
	function variables() {
		if(empty($this->u)) $this->u = array('?','&','=','') ; # <-- Sintaxis de URL (para su uso con mod_rewrite)
		if(empty($this->p)) $this->p = 'pag' ; # <-- Variable de Página
		if(empty($this->e)) $this->e = array('<a href="','">','</a>') ; # <-- Formato de enlace
		if(empty($this->m)) $this->m = 9 ; # <-- máximo de Páginas a mostrar (número impar)
	}
	# Procesar la consulta SQL
	function consultar() {
		require 'config.php' ;
		$this->variables() ; # <-- Comprobar variables
		if(empty($_GET[$this->p]) || !preg_match('^[0-9]+$',$_GET[$this->p])) $_GET[$this->p] = 1 ;
		elseif($this->total_pag > 0 && $_GET[$this->p] > $this->total_pag) $_GET[$this->p] = $this->total_pag ;
		$desde = ($_GET[$this->p] - 1) * $this->resultados ;

		
		return $conectar->query($this->consulta." limit $desde,$this->resultados") ;
	}
	# Obtener los datos de la URL
	function datos_url() {
		$url = '' ;
		foreach ($_GET as $variable => $valor) {
			# Se ignora la variable de Página para evitar que se repita
			if ($variable != $this->p) $url .= $variable.$this->u[2].urlencode($valor).$this->u[1] ;
		}
		return $url ;
	}
	function paginar() {
		$paginas = array() ;
		$datos_url = $this->datos_url() ;
		# Si se está despu�s de la primera Página se muestra la flecha de retroceder y el enlace a la primera Página
		$pag_anterior = $_GET[$this->p] - 1 ;
		if($pag_anterior >= 1) {
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].'1'.$this->u[3].$this->e[1].'Primera'.$this->e[2] ;
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$pag_anterior.$this->u[3].$this->e[1].'�'.$this->e[2] ;
		}
		# Se muestran los enlaces hacia las demás Páginas
		$pag_desde = $_GET[$this->p] - ($this->m - 1) / 2 ;
		if($pag_desde < 1) $pag_desde = 1 ;
		$pag_hasta = $_GET[$this->p] + ($this->m - 1) / 2 ;
		if($pag_hasta > $this->total_pag) $pag_hasta = $this->total_pag ;
		for($a = $pag_desde ; $a <= $pag_hasta ; $a++) {
			# Si se visita una Página se le quita el enlace
			$paginas[] = ($a != $_GET[$this->p]) ? $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$a.$this->u[3].$this->e[1].$a.$this->e[2] : $a ;
		}
		# Si se está antes de la última Página se muestra la flecha de avanzar y el enlace a la última Página
		$pag_siguiente = $_GET[$this->p] + 1 ;
		if($pag_siguiente <= $this->total_pag) {
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$pag_siguiente.$this->u[3].$this->e[1].'�'.$this->e[2] ;
			$paginas[] = $this->e[0].$_SERVER['PHP_SELF'].$this->u[0].$datos_url.$this->p.$this->u[2].$this->total_pag.$this->u[3].$this->e[1].'Ultima'.$this->e[2] ;
		}
		$paginas =
'<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Resultados: <b>'.$this->total_res.'</b> Páginas: <b>'.$this->total_pag.'</b></td>
<td><div align="right">'.implode(', ',$paginas).'</div></td>
</tr>
</table>' ;
		return $paginas ;
	}
}
?>
