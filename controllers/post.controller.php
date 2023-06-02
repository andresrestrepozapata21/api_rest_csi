<?php

use PostController as GlobalPostController;

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
    Peticion post para foto de un viaje
    =============================================*/
    static public function postTripPicture($data, $file)
    {
        /*=============================================
        Cargamos la imagen del plan
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        $nombreArchivo = $file['name'];

        $target_path_nuevo = "src/images_trip_pictures/" . $data->fk_id_viaje_registro_fotografico_viaje . "/";
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
            $return->fncResponse($response);
        } else {

            $response = PostModel::postTripPicture($data, $target_path_nuevo);
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

        //guardo en una variable la bandera para saber si enviar o no notificaciones a los contactos de emergencia del usuario
        $notificar_contactos = $data->notificar_contactos;
        unset($data->notificar_contactos);

        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        if (empty($file[0]['name']) && empty($file[1]['name']) && empty($file[2]['name'])) {

            //conexion a la base de datos
            $conexion = Connection::conexionAlternativa();

            //registramos la alerta
            $response_alert = PostModel::postAlert($data, null);

            //consultamos el acumulado de puntos de este usuario
            $sentencia_acumulado = "SELECT * FROM puntos_ganados WHERE fk_id_usuario_cliente_punto_ganado = $id_usuario_cliente ORDER BY date_created_punto_ganado DESC LIMIT 1";
            $resultado_acumularo = mysqli_query($conexion, $sentencia_acumulado);
            $puntos_acumulado = 0;

            if ($resultado_acumularo) {
                $fila_aculumado = mysqli_fetch_assoc($resultado_acumularo);
                $puntos_acumulado = $fila_aculumado['acumulado_puntos_punto_ganado'];
            }

            //consultamos los datos que faltan para registrar los puntos ganados de este usuario
            $sentencia_servicio = "SELECT * FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio WHERE sz.id_servicos_por_zona = $id_servicio_por_zona";
            $resultado_servicio = mysqli_query($conexion, $sentencia_servicio);
            $fila_servicio = mysqli_fetch_assoc($resultado_servicio);
            $puntos_servicio = $fila_servicio["puntos_servicio"];

            $datos_puntos_ganados = array(
                "puntos_ganados" => $puntos_servicio,
                "acumulado_puntos" => $puntos_acumulado + $puntos_servicio,
                "fk_id_usuario_cliente" => $id_usuario_cliente,
                "fk_id_servicio_por_zona" => $id_servicio_por_zona
            );
            $datos_puntos_ganados = json_decode(json_encode($datos_puntos_ganados, JSON_FORCE_OBJECT));

            //Registramos los puntos ganados
            $response = PostModel::postWithoutPhoto("puntos_ganados", "punto_ganado", $datos_puntos_ganados);

            //Mandamos a registrar y enviar toda la mensajeria
            PostController::enviar_SMS_llamada_push_email($response_alert, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona, $notificar_contactos);
        } else {
            $files = array();
            //Registramos en la base de datos la alerta
            $comprimir_imagenes = new GlobalPostController();

            foreach ($file as $key => $value) {
                $nombreArchivo = $value['name'];

                if (!empty($nombreArchivo)) {
                    $target_path_nuevo = "src/evidence_alerts/" . $data->fk_id_usuario_cliente_alerta . "/";
                    error_log("Nuevo Path: " . $target_path_nuevo);

                    // verificamos si la carpeta existe de no ser asi la creamos
                    if (!file_exists("./" . $target_path_nuevo)) {
                        if (mkdir("./" . $target_path_nuevo, 0777, true)) {
                            error_log("Exito! Carpeta creada:" . $target_path_nuevo);
                        } else {
                            error_log(" :( No pudo crear:" . $target_path_nuevo);
                        }
                    } else {
                        error_log("Carpeta existente:" . $target_path_nuevo);
                    }
                    // nueva direccion
                    $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

                    // verificamos que el archivo no exista de no ser asi podemos proceder a moverlo a la carpeta
                    if (file_exists("./" . $target_path_nuevo)) {
                        $response = array(
                            "code" => 13
                        );
                        $return = new PostController();
                        $return->fncResponse($response, null);
                    } else {
                        //Pusheamos al arreglo los nombres de los archivos
                        array_push($files, $target_path_nuevo);
                        // Localizacion
                        $location = $target_path_nuevo;
                        // comprimimos la imagen
                        $comprimir_imagenes->compressImage($value['tmp_name'], $location, 60);

                        //Mover el archivo a la carpeta
                        //move_uploaded_file($value['tmp_name'], "./" . $target_path_nuevo);
                    }
                }
            }
            //Registramos en la base de datos la alerta
            $response_alert = PostModel::postAlert($data, $files);

            //consultamos los datos que faltan para registrar los puntos ganados de este usuario
            $conexion = Connection::conexionAlternativa();

            //consultamos el acumulado de puntos de este usuario
            $sentencia_acumulado = "SELECT * FROM puntos_ganados WHERE fk_id_usuario_cliente_punto_ganado = $id_usuario_cliente ORDER BY date_created_punto_ganado DESC LIMIT 1";
            $resultado_acumularo = mysqli_query($conexion, $sentencia_acumulado);
            $puntos_acumulado = 0;

            if ($resultado_acumularo) {
                $fila_aculumado = mysqli_fetch_assoc($resultado_acumularo);
                $puntos_acumulado = $fila_aculumado['acumulado_puntos_punto_ganado'];
            }

            $sentencia_servicio = "SELECT * FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio WHERE sz.id_servicos_por_zona = $id_servicio_por_zona";
            $resultado_servicio = mysqli_query($conexion, $sentencia_servicio);
            $fila_servicio = mysqli_fetch_assoc($resultado_servicio);
            $puntos_servicio = $fila_servicio["puntos_servicio"];

            $datos_puntos_ganados = array(
                "puntos_ganados" => $puntos_servicio,
                "acumulado_puntos" => $puntos_acumulado + $puntos_servicio,
                "fk_id_usuario_cliente" => $id_usuario_cliente,
                "fk_id_servicio_por_zona" => $id_servicio_por_zona
            );
            $datos_puntos_ganados = json_decode(json_encode($datos_puntos_ganados, JSON_FORCE_OBJECT));

            //Registramos los puntos ganados
            $response = PostModel::postWithoutPhoto("puntos_ganados", "punto_ganado", $datos_puntos_ganados);

            //Registramos y enviamos toda la mensajeria
            PostController::enviar_SMS_llamada_push_email($response_alert, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona, $notificar_contactos);
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
    Peticion post para reaccion de agente
    =============================================*/
    static public function postReactionAgentAlert($table, $suffix, $data)
    {
        $datos_agente = GetModel::getDataFilter("usuarios_agentes", "*", "id_usuario_agente", $data->fk_id_usuario_agente);

        $token_dispositivo_agente  = $datos_agente[0]->token_dispositivo;
        $telefono_agente = $datos_agente[0]->telefono_usuario_agente;
        $nombre_agente = $datos_agente[0]->nombre_usuario_agente;

        $conexion = Connection::conexionAlternativa();
        $sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta =uc.id_usuario_cliente WHERE a.id_alerta = $data->fk_id_alerta";
        $resultado = mysqli_query($conexion, $sentencia);
        $fila = mysqli_fetch_assoc($resultado);

        $telefono_cliente = $fila['telefono_usuario_cliente'];
        $nombre_cliente = $fila['nombre_usuario_cliente'];

        //PostController::sendGCM($token_dispositivo_agente, "psuh de prueba"); ------------------------------------ notificacines push por configurar --------------------------------------------

        //Envia el SMS Al usuario también
        $mensaje_cliente = 'El agente CSI ' . $nombre_agente . " te va a asistir, Su teléfono es " . $telefono_agente . " Contáctalo para más información";
        $url = 'http://api.mipgenlinea.com/serviceSMS.php';
        $data_menssage = array(
            "usuario" => "smsFoxUser",
            "password" => "rhjIMEI3*",
            "telefono" => "+57" . $telefono_cliente,
            "mensaje" => $mensaje_cliente,
            "aplicacion" => "SMS Test Unitario",
        );
        $json = json_encode($data_menssage);
        $header = array('Content-Type: application/json');
        $resultado_sms = new  PostController();
        $result = $resultado_sms->CallAPI($url, $json, $header);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);

        //Enviar el SMS al agente, recordandole que debe reaccionar
        $mensaje_agente = 'Hola ' . $nombre_agente . " recuerda que debes asistir al usuario " . $nombre_cliente . " Contáctalo para más información a su teléfono " . $telefono_cliente . " mantén un buen ranking en CSI asistiendo a los usuarios.";
        $url = 'http://api.mipgenlinea.com/serviceSMS.php';
        $data_menssage = array(
            "usuario" => "smsFoxUser",
            "password" => "rhjIMEI3*",
            "telefono" => "+57" . $telefono_agente,
            "mensaje" => $mensaje_agente,
            "aplicacion" => "SMS Test Unitario",
        );
        $json = json_encode($data_menssage);
        $header = array('Content-Type: application/json');
        $resultado_sms = new  PostController();
        $result = $resultado_sms->CallAPI($url, $json, $header);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);

        $response = PostModel::postReactionAgentAlert($table, $suffix, $data);

        $return = new PostController();
        $return->fncResponse($response, $result);
    }

    /*=============================================
    Peticion post para reaccion del cliente
    =============================================*/
    static public function postReactionCustomerAlert($table, $suffix, $data)
    {

        $conexion = Connection::conexionAlternativa();
        $sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta =uc.id_usuario_cliente WHERE a.id_alerta = $data->fk_id_alerta";
        $resultado = mysqli_query($conexion, $sentencia);
        $fila = mysqli_fetch_assoc($resultado);

        $id_usuario_cliente = $fila["id_usuario_cliente"];
        $nombre_cliente = $fila["nombre_usuario_cliente"];
        $telefono_cliente = $fila["telefono_usuario_cliente"];
        $token_dispositivo_cliente = $fila["token_dispositivo"];
        error_log($sentencia);

        //busca los nombres del usuario que está reaccionando
        $sentencia_usuario_reaccion = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$data->fk_id_usuario_cliente";
        $resultado_usuarios_reaccion = mysqli_query($conexion, $sentencia_usuario_reaccion);
        $filas_usuario_reaccion = mysqli_fetch_assoc($resultado_usuarios_reaccion);
        error_log($sentencia_usuario_reaccion);
        error_log("id_usuario" . $fila["id_usuario_cliente"]);
        error_log("id_usuario_reaccion" . $filas_usuario_reaccion["id_usuario_cliente"]);

        if ($fila["id_usuario_cliente"] != $filas_usuario_reaccion["id_usuario_cliente"]) {
            //PostController::sendGCM($token_dispositivo_cliente, "push de prueba"); ------------------------------------ notificacines push por configurar --------------------------------------------
        }

        $response = PostModel::postReactionCustomerAlert($table, $suffix, $data);

        $return = new PostController();
        $return->fncResponse($response, null);
    }

    /*=============================================
    Peticion post para registrar el viaje de un usuario
    =============================================*/
    static public function postTrip($table, $suffix, $data)
    {
        // Ejemplo de uso:
        $fechaOriginal = $data->fecha_estimada_recorrido_viaje;
        $fechaConvertida = PostController::convertirFormatoFecha($fechaOriginal);

        $data->fecha_estimada_recorrido_viaje = $fechaConvertida;

        $response = PostModel::postTrip($table, $suffix, $data);

        $return = new PostController();
        $return->fncResponse($response, null);
    }

    /*=============================================
    Peticion post para registrar las paradas del viaje
    =============================================*/
    static public function postStop($data, $file)
    {
        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        if (empty($file['name'])) {
            $response = PostModel::postStop($data, null);

            $return = new PostController();
            $return->fncResponse($response, null);
        } else {
            $nombreArchivo = $file['name'];

            $target_path_nuevo = "src/images_stops/";
            error_log("Nuevo Path: " . $target_path_nuevo);

            $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

            if (file_exists("./" . $target_path_nuevo)) {
                $response = array(
                    "code" => 13
                );
                $return = new PostController();
                $return->fncResponse($response);
            } else {
                $response = PostModel::postStop($data, $target_path_nuevo);
                move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                $return = new PostController();
                $return->fncResponse($response, null);
            }
        }
    }

    /*=============================================
    METODOS AUXILIARES
    =============================================*/
    function enviar_SMS_llamada_push_email($response, $latitud, $longitud, $id_usuario_cliente, $id_servicio_por_zona, $notificar_contactos)
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
        //$resultado_sms = new  PostController();
        //$result_SMS_cliente = $resultado_sms->CallAPI($url, $json, $header);
        //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result_SMS_cliente\n\r", FILE_APPEND);


        /*========================================================================================================================================
                                                            Enviamos correo al cliente
        ===========================================================================================================================================*/
        //$result_email = enviar_correo_confirmacion("arz.950203@gmail.com", "Andres", "Alarma Incendio");
        //echo $result_email;
        //$result_email = enviar_correo($email, $nombre, $nombre_evento);
        //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "CORREO API enviado a -> $email RESULTADO: $result_email\n\r", FILE_APPEND);

        /*=============================================
        Validamos que se envie mensajes a los numeros de contacto si y solo si el flag enviado por el front es igual a 1
        =============================================*/
        if ($notificar_contactos == 1) {
            //envia a todos los contactos registrados
            $sentencia_contactos = "SELECT * FROM contactos where fk_id_usuario_cliente_contacto=$id_usuario_cliente";
            $resultado_contactos = mysqli_query($conexion, $sentencia_contactos);

            if (!$resultado_contactos) {
                $error2 = "error en SQL 2" . mysqli_error($conexion) . " SQL->" . $sentencia_contactos;
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "ERROR -> " . $error2 . "\n\r", FILE_APPEND);
                return;
            }

            file_put_contents('./log_' . date("j.n.Y") . '.txt', $url . "\n\r", FILE_APPEND);

            /*=============================================
            Consultamos el mensaje para el contacto de emergencia
            =============================================*/
            $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 2";
            $resultado = mysqli_query($conexion, $sentencia);
            $fila = mysqli_fetch_assoc($resultado);
            $mensaje1 = $fila["descripcion_plantilla_mensaje"];
            $mensaje1 = str_replace("%nombre%", $nombre, $mensaje1);
            $mensaje1 = str_replace("%apellido%", $apellido, $mensaje1);
            $mensaje1 = str_replace("%servicio%", $nombre_evento, $mensaje1);

            /*=============================================
            Consultamos el mensaje 2 para el contato de emergencia
            =============================================*/
            $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 3";
            $resultado = mysqli_query($conexion, $sentencia);
            $fila = mysqli_fetch_assoc($resultado);
            $mensaje2 = $fila["descripcion_plantilla_mensaje"];
            $mensaje2 = str_replace("%nombre%", $nombre, $mensaje2);
            $mensaje2 = str_replace("%apellido%", $apellido, $mensaje2);

            while ($fila_contactos = mysqli_fetch_assoc($resultado_contactos)) {
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "entrando al ciclo...\n\r", FILE_APPEND);
                file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SQL -> $sentencia_contactos\n\r", FILE_APPEND);

                $url = 'http://api.mipgenlinea.com/serviceSMS.php';
                $telefonoContacto = $fila_contactos["telefono_contacto"];

                //Primer Mensaje
                $data = array(
                    "usuario" => "smsFoxUser",
                    "password" => "rhjIMEI3*",
                    "telefono" => $telefonoContacto,
                    "mensaje" => $mensaje1,
                    "aplicacion" => "SMS Test Unitario",

                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                //$resultado_sms = new  PostController();
                //$result_sms = $resultado_sms->CallAPI($url, $json, $header);
                //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms . ",\n\r", FILE_APPEND);

                //Segundo Mensaje
                $data = array(
                    "usuario" => "smsFoxUser",
                    "password" => "rhjIMEI3*",
                    "telefono" => $telefonoContacto,
                    "mensaje" => $mensaje2,
                    "aplicacion" => "SMS Test Unitario",

                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                //$resultado_sms = new  PostController();
                //$result_sms2 = $resultado_sms->CallAPI($url, $json, $header);
                //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms2 . ",\n\r", FILE_APPEND);


                //Llamada a los contactos de emergencia
                $url = 'http://api.mipgenlinea.com/serviceIVR.php';
                $urlAudio = "https://csi.mipgenlinea.com/audiosAlerta/xml-message-csi.xml";
                $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => $telefonoContacto, 'mensaje' => $urlAudio, 'fecha' => 'NA', 'aplicacion' => 'CSI LLAMADA'];
                //$resultado_sms2 = new  PostController();
                //$result_sms3 = $resultado_sms2->CallAPIIVR("POST", $url, json_encode($datos));
                //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "IVR API -> TELEFONO:" . $telefonoContacto . " - " . $result_sms3 . ",\n\r", FILE_APPEND);
            }
        }

        /*=============================================
        Consultamos los lideres de zona para enviarles mensaje
        =============================================*/
        $sentencia_listar = "SELECT * FROM agentes_lideres_por_zona alz INNER JOIN usuarios_agentes ua ON ua.id_usuario_agente=alz.fk_id_agente_agente_lider_zona WHERE fk_id_zona_agente_lider_zona=$id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $arrayTokenDevices = array();
        $url = 'http://api.mipgenlinea.com/serviceSMS.php';

        if ($resultado_listado) {
            /*=============================================
            Consultamos el mensaje 2 para el contato de emergencia
            =============================================*/
            $sentencia = "SELECT * FROM `plantillas_mensajes` WHERE tipo_plantilla_mensaje = 4";
            $resultado = mysqli_query($conexion, $sentencia);
            $fila = mysqli_fetch_assoc($resultado);
            $mensaje = $fila["descripcion_plantilla_mensaje"];
            $mensaje = str_replace("%nombre%", $nombre, $mensaje);
            $mensaje = str_replace("%apellido%", $apellido, $mensaje);
            $mensaje = str_replace("%servicio%", $nombre_evento, $mensaje);

            while ($valor = mysqli_fetch_assoc($resultado_listado)) {

                $telefonoLider = $valor["telefono_usuario_agente"];
                $tokenDevice = $valor["token_dispositivo"];
                array_push($arrayTokenDevices, $tokenDevice);

                $data = array(
                    "usuario" => "smsFoxUser",
                    "password" => "rhjIMEI3*",
                    "telefono" => "+57" . $telefonoLider,
                    "mensaje" => $mensaje,
                    "aplicacion" => "SMS Test Unitario",
                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                //$resultado_sms = new  PostController();
                //$result_sms3 = $resultado_sms->CallAPI($url, $json, $header);
                //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> TELEFONO:" . $telefonoLider . " - " . $result_sms3 . ",\n\r", FILE_APPEND);
            }

            if (!empty($arrayTokenDevices)) {
                //PostController::sendGCM($arrayTokenDevices, "CSI Reaccion - Una alerta fue enviada cerca de donde te encuentas"); ------------------------------------ notificacines push por configurar --------------------------------------------
            }
        }

        /*=============================================
        Consultamos los responsables del servicio
        =============================================*/
        $sentencia_listar = "SELECT * FROM responsables_por_servicios rps INNER JOIN responsables_servicios rs ON rs.id_responsable_servicio=rps.fk_id_responsable_servicio_responsable_por_servicio WHERE fk_id_servicio_responsable_por_servicio=$id_servicio";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        if ($resultado_listado) {
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
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                $telefono_responsable = $valor["telefono_responsable_servicio"];

                $url = 'http://api.mipgenlinea.com/serviceSMS.php';
                $data = array(
                    "usuario" => "smsFoxUser",
                    "password" => "rhjIMEI3*",
                    "telefono" => "+57" . $telefono_responsable,
                    "mensaje" => $mensaje,
                    "aplicacion" => "SMS Test Unitario",
                );
                $json = json_encode($data);
                $header = array('Content-Type: application/json');
                //$resultado_sms = new  PostController();
                //$result = $resultado_sms->CallAPI($url, $json, $header);
                //file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " -> TELEFONO:" . $telefonoLider . "SMS API -> $result\n\r", FILE_APPEND);
            }
        }
        /*=============================================
        Consultamos las posiciones de los agentes cercanos con menos de 1 hora para enviarles mensajes
        =============================================*/
        $sentencia_listar = "SELECT * FROM posiciones_agentes pa INNER JOIN usuarios_agentes ua ON ua.id_usuario_agente=pa.fk_id_usuario_agente_posicion_agente";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $suffix = "posicion_agente";
        $arrayTokenDevices = array();

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {

                $distancia = PostController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

                if ($distancia <= 100) {
                    if (round($distancia * 1000) <= 1000) {

                        $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                        $valor["minutos"] = '' . PostController::contarDias(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

                        if ($valor["minutos"] <= 60) {
                            $tokenDevice = $valor["token_dispositivo"];
                            array_push($arrayTokenDevices, $tokenDevice);
                        }
                    }
                }
            }
            if (!empty($arrayTokenDevices)) {
                //PostController::sendGCM($arrayTokenDevices,, "CSI Reaccion - Una alerta fue enviada cerca de donde te encuentas"); ------------------------------------ notificacines push por configurar --------------------------------------------
            }
        }

        $return = new PostController();
        $return->fncResponse($response, "NADA POR AHORA");
    }

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

    public function sendGCM($deviceToken, $body)
    {
        define('API_ACCESS_KEY', 'key=AAAAPBpq6KE:APA91bH4B4CF3XR6gXosqn317XPu02riJ6u7aBNOIYgYak363HaD23k5oii4FvZ90sC1NV19-Mi8xW1aqhRTPnymGXeNhzjXihZJljEywO5h9YDBL5q64l-ty-eWbxNDe5LuF9f0tlrh');
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification_id = $deviceToken;

        $headers = array(
            'Authorization:' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $notification = [
            'title'  => '' . "Atención Emergencia Reportada por CSI" . '',
            'body'   => '' . $body . ''
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

    function convertirFormatoFecha($fecha)
    {
        $fechaObj = new DateTime($fecha);
        return $fechaObj->format('Y-m-d H:i:s');
    }

    function compressImage($source, $destination, $quality)
    {
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);
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
                        'lastId' => $response["lastId"],
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
