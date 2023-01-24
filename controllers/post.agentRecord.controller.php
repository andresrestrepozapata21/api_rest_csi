<?php

require_once "models/connection.php";
require_once "models/post.agentRecord.model.php";

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postRegister($data)
    {
        $crypt = crypt($data['password'], '$2a$07$azybxcags23425sdg23sdfhsd$');
        $data['password'] = $crypt;
        
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
            $json  = array(

                'status' => 200,
                'result' => $response
            );
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
