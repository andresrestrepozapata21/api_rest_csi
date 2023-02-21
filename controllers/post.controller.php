<?php

require_once "models/connection.php";
require_once "models/post.model.php";
require_once "models/get.filter.model.php";
include("correos/enviar_correo.php");

class PostController
{
    /*=============================================
    Peticion post para servicio
    =============================================*/
    static public function postService($data, $file)
    {

        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        $nombreArchivo = $file['name'];

        $target_path_nuevo = "src/images_services/";
        error_log("Nuevo Path: " . $target_path_nuevo);

        $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

        if (file_exists("./" . $target_path_nuevo)) {
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        } else {

            $response = PostModel::postService($data, $target_path_nuevo);
            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

            $return = new PostController();
            $return->fncResponse($response, null);
        }
    }

    /*=============================================
    Peticion post para planes
    =============================================*/
    static public function postPlan($data, $file)
    {

        /*=============================================
        Cargamos la imagen del plan
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        $nombreArchivo = $file['name'];

        $target_path_nuevo = "src/images_plans/";
        error_log("Nuevo Path: " . $target_path_nuevo);

        $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

        if (file_exists("./" . $target_path_nuevo)) {
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        } else {

            $response = PostModel::postPlan($data, $target_path_nuevo);
            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

            $return = new PostController();
            $return->fncResponse($response, null);
        }
    }

    /*=============================================
    Peticion post para las alertas
    =============================================*/
    static public function postAlert($data, $file)
    {

        $latitud = $data->latitud_alerta;
        $longitud = $data->longitud_alerta;
        $id_usuario_cliente = $data->fk_id_usuario_cliente_alerta;
        $id_servicio_por_zona =  $data->fk_id_servicio_por_zona_alerta;
        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        if (empty($file['name'])) {

            $response = PostModel::postAlert($data, null);

            PostController::enviar_SMS_llamada_push_email($response, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona);
        } else {

            $nombreArchivo = $file['name'];

            $target_path_nuevo = "src/evidence_alerts/" . $data->fk_id_usuario_cliente_alerta . "/";
            error_log("Nuevo Path: " . $target_path_nuevo);


            if (!file_exists("./" . $target_path_nuevo)) {
                if (mkdir("./" . $target_path_nuevo, 0777, true)) {
                    error_log("Exito! Carpeta creada:" . $target_path_nuevo);
                } else {
                    error_log(" :( No pudo crear:" . $target_path_nuevo);
                }
            } else {
                error_log("Carpeta existente:" . $target_path_nuevo);
            }


            $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

            if (file_exists("./" . $target_path_nuevo)) {
                $response = array(
                    "code" => 13
                );
                $return = new PostController();
                $return->fncResponse($response, null);
            } else {

                $response = PostModel::postAlert($data, $target_path_nuevo);
                move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                PostController::enviar_SMS_llamada_push_email($response, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona);
            }
        }
    }

    /*=============================================
    Peticion post para servicios por zona
    =============================================*/
    static public function postServicePerZone($data)
    {

        $responseServicePerZone = GetModel::getDataFilterServicePerZone("servicios_por_zona", $data);

        if (empty($responseServicePerZone)) {
            $response = PostModel::postServicePerZone($data);

            $return = new PostController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "code" => 18
            );
            $return = new PostController();
            $return->fncResponse($response, null);
        }
    }

    /*=============================================
    Peticion post para registrar las posiciones tanto de clientes como agentes
    =============================================*/
    static public function postPosition($table, $suffix, $data)
    {
        $response = PostModel::postWithoutPhoto($table, $suffix, $data);

        $return = new PostController();
        $return->fncResponse($response, null);
    }

    /*=============================================
    Peticion post para crear tipo de usuario
    =============================================*/
    static public function postTypeUser($table, $suffix, $data)
    {
        $response = PostModel::postWithoutPhoto($table, $suffix, $data);

        $return = new PostController();
        $return->fncResponse($response, null);
    }

    function enviar_SMS_llamada_push_email($response, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona)
    {
        $conexion = Connection::conexionAlternativa();

        $sentencia_evento = "SELECT * FROM servicios s INNER JOIN servicios_por_zona sz ON s.id_servicio=sz.fk_id_servicio_servicos_por_zona INNER JOIN zonas z ON sz.fk_id_zona_servicos_por_zona=z.id_zona WHERE sz.id_servicos_por_zona=$id_servicio_por_zona";
        $resultado_evento = mysqli_query($conexion, $sentencia_evento);
        $fila_evento = mysqli_fetch_assoc($resultado_evento);
        $nombre_evento = $fila_evento["descripcion_servicio"];
        $id_zona = $fila_evento["id_zona"];
        $id_servicio = $fila_evento["id_servicio"];

        /*=============================================
        Consultamos en que zona estamos
        =============================================*/
        $sentencia_listar = "SELECT * FROM `usuarios_clientes` WHERE id_usuario_cliente = $id_usuario_cliente";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);
        $fila_telefono = mysqli_fetch_assoc($resultado_listado);
        $telefono = $fila_telefono["telefono_usuario_cliente"];
        $nombre = $fila_telefono["nombre_usuario_cliente"];
        $apellido = $fila_telefono["apellido_usuario_cliente"];
        $email = $fila_telefono["email"];
        /*=============================================
        Consultamos el mensaje para el cliente
        =============================================*/
        $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 1";
        $resultado = mysqli_query($conexion, $sentencia);
        $fila = mysqli_fetch_assoc($resultado);
        $mensaje = $fila["descripcion_plantilla_mensaje"];
        $mensaje = str_replace("%nombre%", $nombre, $mensaje);
        $mensaje = str_replace("%servicio%", $nombre_evento, $mensaje);
        $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
        $data = array(
            "usuario" => "00486966949",
            "password" => "Juryzu57",
            "telefono" => $telefono,
            "mensaje" => $mensaje,
            "aplicacion" => "SMS Test Unitario"
        );
        $json = json_encode($data);
        $header = array('Content-Type: application/json');
        $resultado_sms = new  PostController();
        $result = $resultado_sms->CallAPI($url, $json, $header);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);


        /*========================================================================================================================================
                                                            Enviamos correo al cliente
        ===========================================================================================================================================*/
        //$result_email = enviar_correo_confirmacion("arz.950203@gmail.com", "Andres", "Alarma Incendio");
        //echo $result_email;
        $result_email = enviar_correo($email, $nombre, $nombre_evento);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "CORREO API enviado a -> $email RESULTADO: $result_email\n\r", FILE_APPEND);

        //envia a todos los contactos registrados
        $sentencia_contactos = "SELECT * FROM contactos where fk_id_usuario_cliente_contacto=$id_usuario_cliente";
        $resultado_contactos = mysqli_query($conexion, $sentencia_contactos);

        if (!$resultado_contactos) {
            $error2 = "error en SQL 2" . mysqli_error($conexion) . " SQL->" . $sentencia_contactos;
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "ERROR -> " . $error2 . "\n\r", FILE_APPEND);
            return;
        }

        file_put_contents('./log_' . date("j.n.Y") . '.txt', $url . "\n\r", FILE_APPEND);

        while ($fila_contactos = mysqli_fetch_assoc($resultado_contactos)) {
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "entrando al ciclo...\n\r", FILE_APPEND);
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SQL -> $sentencia_contactos\n\r", FILE_APPEND);

            $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
            $telefonoContacto = $fila_contactos["telefono_contacto"];
            //Primer Mensaje
            /*=============================================
            Consultamos el mensaje para el contacto de emergencia
            =============================================*/
            $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 2";
            $resultado = mysqli_query($conexion, $sentencia);
            $fila = mysqli_fetch_assoc($resultado);
            $mensaje = $fila["descripcion_plantilla_mensaje"];
            $mensaje = str_replace("%nombre%", $nombre, $mensaje);
            $mensaje = str_replace("%apellido%", $apellido, $mensaje);
            $mensaje = str_replace("%servicio%", $nombre_evento, $mensaje);
            $data = array(
                "usuario" => "00486966949",
                "password" => "Juryzu57",
                "telefono" => $telefonoContacto,
                "mensaje" => $mensaje,
                "aplicacion" => "SMS Test Unitario"
            );
            $json = json_encode($data);
            $header = array('Content-Type: application/json');
            $resultado_sms = new  PostController();
            $result_sms = $resultado_sms->CallAPI($url, $json, $header);
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms . ",\n\r", FILE_APPEND);

            //Segundo Mensaje
            /*=============================================
            Consultamos el mensaje para el cliente
            =============================================*/
            $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 3";
            $resultado = mysqli_query($conexion, $sentencia);
            $fila = mysqli_fetch_assoc($resultado);
            $mensaje = $fila["descripcion_plantilla_mensaje"];
            $mensaje = str_replace("%nombre%", $nombre, $mensaje);
            $mensaje = str_replace("%apellido%", $apellido, $mensaje);
            $data = array(
                "usuario" => "00486966949",
                "password" => "Juryzu57",
                "telefono" => $telefonoContacto,
                "mensaje" => $mensaje,
                "aplicacion" => "SMS Test Unitario"
            );
            $json = json_encode($data);
            $header = array('Content-Type: application/json');
            $resultado_sms = new  PostController();
            $result_sms2 = $resultado_sms->CallAPI($url, $json, $header);
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms2 . ",\n\r", FILE_APPEND);


            //Llamada a los contactos de emergencia
            $url = 'http://api.mipgenlinea.com/serviceIVR.php';
            $urlAudio = "https://csi.mipgenlinea.com/audiosAlerta/xml-message-csi.xml";
            $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => $telefonoContacto, 'mensaje' => $urlAudio, 'fecha' => 'NA', 'aplicacion' => 'CSI LLAMADA'];
            $resultado_sms2 = new  PostController();
            $result_sms3 = $resultado_sms2->CallAPIIVR("POST", $url, json_encode($datos));
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "IVR API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms3 . ",\n\r", FILE_APPEND);
        }
        /*=============================================
        Consultamos los lideres de zona para enviarles mensaje
        =============================================*/
        $sentencia_listar = "SELECT * FROM agentes_lideres_por_zona WHERE fk_id_zona_agente_lider_zona=$id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {

                /*=============================================
                Aqui van los mensajes sean Push o SMS o llamada.                                                    // FALTA 
                =============================================*/
            }
        }
        /*=============================================
        Consultamos los responsables del servicio
        =============================================*/
        $sentencia_listar = "SELECT * FROM responsables_por_servicios rps INNER JOIN responsables_servicios rs ON rs.id_responsable_servicio=rps.fk_id_responsable_servicio_responsable_por_servicio WHERE fk_id_servicio_responsable_por_servicio=$id_servicio";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                $telefono_responsable = $valor["telefono_responsable_servicio"];
                /*=============================================
                Consultamos el mensaje para el cliente
                =============================================*/
                $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 5";
                $resultado = mysqli_query($conexion, $sentencia);
                $fila = mysqli_fetch_assoc($resultado);
                $mensaje = $fila["descripcion_plantilla_mensaje"];
                $mensaje = str_replace("%servicio%", $nombre_evento, $mensaje);
                $mensaje = str_replace("%nombre%", $nombre, $mensaje);
                $mensaje = str_replace("%apellido%", $apellido, $mensaje);
                $mensaje = str_replace("%telefono%", $telefono, $mensaje);
                $mensaje = str_replace("%email%", $email, $mensaje);
                $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
                $data = array(
                    "usuario" => "00486966949",
                    "password" => "Juryzu57",
                    "telefono" => $telefono_responsable,
                    "mensaje" => $mensaje,
                    "aplicacion" => "SMS Test Unitario"
                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                $resultado_sms = new  PostController();
                $result = $resultado_sms->CallAPI($url, $json, $header);
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);
            }
        }
        /*=============================================
        Consultamos las posiciones de los agentes cercanos con menos de 1 hora para enviarles mensajes
        =============================================*/
        $sentencia_listar = "SELECT * FROM posiciones_agentes";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $suffix = "posicion_agente";

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {

                $distancia = PostController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

                if ($distancia <= 100) {
                    if (round($distancia * 1000) <= 1000) {

                        $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                        $valor["minutos"] = '' . PostController::contarDias(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

                        if ($valor["minutos"] <= 60) {
                            /*=============================================
                            Aqui van los mensajes sean Push o SMS o llamada.                                                    // FALTA 
                            =============================================*/
                        }
                    }
                }
            }
        }

        $return = new PostController();
        $return->fncResponse($response, $result);
    }

    /*=============================================
    METODO PARA LLAMAR EL API serviceSMS2
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

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    function contarDias($fecha1, $fecha2)
    {
        $startTimeStamp = strtotime($fecha1);
        $endTimeStamp = strtotime($fecha2);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 60;  // 60 seconds in one minute
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    public function sendGCM($deviceToken)
    {
        define('API_ACCESS_KEY', '<AQUI LA LLAVE>');
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification_id = $deviceToken;

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $notification = [
            'title'  => '' . "notificaciones de prueba" . '',
            'body'   => '' . "esto es una prueba de notificaciones push" . ''
        ];
        $extraNotificationData = ["message" => $notification, "moredata" => 'dd'];

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

        echo $result;
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response, $result)
    {
        if ($result == null) {
            if (!empty($response)) {
                if ($response['code'] == 3) {
                    $json  = array(

                        'status' => 200,
                        'result' => $response["code"],
                        'method' => $_SERVER['REQUEST_METHOD']
                    );
                } else {
                    $json = array(
                        'status' => 200,
                        'result' => $response['code'],
                        'method' => $_SERVER['REQUEST_METHOD']
                    );
                }
            } else {
                $json = array(
                    'status' => 404,
                    'result' => 'Not Found',
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            }
        } else {
            if (!empty($response)) {
                if ($response['code'] == 3) {
                    $json  = array(

                        'status' => 200,
                        'result' => $response["code"],
                        'method' => $_SERVER['REQUEST_METHOD'],
                        'detail' => json_decode($result)
                    );
                } else {
                    $json = array(
                        'status' => 200,
                        'result' => $response['code'],
                        'method' => $_SERVER['REQUEST_METHOD']
                    );
                }
            } else {
                $json = array(
                    'status' => 404,
                    'result' => 'Not Found',
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            }
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
