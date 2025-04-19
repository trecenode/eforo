<?php
/*
*************************************************
*** eForo v4.1
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
require '../eforo_funciones/sesion.php';
require '../eforo_funciones/aviso.php';
require '../eforo_funciones/epaginas.php' ;

$ePiel->cargar(array(
    'cabecera' => '../'.$conf['plantilla'].'cabecera.pta',
    'piedepagina' => '../'.$conf['plantilla'].'piedepagina.pta'
    )) ;
$ePiel->variables(array(
    'titulo' => $conf['foro_titulo'].' - Panel de administración - Foros',
    'estilo' => '../'.$conf['estilo']
)) ;

if(!$es_administrador) {
    header("Location: ../$u[0]foro$u[1]$u[5]");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'foros';
$allowed_pages = ['foros', 'configuracion', 'rangos', 'permisos', 'usuarios', 'sincronizador'];
if (!in_array($page, $allowed_pages)) {
    $page = 'foros';
}
?>
<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>eForo 4.1 - Panel de administración</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 20%;
            background-color: #333;
            color: white;
            padding: 20px;
        }
        .main-content {
            width: 80%;
            padding: 20px;
        }
        .nav-link {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .nav-link:hover {
            background-color: #444;
        }
        .nav-title {
            font-size: 1.2em;
            margin: 15px 0 10px 0;
            color: #ddd;
        }
        .content-area {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <?php include 'menu.php'; ?>
        </div>
        
        <div class="main-content">
            <div class="content-area">
                <?php
                // Load the requested page
                $page_file = $page . '.php';
                if (file_exists($page_file)) {
                    include $page_file;
                } else {
                    echo '<h2>Página no encontrada</h2>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
