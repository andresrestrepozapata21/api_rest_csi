<?php
//Configuracion de la zona horaria
date_default_timezone_set('America/Bogota');
//Require de la conexion
require_once "models/connection.php";
//Fecha actual
$fecha = date('Y-m-d H:i:s');

//Instancia de la conexion
$conexion = Connection::conexionAlternativa();
$sentencia = "SELECT * FROM planes_comprados pc INNER JOIN planes p ON pc.fk_id_plan_plan_comprado=p.id_plan WHERE pc.activo_plan_comprado = 1";
$consulta = mysqli_query($conexion, $sentencia);

while ($row = mysqli_fetch_assoc($consulta)) {
    $fecha_creacion = $row["date_created_plan_comprado"];
    $vigencia_minutos = $row["vigencia_plan"];
    $id_plan_comprado = $row["id_plan_comprado"];

    // Crear un objeto DateTime a partir de la fecha actual
    $fecha_actual = new DateTime($fecha_creacion);
    // Sumar 10 minutos al objeto DateTime
    $fecha_actual->add(new DateInterval('PT'.$vigencia_minutos.'M'));
    // Obtener la nueva fecha y hora con los minutos añadidos
    $fecha_vencimiento = $fecha_actual->format('Y-m-d H:i:s');

    if($fecha > $fecha_vencimiento){
        $update = "UPDATE planes_comprados SET activo_plan_comprado = 0 WHERE id_plan_comprado=$id_plan_comprado";
        $update_query = mysqli_query($conexion, $update);
    }
}
