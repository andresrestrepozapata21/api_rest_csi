<?php

require_once "models/connection.php";
require_once "models/post.model.php";
require_once "models/get.filter.model.php";

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

        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

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

            $conexion = Connection::conexionAlternativa();

            $id_usuario_cliente = $data->fk_id_usuario_cliente_alerta;
            $id_servicio_por_zona =  $data->fk_id_servicio_por_zona_alerta;

            $sentencia_evento = "SELECT * FROM servicios where id_servicio=$id_servicio_por_zona";
            $resultado_evento = mysqli_query($conexion, $sentencia_evento);
            $fila_evento = mysqli_fetch_assoc($resultado_evento);
            $nombre_evento = $fila_evento["descripcion_servicio"];

            /*=============================================
            Consultamos en que zona estamos
            =============================================*/
            $sentencia_listar = "SELECT * FROM `usuarios_clientes` WHERE id_usuario_cliente = $id_usuario_cliente";
            $resultado_listado = mysqli_query($conexion, $sentencia_listar);
            $fila_telefono = mysqli_fetch_assoc($resultado_listado);

            $telefono = $fila_telefono["telefono_usuario_cliente"];
            $nombre = $fila_telefono["nombre_usuario_cliente"];
            $apellido = $fila_telefono["apellido_usuario_cliente"];
            $mensaje = "Hola $nombre, nuestra central ha recibido tu alerta de $nombre_evento, en breve nos comunicaremos contigo. Si te es posible ten tu telefono a la mano, y ubícate en un sitio seguro. El equipo de CSI está en reacción";

            $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
            //$datos = ['usuario' => '00486966949', 'password' => 'Juryzu57', 'telefono' => $telefono, 'mensaje' => $mensaje, 'fecha' => 'NA', 'aplicacion' => 'CSI ALERTA'];

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
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result", FILE_APPEND);

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
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SQL -> $sentencia_contactos", FILE_APPEND);


                $telefonoContacto = $fila_contactos["telefono_contacto"];
                $nombreContacto = $fila_contactos["nombre_contacto"];
                $mensaje = "Hola! Tu amigo $nombre $apellido, reportó un evento de pelígro: $nombre_evento y tu eres su contacto de Emergencia. El equipo CSI esta en reaccion, por favor te pedimos estés alerta para apoyarnos. Muchas Gracias!";
                $url = "http://190.60.119.74:8009/send?username=darbelae&password=Juryzu57&to=57$telefonoContacto&content=$mensaje&from=apihttp";

                //SMS a los contactos de emergencia
                $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
                //Primer Mensaje
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
                $result_sms = $resultado_sms->CallAPI($url, $json, $header);
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms . ",", FILE_APPEND);
                //Segundo Mensaje
                $mensaje = "Mira aqui lo que $nombre $apellido, reportó y quiere que lo sepas -> AQUI VA EL LINK";
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
                $result_sms2 = $resultado_sms->CallAPI($url, $json, $header);
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms2 . ",", FILE_APPEND);


                //Llamada a los contactos de emergencia
                $urlAudio = "https://csi.mipgenlinea.com/audiosAlerta/xml-message-csi.xml";
                $url = 'http://api.mipgenlinea.com/serviceIVR.php';
                $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => $telefonoContacto, 'mensaje' => $urlAudio, 'fecha' => 'NA', 'aplicacion' => 'CSI LLAMADA'];

                $resultado_sms2 = new  PostController();
                $result_sms3 = $resultado_sms2->CallAPIIVR("POST", $url, json_encode($datos));
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "IVR API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms3 . ",", FILE_APPEND);
            }

            $return = new PostController();
            $return->fncResponse($response, $result);
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