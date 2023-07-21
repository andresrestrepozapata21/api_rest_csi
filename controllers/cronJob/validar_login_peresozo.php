<?php
//Seteo la zona horaria
date_default_timezone_set('America/Bogota');
//requiero los scripts que necesito
require_once "models/connection.php";
//capturo la fecha actual
$fecha = date('Y-m-d H:i:s');
//instancio la coneccion a la base de datos
$conexion = Connection::conexionAlternativa();
//SQl para traerme los clientes activos
$sentencia = "SELECT id_usuario_cliente, fecha_verificacion_pin FROM usuarios_clientes WHERE activo_usuario_cliente = 1";
$resultado = mysqli_query($conexion, $sentencia);
//SQl para traerme todos los documentos que sean de los clientes
$sentencia_documentos = "SELECT fk_id_usuario_cliente_documento FROM documentos WHERE fk_id_usuario_cliente_documento != 'null' ";
$resultado_documentos = mysqli_query($conexion, $sentencia_documentos);
//creo un array de documentos
$array_documentos = array();
//recorro el resultado de los documentos y los guardo en el arreglo
while ($fila_documentos = mysqli_fetch_assoc($resultado_documentos)) {
    array_push($array_documentos, $fila_documentos["fk_id_usuario_cliente_documento"]);
}

//elimino los elementos repetidos del array de documentos
$array_documentos = array_unique($array_documentos);

//recorro la consulta de los usuarios
while ($fila_usuario = mysqli_fetch_assoc($resultado)) {
    //capturo el id del usuario en cada iteracion
    $id = $fila_usuario["id_usuario_cliente"];
    //condicional, si la fecha de verificacion del pin es diferente de vacia
    if (!empty($fila_usuario["fecha_verificacion_pin"])) {
        //guardo esta fecha
        $fecha_verificacion_pin = $fila_usuario["fecha_verificacion_pin"];
        //creo una fecha +24horas despues de valido el PIn
        $nueva_fecha = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($fecha_verificacion_pin)));
        //si ya pasaron esas 24 horas despues de validado el PIN
        if ($fecha > $nueva_fecha) {
            //valido que el usuario ya haya cargado o no los documentos
            if (!in_array($id, $array_documentos)) {
                //modifico el flag activo_usuario_cliente en = 3 para que el usuario no pueda logearse y el front lo ponga a cargar documentos
                $actualizar_activo_cliente = "UPDATE `usuarios_clientes` SET `activo_usuario_cliente`= 3 WHERE id_usuario_cliente = $id";
                $resultado_actualizar_activo_cliente = mysqli_query($conexion, $actualizar_activo_cliente);
            }
        }
    }
}
