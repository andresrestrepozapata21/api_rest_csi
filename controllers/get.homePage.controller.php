<?php
date_default_timezone_set('America/Bogota');

require_once "models/get.homePage.model.php";
require_once "models/connection.php";


class GetController
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getData($data)
    {
        $conexion = Connection::conexionAlternativa();
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        $responseUser = GetHomePageModel::getUsuario($data->id_usuario_cliente);

        if (empty($responseUser)) {
            return GetController::fncResponse(null);
        }

        $id = $responseUser[0]->id_usuario_cliente;
        $email = $responseUser[0]->email;

        /*=============================================
        Valido que el usuario que ya esta registrado y activo sea un usuario beneficiario
        =============================================*/
        $sentencia_beneficiario = "SELECT * FROM usuario_beneficiarios WHERE correo_usuario_beneficiario = '$email'";
        $resultado_beneficiario = mysqli_query($conexion, $sentencia_beneficiario);

        if (mysqli_num_rows($resultado_beneficiario) > 0) {
            $fila_beneficiario = mysqli_fetch_assoc($resultado_beneficiario);
            $id_plan = $fila_beneficiario["fk_id_plan_usuario_beneficiario"];
            $fecha_compra = $fila_beneficiario["fecha_compra_plan"];

            $sentencia_aux = "SELECT * FROM planes_comprados WHERE fk_id_usuario_cliente_plan_comprado = $id AND activo_plan_comprado = 1";
            $resultado_aux = mysqli_query($conexion, $sentencia_aux);

            if (mysqli_num_rows($resultado_aux) == 0) {
                $sentencia_insertar_plan_beneficiario = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,$id_plan,$id,'$fecha_compra')";
                $resultado_insertar_plan_beneficiario = mysqli_query($conexion, $sentencia_insertar_plan_beneficiario);
            }
        }

        /*=============================================
        Continuo con la ejecucion del Master
        =============================================*/
        $nombre = $responseUser[0]->nombre_usuario_cliente;
        $cedula = $responseUser[0]->cedula_usuario_cliente;
        $popup_inicio = $responseUser[0]->presentacion_inicial_popup_usuario_cliente;
        $popup_anuncio = $responseUser[0]->anuncio_popup_usuario_cliente;
        $url_cargar_info = $responseUser[0]->url_cargar_info_usuario_cliente;

        $responsePlan = GetHomePageModel::getPlanUsuario($id);

        $fecha_compra_plan = "";

        if (isset($responsePlan[0]->tipo_plan)) {
            $tipo_plan = $responsePlan[0]->tipo_plan;
            $id_plan = $responsePlan[0]->id_plan;
            $codigo_plan = $responsePlan[0]->codigo_plan;
            $contactos_emergencia_plan = $responsePlan[0]->contactos_emergencia_plan;
            $fecha_compra_plan = $responsePlan[0]->date_created_plan_comprado;
            $fecha_vencimiento = date("d-m-Y", strtotime($responsePlan[0]->date_created_plan_comprado . "+ 30 days"));
        } else {
            $tipo_plan = 0;
            $id_plan = 0;
            $codigo_plan = 0;
            $contactos_emergencia_plan = 0;
            $fecha_vencimiento = 0;
        }


        error_log("Parametros recibidos " . $latitud . " - " . $longitud);

        $filasZonas = GetController::validarZonaCercana($latitud, $longitud);

        /*=============================================
        Consultamos cuales son las alertas cercanas
        =============================================*/
        $sentencia_listar = "SELECT * FROM alertas ORDER BY date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_alerta"], $valor["longitud_alerta"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 200) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["dias"] = '' . GetController::contarDias(date('Y-m-d'), $valor["date_created_alerta"]) . '';

                    $valor["imagenes"] = array();

                    if ($valor["ruta1_imagen_alerta"]) {
                        array_push($valor["imagenes"], $valor["ruta1_imagen_alerta"]);
                        if ($valor["ruta2_imagen_alerta"]) {
                            array_push($valor["imagenes"], $valor["ruta2_imagen_alerta"]);
                            if ($valor["ruta3_imagen_alerta"]) {
                                array_push($valor["imagenes"], $valor["ruta3_imagen_alerta"]);
                            }
                        }
                    }

                    unset($valor["ruta1_imagen_alerta"]);
                    unset($valor["ruta2_imagen_alerta"]);
                    unset($valor["ruta3_imagen_alerta"]);
                    $filasAlertas[] = $valor;
                }
            }
        }

        /*=============================================
        Consultamos los servicios por zona
        =============================================*/
        if ($filasZonas != 0) {
            unset($filasZonas[0]["codigo_zona"]);
            $id_zona = $filasZonas[0]["id_zona"];

            $sentencia_listar = "SELECT * FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $id_zona";
            $resultado_listado = mysqli_query($conexion, $sentencia_listar);

            $filasServicios = array();

            if ($resultado_listado) {
                while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                    $filasServicios[] = $valor;
                }
            } else {
                $filasServicios["comentario"] = 0;
            }
        } else {
            $filasServicios["comentario"] = 0;
        }

        if (isset($filasAlertas["comentario"])) {
            unset($filasAlertas["comentario"]);
            $filasAlertas = [];
        }

        if (isset($filasServicios["comentario"])) {
            unset($filasServicios["comentario"]);
            $filasServicios = [];
        }

        $sentencia_listar = "SELECT id_establecimiento, nombre_establecimiento, ruta_imagen_establecimiento, nombre_promocion, descripcion_corta_promocion, url_detalle_establecimiento FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.fk_id_zona_establecimiento = $id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filaslocals = array();

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                $filaslocals[] = $valor;
            }
        }

        if ($filasZonas == 0) {
            $numero_zonas = 0;
            $filasZonas = [];
        } else {
            $numero_zonas = count($filasZonas);
        }

        /*=============================================
        Consultamos los puntos acumulados del usuario
        =============================================*/
        $sentencia_puntos = "SELECT pg.acumulado_puntos_punto_ganado FROM puntos_ganados pg WHERE fk_id_usuario_cliente_punto_ganado = $id ORDER BY acumulado_puntos_punto_ganado DESC LIMIT 1";
        $consulta_puntos = mysqli_query($conexion, $sentencia_puntos);
        $fila_puntos = mysqli_fetch_assoc($consulta_puntos);
        $puntos_usuario = 0;

        if (isset($fila_puntos["acumulado_puntos_punto_ganado"])) {
            $puntos_usuario = (int) $fila_puntos["acumulado_puntos_punto_ganado"];
        }

        /*=============================================
        Consultamos los productos fisico que el usuario puede comprar
        =============================================*/
        $sentencia_fisicos = "SELECT * FROM productos_fisicos";
        $consulta_fisicos = mysqli_query($conexion, $sentencia_fisicos);

        $filasFisicos = array();

        if ($consulta_fisicos) {
            while ($valor = mysqli_fetch_assoc($consulta_fisicos)) {
                $filasFisicos[] = $valor;
            }
        }

        /*=============================================
        Consultamos la informacion de los cuadrantes de la zona
        =============================================*/
        $sentencia_cuadrantes = "SELECT * FROM cuadrantes_por_zonas WHERE fk_id_zona_cuadrante_por_zona=$id_zona";
        $consulta_cuadrantes = mysqli_query($conexion, $sentencia_cuadrantes);

        $filasCuadrantes = array();

        if ($consulta_cuadrantes) {
            while ($valor = mysqli_fetch_assoc($consulta_cuadrantes)) {
                $filasCuadrantes[] = $valor;
            }
        }

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)
        =============================================*/
        $sentencia_contactos_seguridad = "SELECT * FROM contactos_seguridad WHERE fk_id_zona_contacto_seguridad=$id_zona";
        $consulta_contactos_seguridad = mysqli_query($conexion, $sentencia_contactos_seguridad);

        $filasContactosSeguridad = array();

        if ($consulta_contactos_seguridad) {
            while ($valor = mysqli_fetch_assoc($consulta_contactos_seguridad)) {
                $filasContactosSeguridad[] = $valor;
            }
        }

        /*=============================================
        Consultamos cuantos Agentes activos hay en CSI
        =============================================*/
        //$sentencia_agentes_activos = "SELECT count(*) as cantidad_agentes_activos FROM usuarios_agentes WHERE activo_usuario_agente=1";
        //$consulta_agentes_activos = mysqli_query($conexion, $sentencia_agentes_activos);
        //$dato_agentes_activos = mysqli_fetch_assoc($consulta_agentes_activos);


        $dato_agentes_activos = rand(800, 999);

        /*=============================================
        Consultamos cuantos Agentes activos hay en CSI
        =============================================*/
        //$sentencia_clientes_activos = "SELECT count(*) as cantidad_clientes_activos FROM usuarios_clientes WHERE activo_usuario_cliente=1";
        //$consulta_clientes_activos = mysqli_query($conexion, $sentencia_clientes_activos);
        //$dato_clientes_activos = mysqli_fetch_assoc($consulta_clientes_activos);

        $dato_clientes_activos = rand(100, 250);

        /*=============================================
        Vamos a generar un saludo aleatorio
        =============================================*/
        $horaActual = date('H:i');

        if ($horaActual >= '06:00' && $horaActual <= '10:30') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 1 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } elseif ($horaActual > '10:30' && $horaActual <= '12:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 4 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } elseif ($horaActual > '12:00' && $horaActual <= '18:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 2 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } elseif ($horaActual > '18:00' && $horaActual <= '00:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 3 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } else if ($horaActual > '00:00' && $horaActual < '06:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 0 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        }

        $conexion->close();

        $saludo = str_replace("[NOMBRE]", $nombre, $saludo);

        /*=============================================
        Armo el arreglo que se convertira en JSON en el detail de la respuesta
        =============================================*/
        $response = array(
            'id_usuario_cliente' => $id,
            'nombre_usuario_cliente' => $nombre,
            'saludo' => $saludo,
            'url_cargar_info' => $url_cargar_info,
            'modal_inicio' => $popup_inicio,
            'modal_anuncio' => $popup_anuncio,
            "codigo_QR" => "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2Fpruebas.mipgenlinea.com%2Fvalidador_qr.php?cedula=$cedula",
            'puntos_ganados' => $puntos_usuario,
            'id_plan' => (int) $id_plan,
            'tipo_plan' => $tipo_plan,
            'codigo_plan' => (int) $codigo_plan,
            'fecha_compra_plan' => $fecha_compra_plan,
            'vencimiento' => $fecha_vencimiento,
            'contactos_emergencia_plan' => (int) $contactos_emergencia_plan,
            'cantidad_zonas' => $numero_zonas,
            //'cantidad_agentes_activos' => $dato_agentes_activos["cantidad_agentes_activos"],
            //'cantidad_clientes_activos' => $dato_clientes_activos["cantidad_clientes_activos"],
            'cantidad_agentes_activos' => $dato_agentes_activos,
            'cantidad_clientes_activos' => $dato_clientes_activos,
            'zona' => $filasZonas,
            'alertas_cercanas' => $filasAlertas,
            'servicios_zona' => $filasServicios,
            'establecimientos' => $filaslocals,
            'productos_fisicos' => $filasFisicos,
            'cuadrantes' => $filasCuadrantes,
            "contactos_seguridad_zona" => $filasContactosSeguridad
        );

        $return = new GetController();
        $return->fncResponse($response);
    }
    /*=============================================
    METODOS UTILES PARA REGRESAR EL MASTER
    =============================================*/
    function validarZonaCercana($latitud, $longitud)
    {
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "select * from zonas";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasZonas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_zona"], $valor["longitud_zona"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= $valor["radio_zona"]) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';

                    $filasZonas[] = $valor;
                }
            }
        }

        if (empty($filasZonas)) {
            return  $filasZonas["comentario"] = 0;
        }

        return $filasZonas;
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
        $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if ($response["id_plan"] != 0) {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'id_plan' => (int) $response["id_plan"],
                    'detail' => $response
                );
            } else {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'id_plan' => (int) $response["id_plan"],
                    'detail' => $response
                );
            }
        } else {
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
