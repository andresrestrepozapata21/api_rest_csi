<?php

require_once "models/get.filter.model.php";

class GetController
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getData($table, $select, $data, $id)
    {

        $response = GetModel::getDataFilter($table, $select, $id, $data->$id);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticiones GET sin filtro
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
                $sentencia_listar = "SELECT * FROM `establecimientos` WHERE fk_id_zona_establecimiento = $data->id_zona";
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
                    'total' => count($response),
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
