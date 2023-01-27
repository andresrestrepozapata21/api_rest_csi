<?php

require_once "models/connection.php";
require_once "models/post.contact.model.php";
require_once "models/get.filter.model.php";

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postRegister($data)
    {
        /*=============================================
        Validamos que el correo No exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter("contactos", "id_contacto, email_contacto, telefono_contacto", "telefono_contacto", $data->telefono_contacto);

        if (empty($response)) {

            $response = PostModel::postData($data);

            $return = new PostController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "code" => 10
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
