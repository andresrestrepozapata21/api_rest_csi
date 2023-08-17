<?php
//Configuracion de la zona horaria
date_default_timezone_set('America/Bogota');
//Require de la conexion
require_once "models/connection.php";
//Fecha actual
$fecha = date('Y-m-d H:i:s');
//Instancia de la conexion
$conexion = Connection::conexionAlternativa();

/*=================================================================================================
            Codigo para las notificaciones de confirmacion del usuario VISTIMA
=================================================================================================== */
//Busco todas las reacciones en las cuales la victima no ha confirmado la asistencia
$sql_vistima = "SELECT * FROM `reacciones_cliente_cliente` WHERE confirmacionVictima = 0";
$query_vistima = mysqli_query($conexion, $sql_vistima);
//Si la consulta tiene algo
if (mysqli_num_rows($query_vistima) > 0) {
    //Obtengo y recorro los datos de la consulta con un FetchAssoc
    while ($datos_reaccion = mysqli_fetch_assoc($query_vistima)) {
        //capturo las variables que necesito
        $id_reaccion_cliente_cliente  = $datos_reaccion["id_reaccion_cliente_cliente"];
        $fk_id_alerta = $datos_reaccion["fk_id_alerta_reaccion_cliente_cliente"];
        $fk_id_usuario_cliente = $datos_reaccion["fk_id_usuario_cliente_reaccion_cliente_cliente"];
        $notificacion = $datos_reaccion["notificacionesVictima"];
        $fecha_ult_notificacion_victima = $datos_reaccion["fecha_ult_notificacion_victima"];
        $fecha_creaccion_reaccion = $datos_reaccion["date_created_reaccion_cliente_cliente"];

        // Sumar los minutos a la fecha utilizando strtotime() de la creacion de la reaccion
        $fecha_5min_despues_creacion_reaccion = date('Y-m-d H:i:s', strtotime($fecha_creaccion_reaccion . ' + 5 minutes'));

        // Crear un objeto DateTime a partir de la fecha dada
        $datetime = new DateTime($fecha_ult_notificacion_victima);
        // Sumar 5 minutos
        $datetime->add(new DateInterval('PT5M'));
        // Obtener la fecha resultante en el formato deseado
        $fecha_5min_notificacion = $datetime->format('Y-m-d H:i:s');

        //Busco los datos de la alerta, info de la alerta, de la victima
        $sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta =uc.id_usuario_cliente WHERE a.id_alerta = $fk_id_alerta";
        $resultado = mysqli_query($conexion, $sentencia);
        $fila = mysqli_fetch_assoc($resultado);
        //capturo los datos de la alerta y del usuario victima que necesite
        $id_alerta = $fila["id_alerta"];
        $nombre_cliente_victima = $fila["nombre_usuario_cliente"];
        $apellido_cliente_victima = $fila["apellido_usuario_cliente"];
        $telefono_cliente_victima = $fila["telefono_usuario_cliente"];
        $tokenDeviceVistima = $fila["token_dispositivo"];
        //imprimo la sentencia en el log
        error_log($sentencia);

        //busca los datos del usuario que reacciono
        $sentencia_usuario_reaccion = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$fk_id_usuario_cliente";
        $resultado_usuarios_reaccion = mysqli_query($conexion, $sentencia_usuario_reaccion);
        $filas_usuario_reaccion = mysqli_fetch_assoc($resultado_usuarios_reaccion);
        //Capturo los datos del usuario que esta reaccionando
        $id_usuario_cliente_reaccion = $filas_usuario_reaccion["id_usuario_cliente"];
        $nombre_usuario_cliente_reaccion = $filas_usuario_reaccion["nombre_usuario_cliente"];
        $apellido_usuario_cliente_reaccion = $filas_usuario_reaccion["apellido_usuario_cliente"];
        $tokenDeviceReaccion = $filas_usuario_reaccion["token_dispositivo"];

        //Condicion que valida si pasaron 5 minutos despues de la creacion de la reaccion.
        if ($fecha > $fecha_5min_despues_creacion_reaccion) {
            # ------------- SMS con el link para que confirme y push notification----------------- #
            //defino los mensajes sms y push
            $mensaje = "Hola %nombre% %apellido%, por favor confirmanos que el usuario %nombre2% %apellido2% atendio tu emergencia, INGRESA AQUI -> https://apicsi.mipgenlinea.com/reacciones/confirmarVictima.php?id_reaccion=$id_reaccion_cliente_cliente";
            $mensaje = str_replace("%nombre%", $nombre_cliente_victima, $mensaje);
            $mensaje = str_replace("%apellido%", $apellido_cliente_victima, $mensaje);
            $mensaje = str_replace("%nombre2%", $nombre_usuario_cliente_reaccion, $mensaje);
            $mensaje = str_replace("%apellido2%", $apellido_usuario_cliente_reaccion, $mensaje);
            $body = "CSI Reaccion - Hola %nombre% %apellido%, por favor confirmanos que el usuario %nombre2% %apellido2% atendio tu emergencia.";
            $body = str_replace("%nombre%", $nombre_cliente_victima, $body);
            $body = str_replace("%apellido%", $apellido_cliente_victima, $body);
            $body = str_replace("%nombre2%", $nombre_usuario_cliente_reaccion, $body);
            $body = str_replace("%apellido2%", $apellido_usuario_cliente_reaccion, $body);
            //estructura condicional que me verifica en que notificaciones esta
            if ($notificacion == 0) {
                //Llamo mi metodo para enviar el SMS con los datos correspondiente
                enviarSMS($mensaje, $telefono_cliente_victima, "VICTIMA");
                # ------------- push notification para el usuario que este en cada iteracion ----------------- #
                $url_push = "https://apicsi.mipgenlinea.com/reacciones/confirmarVictima.php?id_reaccion=$id_reaccion_cliente_cliente";
                $title = "Atención Confirma la Asistencia a tu Emergencia";
                $resut_push_notification = sendGCM($tokenDeviceVistima, $body, $title, $url_push);
                file_put_contents('./log_confirmacion_reacciones' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " PUSH API VICTIMA -> $resut_push_notification\n\r", FILE_APPEND);
                //modifico en la base de datos el respectivo flag
                $sentencia_0 = "UPDATE reacciones_cliente_cliente SET notificacionesVictima= 1,fecha_ult_notificacion_victima='$fecha' WHERE id_reaccion_cliente_cliente = $id_reaccion_cliente_cliente";
                $consulta_0 = mysqli_query($conexion, $sentencia_0);
            } else if ($notificacion != 0 && $notificacion < 3) {
                if ($fecha > $fecha_5min_notificacion) {
                    //sumamos el flag en 1 para evidencias que se realizo otro envio
                    $notificacion++;
                    //Llamo mi metodo para enviar el SMS con los datos correspondiente
                    enviarSMS($mensaje, $telefono_cliente_victima, "VICTIMA");
                    # ------------- push notification para el usuario que este en cada iteracion ----------------- #
                    $url_push = "https://apicsi.mipgenlinea.com/reacciones/confirmarVictima.php?id_reaccion=$id_reaccion_cliente_cliente";
                    $title = "Atención Confirma la Asistencia a tu Emergencia";
                    $resut_push_notification = sendGCM($tokenDeviceVistima, $body, $title, $url_push);
                    file_put_contents('./log_confirmacion_reacciones' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " PUSH API VICTIMA -> $resut_push_notification\n\r", FILE_APPEND);
                    //modifico en la base de datos el respectivo flag aumentado en 1
                    $sentencia_0 = "UPDATE reacciones_cliente_cliente SET notificacionesVictima= $notificacion,fecha_ult_notificacion_victima='$fecha' WHERE id_reaccion_cliente_cliente = $id_reaccion_cliente_cliente";
                    $consulta_0 = mysqli_query($conexion, $sentencia_0);
                }
            }
        }
    }
}


/*=================================================================================================
            Codigo para las notificaciones de confirmacion del usuario REACCION
=================================================================================================== */
//Busco todas las reacciones en las cuales la victima no ha confirmado la asistencia
$sql_reaccion = "SELECT * FROM `reacciones_cliente_cliente` WHERE confirmacionReaccion = 0";
$query_reaccion = mysqli_query($conexion, $sql_reaccion);
//Si la consulta tiene algo
if (mysqli_num_rows($query_reaccion) > 0) {
    //Obtengo y recorro los datos de la consulta con un FetchAssoc
    while ($datos_reaccion = mysqli_fetch_assoc($query_reaccion)) {
        //capturo las variables que necesito
        $id_reaccion_cliente_cliente  = $datos_reaccion["id_reaccion_cliente_cliente"];
        $fk_id_alerta = $datos_reaccion["fk_id_alerta_reaccion_cliente_cliente"];
        $fk_id_usuario_cliente = $datos_reaccion["fk_id_usuario_cliente_reaccion_cliente_cliente"];
        $notificacion = $datos_reaccion["notificacionesReaccion"];
        $fecha_ult_notificacion_reaccion = $datos_reaccion["fecha_ult_notificacion_reaccion"];
        $fecha_creaccion_reaccion = $datos_reaccion["date_created_reaccion_cliente_cliente"];

        // Sumar los minutos a la fecha utilizando strtotime() de la creacion de la reaccion
        $fecha_5min_despues_creacion_reaccion = date('Y-m-d H:i:s', strtotime($fecha_creaccion_reaccion . ' + 5 minutes'));

        // Crear un objeto DateTime a partir de la fecha dada
        $datetime = new DateTime($fecha_ult_notificacion_reaccion);
        // Sumar 5 minutos
        $datetime->add(new DateInterval('PT5M'));
        // Obtener la fecha resultante en el formato deseado
        $fecha_5min_notificacion = $datetime->format('Y-m-d H:i:s');

        //Busco los datos de la alerta, info de la alerta, de la victima
        $sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta =uc.id_usuario_cliente WHERE a.id_alerta = $fk_id_alerta";
        $resultado = mysqli_query($conexion, $sentencia);
        $fila = mysqli_fetch_assoc($resultado);
        //capturo los datos de la alerta y del usuario victima que necesite
        $id_alerta = $fila["id_alerta"];
        $nombre_cliente_victima = $fila["nombre_usuario_cliente"];
        $apellido_cliente_victima = $fila["apellido_usuario_cliente"];
        $tokenDeviceVistima = $filas["token_dispositivo"];
        //imprimo la sentencia en el log
        error_log($sentencia);

        //busca los datos del usuario que reacciono
        $sentencia_usuario_reaccion = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$fk_id_usuario_cliente";
        $resultado_usuarios_reaccion = mysqli_query($conexion, $sentencia_usuario_reaccion);
        $filas_usuario_reaccion = mysqli_fetch_assoc($resultado_usuarios_reaccion);
        //Capturo los datos del usuario que esta reaccionando
        $id_usuario_cliente_reaccion = $filas_usuario_reaccion["id_usuario_cliente"];
        $nombre_usuario_cliente_reaccion = $filas_usuario_reaccion["nombre_usuario_cliente"];
        $apellido_usuario_cliente_reaccion = $filas_usuario_reaccion["apellido_usuario_cliente"];
        $telefono_usuario_cliente_reaccion = $filas_usuario_reaccion["telefono_usuario_cliente"];
        $tokenDeviceReaccion = $filas_usuario_reaccion["token_dispositivo"];


        //Condicion que valida si pasaron 5 minutos despues de la creacion de la reaccion.
        if ($fecha > $fecha_5min_despues_creacion_reaccion) {
            # ------------- SMS con el link para que confirme -----------------
            $mensaje = "Hola %nombre2% %apellido2%, por favor confirmanos que has atendido la emergencia de %nombre% %apellido%, INGRESA AQUI -> https://apicsi.mipgenlinea.com/reacciones/confirmarReaccion.php?id_reaccion=$id_reaccion_cliente_cliente";
            $mensaje = str_replace("%nombre%", $nombre_cliente_victima, $mensaje);
            $mensaje = str_replace("%apellido%", $apellido_cliente_victima, $mensaje);
            $mensaje = str_replace("%nombre2%", $nombre_usuario_cliente_reaccion, $mensaje);
            $mensaje = str_replace("%apellido2%", $apellido_usuario_cliente_reaccion, $mensaje);
            $body = "CSI Reaccion - Hola %nombre2% %apellido2%, por favor confirmanos que has atendido la emergencia de %nombre% %apellido%.";
            $body = str_replace("%nombre%", $nombre_cliente_victima, $body);
            $body = str_replace("%apellido%", $apellido_cliente_victima, $body);
            $body = str_replace("%nombre2%", $nombre_usuario_cliente_reaccion, $body);
            $body = str_replace("%apellido2%", $apellido_usuario_cliente_reaccion, $body);
            //estructura condicional que me verifica en que notificaciones esta
            if ($notificacion == 0) {
                //Llamo mi metodo para enviar el SMS con los datos correspondiente
                enviarSMS($mensaje, $telefono_usuario_cliente_reaccion, "REACCION");
                # ------------- push notification para el usuario que este en cada iteracion ----------------- #
                $url_push = "https://apicsi.mipgenlinea.com/reacciones/confirmarReaccion.php?id_reaccion=$id_reaccion_cliente_cliente";
                $title = "Atención Confirma que Reaccionaste a esta Emergencia";
                $resut_push_notification = sendGCM($tokenDeviceReaccion, $body, $title, $url_push);
                file_put_contents('./log_confirmacion_reacciones' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " PUSH API REACCION -> $resut_push_notification\n\r", FILE_APPEND);
                //modifico en la base de datos el respectivo flag
                $sentencia_0 = "UPDATE reacciones_cliente_cliente SET notificacionesReaccion= 1,fecha_ult_notificacion_reaccion='$fecha' WHERE id_reaccion_cliente_cliente = $id_reaccion_cliente_cliente";
                $consulta_0 = mysqli_query($conexion, $sentencia_0);
            } else if ($notificacion != 0 && $notificacion < 3) {
                if ($fecha > $fecha_5min_notificacion) {
                    //sumamos el flag en 1 para evidencias que se realizo otro envio
                    $notificacion++;
                    //Llamo mi metodo para enviar el SMS con los datos correspondiente
                    enviarSMS($mensaje, $telefono_usuario_cliente_reaccion, "REACCION");
                    # ------------- push notification para el usuario que este en cada iteracion ----------------- #
                    $url_push = "https://apicsi.mipgenlinea.com/reacciones/confirmarReaccion.php?id_reaccion=$id_reaccion_cliente_cliente";
                    $title = "Atención Confirma que Reaccionaste a esta Emergencia";
                    $resut_push_notification = sendGCM($tokenDeviceReaccion, $body, $title, $url_push);
                    file_put_contents('./log_confirmacion_reacciones' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " PUSH API REACCION -> $resut_push_notification\n\r", FILE_APPEND);
                    //modifico en la base de datos el respectivo flag aumentado en 1
                    $sentencia_0 = "UPDATE reacciones_cliente_cliente SET notificacionesReaccion= $notificacion,fecha_ult_notificacion_reaccion='$fecha' WHERE id_reaccion_cliente_cliente = $id_reaccion_cliente_cliente";
                    $consulta_0 = mysqli_query($conexion, $sentencia_0);
                }
            }
        }
    }
}

/*=================================================================================================
                                        Metodos Auxiliares
=================================================================================================== */
//Metodo intermediario para ser reutilizado para enviar los SMS
function enviarSMS($mensaje, $telefono, $tipo_usuario)
{
    $url = 'http://api.mipgenlinea.com/serviceSMS.php';
    $data = array(
        "usuario" => "smsFoxUser",
        "password" => "rhjIMEI3*",
        "telefono" => "+57" . $telefono,
        "mensaje" => $mensaje,
        "aplicacion" => "SMS Confirmacion Reaccion Cliente-Cliente",
    );
    $json = json_encode($data);
    $header = array('Content-Type: application/json');
    //llamo el metodo para realizar el envio del SMS
    $result_SMS_victima = CallAPI($url, $json, $header);
    file_put_contents('./log_confirmacion_reacciones' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " SMS API $tipo_usuario-> $result_SMS_victima\n\r", FILE_APPEND);
}

//metodo para llamar a la api de los sms
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
