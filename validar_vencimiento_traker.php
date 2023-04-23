<?php
//Configuracion de la zona horaria
date_default_timezone_set('America/Bogota');
//Require de la conexion
require_once "models/connection.php";
//Fecha actual
$fecha = date('Y-m-d H:i:s');
//Instancia de la conexion
$conexion = Connection::conexionAlternativa();
$sentencia = "SELECT * FROM viajes v INNER JOIN usuarios_clientes uc ON v.fk_id_usuario_cliente_viaje=uc.id_usuario_cliente";
$consulta = mysqli_query($conexion, $sentencia);

//Solo si la consulta me trae registros
if (mysqli_num_rows($consulta) > 0) {
    //Recorro la consulta y realizo las validaciones
    while ($fila = mysqli_fetch_assoc($consulta)) {
        //Declaramos las variables que vamos a necesitar
        $fecha_inicio_viaje = $fila["date_created_viaje"];
        $fecha_estimada_viaje = $fila["fecha_estimada_recorrido_viaje"];
        $activo = $fila["activo_viaje"];
        $cancelado = $fila["cancelado_viaje"];
        $confirmacion_llegada_destino_viaje = $fila["confirmacion_llegada_destino_viaje"];
        $telefono = $fila["telefono_usuario_cliente"];
        //Validaciones, si el viaje no esta esta activo, no esta cancelado y no se ha confirmado como recorrido exitoso
        if ($activo != 0 && $cancelado != 1 && $confirmacion_llegada_destino_viaje != 1) {
            //Validamos que la fecha estimada ingresada por el usuario sea menor a la fecha actual
            if ($fecha > $fecha_estimada_viaje) {
                //Lanzamos alertas
                /*=============================================
                Consultamos el mensaje para el cliente
                =============================================*/
                //$sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 1";
                //$resultado = mysqli_query($conexion, $sentencia);
                //$fila = mysqli_fetch_assoc($resultado);
                //$mensaje = $fila["descripcion_plantilla_mensaje"];
                //$mensaje = str_replace("%nombre%", $nombre, $mensaje);
                $mensaje = "Este es el mensaje de un traker que no  se a cumplido";
                //$mensaje = str_replace("%servicio%", $nombre_evento, $mensaje);
                $url = 'http://api.mipgenlinea.com/serviceSMS.php';
                $data = array(
                    "usuario" => "smsFoxUser",
                    "password" => "rhjIMEI3*",
                    "telefono" => "+57" . $telefono,
                    "mensaje" => $mensaje,
                    "aplicacion" => "SMS Test Unitario",
                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                $result_SMS_cliente = CallAPI($url, $json, $header);
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " Traker cumplido sin confirmacion - SMS API -> $result_SMS_cliente\n\r", FILE_APPEND);
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
