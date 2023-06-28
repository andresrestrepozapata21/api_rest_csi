<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "backend_csi";
$conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);

if (!$conexion->set_charset("utf8")) {
    printf("", $conexion->error);
} else {
    printf("", $conexion->character_set_name());
}

if ($conexion->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}