<?php
date_default_timezone_set("America/Bogota");
include("conexion.php");
include("utilidad_enviar_correo.php");

$correo_usuario = "arz.950203@gmail.com";
$nombre_completo = "Andrés Restrepo Zapata";
$resultado_correo = enviar_correo_confirmacion($correo_usuario, $nombre_completo);
file_put_contents('log_correos_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " ID_usuario_cliente: " . $fk_id_usuario_cliente_plan_comprado . " correo enviado a -> " . $correo_usuario . " Resultado: " . $resultado_correo . "\n\r", FILE_APPEND);
