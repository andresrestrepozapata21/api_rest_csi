<?php
date_default_timezone_set('America/Bogota');
require_once "models/get.filter.model.php";
require_once "models/connection.php";


class GetController
{

    /*=============================================
    Peticiones GET
    =============================================*/
    public function getData($table, $select, $data, $id)
    {

        $response = GetModel::getDataFilter($table, $select, $id, $data->$id);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET
    =============================================*/
    public function getDataTrip($table, $select, $data, $id)
    {

        $response = GetModel::getDataFilterTrips($table, $select, $id, $data->$id);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET pra traer todos los datos del viaje
    =============================================*/
    public function getTrip($table, $select, $data, $id)
    {
        $conexion = Connection::conexionAlternativa();

        $response_viaje = GetModel::getTrip($table, $select, $id, $data->$id);

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)
        =============================================*/
        $sql_paradas = "SELECT * FROM paradas WHERE fk_id_viaje_parada=" . $data->$id;
        $consulta_parada = mysqli_query($conexion, $sql_paradas);

        $filas_paradas = array();

        if ($consulta_parada) {
            while ($valor = mysqli_fetch_assoc($consulta_parada)) {
                $filas_paradas[] = $valor;
            }
        }

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)
        =============================================*/
        $sql_fotos = "SELECT * FROM registros_fotograficos_viajes WHERE fk_id_viaje_registro_fotografico_viaje =" . $data->$id;
        $consulta_fotos = mysqli_query($conexion, $sql_fotos);

        $filas_fotos = array();

        if ($consulta_fotos) {
            while ($valor = mysqli_fetch_assoc($consulta_fotos)) {
                $filas_fotos[] = $valor;
            }
        }

        $response = array(
            'informacion_viaje' => $response_viaje,
            'paradas_viaje' => $filas_paradas,
            'fotos_viaje' => $filas_fotos,
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET para los planes existentes
    =============================================*/
    public function getDataPlanExistente($table, $data)
    {

        $response = GetModel::getDataFilterPlanExistente($table, $data);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET para traer los establecimientos de la zona
    =============================================*/
    public function getCodeZone($data)
    {
        $codigo_ingresado = $data->codigo_zona;
        $conexion = Connection::conexionAlternativa();

        $sentencia_listar = "SELECT * FROM codigos_zonas WHERE fk_id_zona_codigo_zona = $data->id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $codigo_valido = false;
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
            if ($codigo_ingresado == $valor["codigo_zona"]) {
                $fecha_fin_codigo_zona = new DateTime($valor["vencimiento_codigo_zona"]);
                $timestamp = $fecha_fin_codigo_zona->format("U");
                $time = time();
                if ($time < $timestamp) {

                    $fecha = date('Y-m-d H:i:s');
                    $sentencia_plan_codigo_zona = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,19,$data->id_usuario_cliente,'$fecha')";
                    $consulta_plan_codigo_zona = mysqli_query($conexion, $sentencia_plan_codigo_zona);

                    $codigo_valido = true;
                }
            }
        }

        if ($codigo_valido) {
            $response = array(
                'codigo' => 3
            );

            $return = new GetController();
            $return->fncResponse($response);
        } else {
            $response = array(
                'code' => 19
            );
            $return = new GetController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticiones GET para los planes existentes
    =============================================*/
    public function getClosePosition($table, $suffix, $table2, $suffix2, $data)
    {
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        /*=============================================
        Consultamos las posiciones cercanas
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        //$sentencia_listar = "SELECT id_$suffix, id_dispositivo_$suffix, id_$suffix2, cedula_$suffix2, nombre_$suffix2, apellido_$suffix2, telefono_$suffix2, email, date_created_$suffix,latitud_$suffix, longitud_$suffix, activo_$suffix2 FROM $table p INNER JOIN $table2 u ON u.id_$suffix2=p.fk_id_$suffix2" . "_$suffix WHERE activo_$suffix2 = 1";
        $sentencia_listar = "SELECT id_$suffix, id_dispositivo_$suffix, id_$suffix2, cedula_$suffix2, nombre_$suffix2, apellido_$suffix2, telefono_$suffix2, email, foto_perfil_$suffix2, lastlogin_$suffix2 ,date_created_$suffix, latitud_$suffix, longitud_$suffix, activo_$suffix2 FROM $table p INNER JOIN $table2 u ON u.id_$suffix2=p.fk_id_$suffix2" . "_$suffix WHERE activo_$suffix2 = 1";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);
        $filasPosiciones = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 1000) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["minutos"] = '' . GetController::contarMinutos(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

                    if ($valor["minutos"] <= 60) {
                        $filasPosiciones[] = $valor;
                    }
                }
            }
        }

        $response = array(
            'posiciones_cercanas' => $filasPosiciones
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET obtener establecimiento
    =============================================*/
    public function getLocal($data)
    {

        /*=============================================
        Consultamos en que zona estamos
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT id_establecimiento, nombre_establecimiento, ruta_imagen_establecimiento, nombre_promocion, descripcion_promocion, puntos_promocion FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.id_establecimiento = $data->id_establecimiento LIMIT 1";

        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $response = mysqli_fetch_assoc($resultado_listado);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET obtener servicios por zona
    =============================================*/
    public function getServicesPerZone($data)
    {

        /*=============================================
        Consultamos en que zona estamos
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT id_servicos_por_zona, id_servicio, descripcion_servicio, ruta_imagen_servicio, puntos_servicio, color_sombra_servicio FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $data->id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasServicios = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
            $filasServicios[] = $valor;
        }

        $response = $filasServicios;

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET obtener alertas por usuario cliente
    =============================================*/
    public function getAlertsCostumer($data)
    {
        /*=============================================
        Consultamos las alertas que tienes la foranea del usuario
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM alertas WHERE fk_id_usuario_cliente_alerta = $data->fk_id_usuario_cliente_alerta ORDER BY date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertasCostumer = array();
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
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
            $filasAlertasCostumer[] = $valor;
        }

        $response = $filasAlertasCostumer;

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET obtener alertas por usuario cliente
    =============================================*/
    public function getAlertsZone($data)
    {
        //Guardamos el id de la zona
        $id_zona = $data->id_zona;

        /*=============================================
        Consultamos las alertas que tienes la foranea del usuario
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT id_alerta, latitud_alerta, longitud_alerta, tipo_evento_alerta, estado_alerta, comentario_alerta, fk_id_usuario_cliente_alerta, fk_id_servicio_por_zona_alerta, date_created_alerta, date_update_alerta, ruta1_imagen_alerta, ruta2_imagen_alerta, ruta3_imagen_alerta FROM alertas a INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta=sz.id_servicos_por_zona INNER JOIN zonas z ON sz.fk_id_zona_servicos_por_zona=z.id_zona WHERE z.id_zona=$id_zona ORDER BY a.date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertasCostumer = array();
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
            //Calculamos cuantos dias lleva activa la alerta
            $valor["dias"] = '' . GetController::contarDias(date('Y-m-d'), $valor["date_created_alerta"]) . '';

            if ($valor["dias"] == 0) {
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
                $filasAlertasCostumer[] = $valor;
            }
        }

        if (empty($filasAlertasCostumer)) {
            $response = array(
                'code' => 26
            );
        }else{
            $response = $filasAlertasCostumer;
        }

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Consultamos si es usuario activo o no
    =============================================*/
    public function getValideCostumer($table, $data)
    {
        //conexion a la base de datos
        $conexion = Connection::conexionAlternativa();

        $sql = "SELECT * FROM $table WHERE telefono_usuario_cliente='$data->phone_number' AND activo_usuario_cliente = 1";
        $query = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($query) > 0) {
            $datos = mysqli_fetch_assoc($query);
            $id_usuario_cliente = $datos["id_usuario_cliente"];
            $token_vigente = $datos["token"];
            /*=============================================
            Armo el arreglo que se convertira en JSON en el detail de la respuesta
            =============================================*/
            $response = array(
                "flag_be" => 1,
                'id_usuario_cliente' => (int) $id_usuario_cliente,
                'token' => $token_vigente
            );
        } else {
            $response = array(
                "flag_be" => 0
            );
        }
        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET obtener servicios por zona
    =============================================*/
    public function getZone($data)
    {
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        $filasZonas = GetController::validarZonaCercana($latitud, $longitud);

        /*=============================================
        Consultamos los servicios por zona
        =============================================*/
        if (!isset($filasZonas["comentario"])) {

            $id_zona = $filasZonas[0]["id_zona"];

            $conexion = Connection::conexionAlternativa();
            $sentencia_listar = "SELECT id_servicos_por_zona, id_servicio, descripcion_servicio, ruta_imagen_servicio, puntos_servicio, color_sombra_servicio FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $id_zona";
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

        $response = array(
            'zona' => $filasZonas,
            'servicios_zona' => $filasServicios,
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET
    =============================================*/
    public function getPointsUser($table, $data)
    {
        $conexion = Connection::conexionAlternativa();
        /*=============================================
        Consultamos los puntos acumulados del usuario
        =============================================*/
        $sentencia_puntos = "SELECT pg.acumulado_puntos_punto_ganado FROM $table pg WHERE fk_id_usuario_cliente_punto_ganado = $data->fk_id_usuario_cliente_punto_ganado ORDER BY acumulado_puntos_punto_ganado DESC LIMIT 1";
        $consulta_puntos = mysqli_query($conexion, $sentencia_puntos);
        $fila_puntos = mysqli_fetch_assoc($consulta_puntos);
        $puntos_usuario = 0;

        if (isset($fila_puntos["acumulado_puntos_punto_ganado"])) {
            $puntos_usuario = (int) $fila_puntos["acumulado_puntos_punto_ganado"];
        }

        /*=============================================
        Armo el arreglo que se convertira en JSON en el detail de la respuesta
        =============================================*/
        $response = array(
            'puntos_ganados' => $puntos_usuario
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
                METODOS AUXILIARES
    =============================================*/
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

    function contarMinutos($fecha1, $fecha2)
    {
        $startTimeStamp = strtotime($fecha1);
        $endTimeStamp = strtotime($fecha2);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 60;  // 60 seconds in one minute
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;
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
    Consultamos en que zona estamos
    =============================================*/
    function validarZonaCercana($latitud, $longitud)
    {
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM zonas";
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

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if (isset($response["code"])) {
                $json  = array(
                    'status' => 200,
                    'result' => $response["code"],
                );
            } else {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'detail' => $response
                );
            }
        } else {
            $json = array(
                'status' => 200,
                'result' => 4,
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
