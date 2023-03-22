<?php
date_default_timezone_set('America/Bogota');
require_once "models/get.filter.model.php";

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
        $sentencia_listar = "SELECT id_$suffix, id_dispositivo_$suffix, id_$suffix2, cedula_$suffix2, nombre_$suffix2, apellido_$suffix2, telefono_$suffix2, email, date_created_$suffix,latitud_$suffix, longitud_$suffix, activo_$suffix2 FROM $table p INNER JOIN $table2 u ON u.id_$suffix2=p.fk_id_$suffix2" . "_$suffix WHERE activo_$suffix2 = 1";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);
        $filasPosiciones = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 1000) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["minutos"] = '' . GetController::contarDias(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

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
