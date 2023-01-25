<?php

require_once "models/connection.php";
require_once "models/post.customerRecord.model.php";
require_once "models/get.login.model.php";

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
        $response = GetLoginModel::getDataFilter("usuarios_agentes", "id_usuario_agente, email", "email", $data->email);

        if (empty($response)) {

            $crypt = crypt($data->password, '$2a$07$azybxcags23425sdg23sdfhsd$');
            $data->password = $crypt;

            $response = PostModel::postData($data);

            $return = new PostController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "comment" => "This email is already in use"
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
