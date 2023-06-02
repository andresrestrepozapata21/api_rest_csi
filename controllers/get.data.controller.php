<?php

require_once "models/get.data.model.php";

class GetController{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getData($table, $select){

        $response = GetAllModel::getData($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

     /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getDataPlans($table, $select){

        $response = GetAllModel::getDataPlans($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getDataAlerts($table, $select){

        $response = GetAllModel::getDataAlerts($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response){

        if(!empty($response)){
            $json  = array(

                'status' => 200,
                'total' => count($response),
                'result' => 3,
                'detail' => $response
            );
        }else{
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }

}