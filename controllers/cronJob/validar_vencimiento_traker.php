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
    $telefono = "+57" . $fila["telefono_usuario_cliente"];
    $notificacion = $fila["notificaciones_viaje"];
    $id_viaje = $fila["id_viaje"];
    $fk_id_usuario = $fila["fk_id_usuario_cliente_viaje"];
    $placeID_inicio = $fila["placeID_origen_viaje"];
    $fecha_ultimo_envio = $fila["fecha_ult_notificacion_viaje"];
    $token_dispositivo = $fila["token_dispositivo"];
    $nombre = $fila["nombre_usuario_cliente"];
    $apellido = $fila["apellido_usuario_cliente"];
    $email = $fila["apellido_usuario_cliente"];
    $genero = $fila["genero_usuario_cliente"];
    if($genero == "M"){
        $genero_chatico = "male";
    }else{
        $genero_chatico = "female";
    }
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
                # ------------- push notification -----------------
                $body = "Debes finalizar tu recorrido, de lo contratio este reportara como alerta CSI.";
                $url_push = "#";
                $title = $nombre . " " . $apellido . " te recordamos que:";
                $resut_push_notification = sendGCM($token_dispositivo,  $body, $title, $url_push);
                file_put_contents('./log_push_expired_tracker' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " PUSH -> $resut_push_notification\n\r", FILE_APPEND);
                $sentencia_0 = "UPDATE viajes SET notificaciones_viaje = 1, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                $consulta_0 = mysqli_query($conexion, $sentencia_0);
            } else if ($notificacion  == 1) {
                if ($fecha > $fecha_5min) {
                    /*=============================================
                    Enviamos mensaje de WPP
                    =============================================*/
                    /*
                    $url = 'http://api.mipgenlinea.com/sendWppChatico.php';
                    $data = array(
                        "user" => "smsFoxUser",
                        "password" => "rhjIMEI3*",
                        "first_name" => $nombre,
                        "last_name" => $apellido,
                        "name" => $nombre . " " . $apellido,
                        "phone" => $telefono,
                        "email" => $email,
                        "gender" => $genero_chatico,
                    );
                    $json = json_encode($data);
                    $header = array('Content-Type: application/json');
                    $resultado_chatico = new  PostController();
                    $result_chatico = $resultado_chatico->CallAPI($url, $json, $header);
                    file_put_contents('./log_push_expired_tracker: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " CHATICO API -> $result_chatico \r\n", FILE_APPEND);
                    */
                    $sentencia_1 = "UPDATE viajes SET notificaciones_viaje = 2, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                    $consulta_1 = mysqli_query($conexion, $sentencia_1);
                }
            } else if ($notificacion == 2) {
                if ($fecha > $fecha_5min) {
                    # -------------------------- llamada ---------------------------
                    //Llamada a los contactos de emergencia
                    $url = 'http://api.mipgenlinea.com/serviceIVR.php';
                    $urlAudio = "https://csi.mipgenlinea.com/audiosAlerta/xml-message-csi.xml";
                    $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => $telefono, 'mensaje' => $urlAudio, 'fecha' => 'NA', 'aplicacion' => 'CSI LLAMADA'];
                    $result_call = CallAPIIVR("POST", $url, json_encode($datos));
                    file_put_contents('./llamadas_traker_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "IVR API -> TELEFONO:" . $telefono . " - " . $result_call . ",\n\r", FILE_APPEND);

                    //Insertamos en la base de datos
                    $sentencia_2 = "UPDATE viajes SET notificaciones_viaje = 3, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                    $consulta_2 = mysqli_query($conexion, $sentencia_2);
                }
            } else if ($notificacion == 3) {
                if ($fecha > $fecha_5min) {
                    //echo "se envia una alerta a la central";
                    // Llamas a la función reverseGeocode con el PlaceID como argumento
                    $coordenadas = reverseGeocode($placeID_inicio);
                    //capturamos las coordenadas
                    $latitud_inicio = $coordenadas["latitud"];
                    $longitud_inicio = $coordenadas["longitud"];
                    //insertamos la alerta
                    $sentencia_3 = "INSERT INTO `alertas`(`latitud_alerta`, `longitud_alerta`, `estado_alerta`, `comentario_alerta`, `ruta1_imagen_alerta`,`fk_id_usuario_cliente_alerta`, `fk_id_servicio_por_zona_alerta`, `date_created_alerta`) VALUES ($latitud_inicio,$longitud_inicio,1,'No recibimos ninguna confirmación de que llegaste bien a tu destino usando la funcion ACOMPAÑAME. Hemos iniciado un protocolo de asistencia para comprobar que estás bien y llegaste a tu destino.', 'src/evidence_alerts/imagen_protocolo_acompaname.png',$fk_id_usuario,30,'$fecha')";
                    $consulta_3 = mysqli_query($conexion, $sentencia_3);
                    //Insertamos en la base de datos
                    $sentencia_4 = "UPDATE viajes SET notificaciones_viaje = 4, fecha_ult_notificacion_viaje = '$fecha' WHERE id_viaje = $id_viaje";
                    $consulta_4 = mysqli_query($conexion, $sentencia_4);
                }
            }
        }
    }
}

$conexion->close();

/*=============================================
METODOS AUXILIARES
=============================================*/
/*=============================================
METODO PARA LLAMAR EL API
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

//hacer la llamadas
function CallAPIIVR($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
            // default:
            //     if ($data)
            //         $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

// Función para realizar la inversión del PlaceID
function reverseGeocode($placeId)
{
    $apiKey = "AIzaSyCSnx5CZ4b0b4vF_-r8UJHnca6hmMpYyvY";
    $url = "https://maps.googleapis.com/maps/api/geocode/json?place_id=" . $placeId . "&key=" . $apiKey;

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] == 'OK') {
        $lat = $data['results'][0]['geometry']['location']['lat'];
        $lng = $data['results'][0]['geometry']['location']['lng'];

        $coordenadas = array(
            'latitud' => $lat,
            'longitud' => $lng
        );

        return $coordenadas;
    } else {
        $coordenadas = array(
            'latitud' => 0,
            'longitud' => 0
        );

        return $coordenadas;
    }
}

//enviar los push notification
function sendGCM($deviceToken, $body, $title, $url_push)
{
    define('API_ACCESS_KEY', 'key=AAAAPBpq6KE:APA91bH4B4CF3XR6gXosqn317XPu02riJ6u7aBNOIYgYak363HaD23k5oii4FvZ90sC1NV19-Mi8xW1aqhRTPnymGXeNhzjXihZJljEywO5h9YDBL5q64l-ty-eWbxNDe5LuF9f0tlrh');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    $notification_id = $deviceToken;

    $headers = array(
        'Authorization:' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $notification = [
        'title'  => '' . $title . '',
        'body'   => '' . $body . ''
    ];
    $extraNotificationData = ["body" => $body, "title" => $title, "url" => $url_push];

    $fcmNotification = [
        'notification'  => $notification,
        'registration_ids' => array($notification_id),
        'data' => $extraNotificationData,
        'priority' => 'high'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
