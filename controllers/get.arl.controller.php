<?php

require_once "models/get.arl.model.php";

class GetController{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public function getData(){

        $response = GetModel::getData();
        
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