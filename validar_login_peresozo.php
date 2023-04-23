<?php
date_default_timezone_set('America/Bogota');
require_once "models/connection.php";

$fecha = date('Y-m-d H:i:s');

$conexion = Connection::conexionAlternativa();
$sentencia = "SELECT id_usuario_cliente, fecha_verificacion_pin FROM usuarios_clientes WHERE activo_usuario_cliente = 1";
$resultado = mysqli_query($conexion, $sentencia);

$sentencia_documentos = "SELECT fk_id_usuario_cliente_documento FROM documentos WHERE fk_id_usuario_cliente_documento != 'null' ";
$resultado_documentos = mysqli_query($conexion, $sentencia_documentos);

$array_documentos = array();
while ($fila_documentos = mysqli_fetch_assoc($resultado_documentos)) {
    array_push($array_documentos, $fila_documentos["fk_id_usuario_cliente_documento"]);
}

$array_documentos = array_unique($array_documentos);

while ($fila_usuario = mysqli_fetch_assoc($resultado)) {
    $id = $fila_usuario["id_usuario_cliente"];
    if (!empty($fila_usuario["fecha_verificacion_pin"])) {
        $fecha_verificacion_pin = $fila_usuario["fecha_verificacion_pin"];
        $nueva_fecha = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($fecha_verificacion_pin)));
        if ($fecha > $nueva_fecha) {
            if (!in_array($id, $array_documentos)) {
                //modifico el flag activo_usuario_cliente en = 3 para que el usuario no pueda logearse
                $actualizar_activo_cliente = "UPDATE `usuarios_clientes` SET `activo_usuario_cliente`= 3 WHERE id_usuario_cliente = $id";
                $resultado_actualizar_activo_cliente = mysqli_query($conexion, $actualizar_activo_cliente);
                echo "encontro 1";
                echo "<br>";
            }
        }
    }
}
