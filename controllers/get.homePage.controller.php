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
        $nombre = $responseUser[0]->nombre_usuario_cliente;
        $cedula = $responseUser[0]->cedula_usuario_cliente;

        $responsePlan = GetHomePageModel::getPlanUsuario($id);

        if (isset($responsePlan[0]->tipo_plan)) {
            $tipo_plan = $responsePlan[0]->tipo_plan;
            $id_plan = $responsePlan[0]->id_plan;
            $contactos_emergencia_plan = $responsePlan[0]->contactos_emergencia_plan;
            $fecha_vencimiento = date("d-m-Y", strtotime($responsePlan[0]->date_created_plan_comprado . "+ 30 days"));
        } else {
            $tipo_plan = 0;
            $id_plan = 0;
            $contactos_emergencia_plan = 0;
            $fecha_vencimiento = 0;
        }


        error_log("Parametros recibidos " . $latitud . " - " . $longitud);

        $filasZonas = GetController::validarZonaCercana($latitud, $longitud);

        /*=============================================
        Consultamos cuales son las alertas cercanas
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM alertas";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_alerta"], $valor["longitud_alerta"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 200) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["dias"] = '' . GetController::contarDias(date('Y-m-d'), $valor["date_created_alerta"]) . '';

                    $filasAlertas[] = $valor;
                }
            }
        }

        if (empty($filasAlertas)) {
            $filasAlertas["comentario"] = 0;
        }

        /*=============================================
        Consultamos los servicios por zona
        =============================================*/

        if ($filasZonas != 0) {

            $id_zona = $filasZonas[0]["id_zona"];

            $conexion = Connection::conexionAlternativa();
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
            $filasAlertas = 0;
        }

        if (isset($filasServicios["comentario"])) {
            unset($filasServicios["comentario"]);
            $filasServicios = 0;
        }

        $sentencia_listar = "SELECT id_establecimiento, nombre_establecimiento, ruta_imagen_establecimiento, nombre_promocion, descripcion_corta_promocion, url_detalle_establecimiento FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.fk_id_zona_establecimiento = $id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filaslocals = array();

        if ($resultado_listado) {
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                $filaslocals[] = $valor;
            }
        }

        if (empty($filaslocals)) {
            $filaslocals = 0;
        }

        if ($filasZonas == 0) {
            $numero_zonas = 0;
        } else {
            $numero_zonas = count($filasZonas);
        }

        $sentencia_puntos = "SELECT pg.acumulado_puntos_punto_ganado FROM puntos_ganados pg WHERE fk_id_usuario_cliente_punto_ganado = $id ORDER BY acumulado_puntos_punto_ganado DESC LIMIT 1";
        $consulta_puntos = mysqli_query($conexion, $sentencia_puntos);
        $fila_puntos = mysqli_fetch_assoc($consulta_puntos);
        $puntos_usuario = 0;

        if ($fila_puntos["acumulado_puntos_punto_ganado"]) {
            $puntos_usuario = $fila_puntos["acumulado_puntos_punto_ganado"];
        }

        $response = array(
            'id_usuario_cliente' => $id,
            'nombre_usuario_cliente' => $nombre,
            "codigo_QR" => "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2Fpruebas.mipgenlinea.com%2Fvalidador_qr.php?cedula=$cedula",
            'puntos_ganados' => $puntos_usuario,
            'id_plan' => (int) $id_plan,
            'tipo_plan' => $tipo_plan,
            'vencimiento' => $fecha_vencimiento,
            'contactos_emergencia_plan' => (int) $contactos_emergencia_plan,
            'cantidad_zonas' => $numero_zonas,
            'zona' => $filasZonas,
            'alertas_cercanas' => $filasAlertas,
            'servicios_zona' => $filasServicios,
            'establecimientos' => $filaslocals
        );

        $return = new GetController();
        $return->fncResponse($response);
    }
    /*=============================================
    Consultamos en que zona estamos
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
