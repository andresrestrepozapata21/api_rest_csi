<?php

require_once "models/connection.php";
require_once "models/post.typeUserrecord.model.php";

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postRegister($data)
    {
        $response = PostModel::postData($data);

        $return = new PostController();
        $return->fncResponse($response);
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
