<?php

require_once "models/connection.php";
require_once "controllers/post.contact.controller.php";

if (isset($_POST)) {

    if(isset($data->token)){
        
        $validate = Connection::tokenValidate($data->token, "usuarios_clientes");

        if ($validate == "ok") {
            unset($data->token);
            $response = new PostController();
            $response->postRegister($data);
        }

        if ($validate == "expired") {
            $json = array(
                'status' => 303,
                'result' => 'Error: El token a expirado'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }

        if ($validate == "no-auth") {
            $json = array(
                'status' => 400,
                'result' => 'Error: El usuario no esta autorizado'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }else{
        $json = array(
            'status' => 400,
            'result' => 'Error: Autorización Requerida'
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
}