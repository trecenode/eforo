<?php
/*
*************************************************
*** Firma 1.0
*** Creado por: Danilo Ulloa
*** Sitio web: https://electros.dev
*** Licencia: GNU General Public License
*************************************************
*/
ob_start();
// === Config ===
require '../foroconfig.php';
$c_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
function get_rango($userId) {
    global $conectar, $tabla_usuarios;
    $rangos = [];
    $buscar = $conectar->query('SELECT * FROM `eforo_rangos` ORDER BY `rango` ASC');
    while($datos = $buscar->fetch_assoc()) {
        $rangos[$datos['rango']] = [
            $datos['minimo'],
            $datos['descripcion']
        ];
    }
    $buscar->free();
    $buscar = $conectar->query("SELECT * FROM `{$tabla_usuarios}` WHERE `id`='{$userId}'");
    if($buscar->num_rows) {
        $datos = $buscar->fetch_assoc();
        $buscar->free();
        
        if($datos['rango_fijo']) {
            return $rangos[$datos['rango']][1];
        }
        else {
            $usuario_rango = $rangos[1][1];
            foreach($rangos as $rango) {
                if($rango[0] != 0 && $datos['mensajes'] >= $rango[0]) {
                    $usuario_rango = $rango[1];
                }
            }
            return $usuario_rango;
        }
    }
    
    return "Desconocido";
}
if ($c_id <= 0) {
    $name = "Usuario no encontrado";
    $avatarPath = "../eforo_imagenes/avatares/0.gif";
    $rank = "Invitado";
    $posts = 0;
} else {
    $con = $conectar->query("SELECT * FROM $tabla_usuarios WHERE id='$c_id'");
    if ($con && mysqli_num_rows($con) > 0) {
        $datos = mysqli_fetch_assoc($con);
        if (!empty($datos['avatar'])) {
            $avatarPath = '../eforo_imagenes/avatares/' . $datos['id'] . '.' . $datos['avatar'];
        } else {
            $avatarPath = '../eforo_imagenes/avatares/0.gif'; // Default avatar 60x60
        }
        $name = $datos['nick'];
        $rank = get_rango($c_id);
        $posts = 1234;
    } else {
        $name = "Usuario #$c_id no encontrado";
        $avatarPath = "../eforo_imagenes/avatares/0.gif";
        $rank = "Desconocido";
        $posts = 0;
    }
}

$width = 300;
$height = 80;
$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 30, 30, 30);
$text = imagecolorallocate($image, 255, 255, 255);
$accent = imagecolorallocate($image, 100, 200, 255);
imagefilledrectangle($image, 0, 0, $width, $height, $bg);
$avatarSize = 60;
$avatarX = 10;
$avatarY = ($height - $avatarSize) / 2;

if (file_exists($avatarPath)) {
    $extension = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
    
    switch ($extension) {
        case 'gif':
            $avatar = imagecreatefromgif($avatarPath);
            break;
        case 'jpg':
        case 'jpeg':
            $avatar = imagecreatefromjpeg($avatarPath);
            break;
        case 'png':
            $avatar = imagecreatefrompng($avatarPath);
            break;
        default:
            $avatar = null;
    }
    
    if ($avatar) {
        imagecopyresampled($image, $avatar, $avatarX, $avatarY, 0, 0, $avatarSize, $avatarSize, imagesx($avatar), imagesy($avatar));
        imagedestroy($avatar);
    } else {
        imagefilledrectangle($image, $avatarX, $avatarY, $avatarX + $avatarSize, $avatarY + $avatarSize, $accent);
    }
} else {
    imagefilledrectangle($image, $avatarX, $avatarY, $avatarX + $avatarSize, $avatarY + $avatarSize, $accent);
}

$fontBig = 5;
$fontSmall = 3;
$textX = $avatarX + $avatarSize + 10;

imagestring($image, $fontBig, $textX, 10, $name, $text);
imagestring($image, $fontSmall, $textX, 30, $rank, $text);
imagestring($image, $fontSmall, $textX, 50, "Electros.dev", $accent);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
ob_end_flush();