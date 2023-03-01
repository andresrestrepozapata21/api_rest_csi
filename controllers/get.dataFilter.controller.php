<?php

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
    public function getLocalZone($table, $data, $id, $select)
    {

        $response_Zone = GetModel::getDataFilter($table, $select, $id, $data->id_zona);

        if (!empty($response_Zone)) {
            if ($response_Zone[0]->codigo_zona == $data->codigo_zona) {


                /*=============================================
                Consultamos en que zona estamos
                =============================================*/
                $conexion = Connection::conexionAlternativa();
                $sentencia_listar = "SELECT * FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.fk_id_zona_establecimiento = $data->id_zona";
                $resultado_listado = mysqli_query($conexion, $sentencia_listar);

                $filaslocals = array();
                
                while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                    $filaslocals[] = $valor;
                }

                if (empty($filaslocals)) {
                    $filaslocals["code"] = 20;
                }

                $response = array(
                    'zona' => $response_Zone[0],
                    'establecimientos' => $filaslocals
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
        } else {
            $response = null;
            $return = new GetController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticiones GET para los planes existentes
    =============================================*/
    public function getClosePosition($table, $suffix, $data)
    {
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        /*=============================================
        Consultamos las posiciones cercanas
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM $table";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasPosiciones = array();
        
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 1000) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["minutos"] = '' . GetController::contarDias(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

                    if($valor["minutos"] <= 60){
                        $filasPosiciones[] = $valor;
                    }
                }
            }
        }

        if (empty($filasPosiciones)) {
            $filasPosiciones = 0;
        }

        $response = array(
            'posiciones_cercanas' => $filasPosiciones
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
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
