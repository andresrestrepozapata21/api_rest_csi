<?php

require_once "models/put.customer.model.php";
require_once "models/get.filter.model.php";

class PutController
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    public function putData($data)
    {

        /*=============================================
        Validamos que el ID exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter("usuarios_clientes", "id_usuario_cliente", "id_usuario_cliente", $data->id_usuario_cliente);

        if (!empty($response)) {

            $response = PutModel::putData("usuarios_clientes", $data, "id_usuario_cliente", $data->id_usuario_cliente);

            $return = new PutController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "code" => 4
            );
            $return = new PutController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if ($response['code'] == 3) {
                
                $json  = array(

                    'status' => 200,
                    'result' => $response["code"],
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
        echo json_encode($json, http_response_code($json["status"]));
    }
}
