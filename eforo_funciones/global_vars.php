<?php
/**
*************************************************
*** Variables globales para eForo
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************
*/

// Variables comunes de plantillas
$common_vars = [
    'foro_titulo' => $conf['foro_titulo'],
    'titulo' => $conf['foro_titulo'],
    'estilo' => $conf['estilo'],
    'plantilla' => $conf['plantilla']
];

// Rutas de plantillas
$template_paths = [
    'cabecera' => $conf['plantilla'].'cabecera.pta',
    'piedepagina' => $conf['plantilla'].'piedepagina.pta',
    'menu' => $conf['plantilla'].'foromenu.pta'
];

// Variables del menú
$menu_vars = [
    'subforo_indice_url' => $u[0].'foro'.$u[1].$u[5]
];

// Variables del usuario
$user_vars = [
    'total_en_linea' => $total_en_linea[0] + $total_en_linea[1],
    'usuarios_registrados' => $total_en_linea[1],
    'usuarios_anonimos' => $total_en_linea[0],
    'usuarios_reg_en_linea' => $reg_en_linea
];

// Función para cargar plantillas comunes
function load_common_templates() {
    global $ePiel, $template_paths;
    $ePiel->cargar($template_paths);
}

// Función para establecer variables comunes
function set_common_vars() {
    global $ePiel, $common_vars;
    $ePiel->variables($common_vars);
}

// Función para establecer variables del menú
function set_menu_vars() {
    global $ePiel, $menu_vars;
    $ePiel->variables($menu_vars);
}

// Función para establecer variables del usuario
function set_user_vars() {
    global $ePiel, $user_vars;
    $ePiel->variables($user_vars);
} 