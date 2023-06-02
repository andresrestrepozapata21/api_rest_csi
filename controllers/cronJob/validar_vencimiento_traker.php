<?php
//Configuracion de la zona horaria
date_default_timezone_set('America/Bogota');
//Require de la conexion
require_once "models/connection.php";
//Fecha actual
$fecha = date('Y-m-d H:i:s');
//Instancia de la conexion
$conexion = Connection::conexionAlternativa();
$sentencia = "SELECT * FROM viajes v INNER JOIN usuarios_clientes uc ON v.fk_id_usuario_cliente_viaje=uc.id_usuario_cliente WHERE activo_viaje = 1";
$consulta = mysqli_query($conexion, $sentencia);

//Recorro la consulta y realizo las validaciones
while ($fila = mysqli_fetch_assoc($consulta)) {
    //Declaramos las variables que vamos a necesitar
    $fecha_inicio_viaje = $fila["date_created_viaje"];
    $fecha_estimada_viaje = $fila["fecha_estimada_recorrido_viaje"];
    $activo = $fila["activo_viaje"];
    $cancelado = $fila["cancelado_viaje"];
    $confirmacion_llegada_destino_viaje = $fila["confirmacion_llegada_destino_viaje"];
    $telefono = $fila["telefono_usuario_cliente"];
    $notificacion = $fila["notificaciones_viaje"];
    $id_viaje = $fila["id_viaje"];
    $fecha_ultimo_envio = $fila["fecha_ult_notificacion_viaje"];
    // Crear un objeto DateTime a partir de la fecha dada
    $datetime = new DateTime($fecha_ultimo_envio);
    // Sumar 5 minutos
    $datetime->add(new DateInterval('PT5M'));
    // Obtener la fecha resultante en el formato deseado
    $fecha_5min = $datetime->format('Y-m-d H:i:s');

    //Validaciones, si el viaje no esta esta activo, no esta cancelado y no se ha confirmado como recorrido exitoso
    if ($activo == 1 && $cancelado == 0 && $confirmacion_llegada_destino_viaje == 0) {
        //Validamos que la fecha estimada ingresada por el usuario sea menor a la fecha actual
        if ($fecha > $fecha_estimada_viaje) {
            if ($notificacion == 0) {
                # push notification
                $sentencia_0 = "UPDATE viajes SET notificaciones_viaje = 1, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                $consulta_0 = mysqli_query($conexion, $sentencia_0);
            } else if ($notificacion  == 1) {
                if ($fecha > $fecha_5min) {
                    # wpp wpp
                    $sentencia_1 = "UPDATE viajes SET notificaciones_viaje = 2, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                    $consulta_1 = mysqli_query($conexion, $sentencia_1);
                }
            } else if ($notificacion == 2) {
                if ($fecha > $fecha_5min) {
                    # llamada
                    $sentencia_2 = "UPDATE viajes SET notificaciones_viaje = 3, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                    $consulta_2 = mysqli_query($conexion, $sentencia_2);
                }
            } else if ($notificacion == 3) {
                if ($fecha > $fecha_5min) {
                    echo "se envia una alerta a la central";
                }
            }
        }
    }
}


/*=============================================
METODOS AUXILIARES
=============================================*/
function CallAPI($url, $json, $header)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return $response;
}
