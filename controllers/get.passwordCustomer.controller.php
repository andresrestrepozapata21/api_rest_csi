<?php

require_once "models/get.filter.model.php";

class GetController
{

    /*=============================================
    Peticiones GET contraseña
    =============================================*/
    public function getData($data)
    {

        /*=============================================
        Llamamos al modelo para consultar el EMAIL
        =============================================*/
        $response = GetModel::getDataFilter("usuarios_clientes", "password", "email", $data->email);

        if (!empty($response)) {

            $return = new GetController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "code" => 1
            );
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
            if(isset($response['code'])){
                $json  = array(
                    'status' => 200,
                    'result' => $response['code']
                );
            }else{
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
