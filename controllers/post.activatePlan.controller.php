<?php

require_once "models/connection.php";
require_once "models/post.activatePlan.model.php";
require_once "models/get.consultarCodigoActivacion.model.php";
require_once "models/put.deactivateCode.model.php";

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postRegister($table, $data)
    {

        /*=============================================
        Llamamos al modelo para consultar si el codigo es valido
        =============================================*/
        $response = GetCodeModel::getDataFilter($data);

        if (!empty($response)) {

            $id_usuario_cliente = $data->id_usuario_cliente;
            /*=============================================
            Llamamos al modelo para consultar si el usuario ya tiene un plan activo
            =============================================*/
            $responseUsuarioPlan = GetCodeModel::getPlanUsuario($id_usuario_cliente);

            if(empty($responseUsuarioPlan)){

                $id_codigo = $response[0]->id_codigo_activacion;

                $responsePOST = PostModel::postData($table, $data);
                
                if($responsePOST['code']==3){
                    PutModel::putData("codigos_activacion", "id_codigo_activacion", $id_codigo);
                }
                $return = new PostController();
                $return->fncResponse($responsePOST);
            }else{
                $response = array(
                    "code" => 15
                );
                $return = new PostController();
                $return->fncResponse($response);
            }
        } else {
            $response = array(
                "code" => 14
            );
            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {
        if (!empty($response)) {
            if($response['code'] == 3){
                $json  = array(
                    
                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            }else{
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
