<?php

require_once "models/put.checkAccount.model.php";
require_once "models/get.filter.model.php";

class PutController
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    public function putData($table, $select, $suffix, $data)
    {

        /*=============================================
        Validamos que el ID exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, $select.",codigo_verificacion", "id_usuario_cliente", $data->id_usuario_cliente);

        if (!empty($response)) {

            if($response[0]->codigo_verificacion == $data->codigo_verificacion){

                $response = PutModel::putData($table, $select, $response[0]->$select, $suffix);

                $return = new PutController();
                $return->fncResponse($response);
            }else{
                $response = array(
                    "code" => 9
                );
                $return = new PutController();
                $return->fncResponse($response);
            }
        } else {
            $response = array(
                "code" => 1
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
