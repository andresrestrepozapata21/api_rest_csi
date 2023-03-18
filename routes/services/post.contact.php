<?php

require_once "models/connection.php";
require_once "controllers/post.contact.controller.php";

if (isset($_POST)) {

    if(isset($data->token)){
        
        $validate = Connection::tokenValidate($data->token, $userToken);

        if ($validate == "ok") {
            unset($data->token);
            $response = new PostController();
            $response->postRegister($data);
        }

        if ($validate == "expired") {
            $json = array(
                'status' => 200,
                'result' => 22
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }

        if ($validate == "no-auth") {
            $json = array(
                'status' => 200,
                'result' => 23
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }else{
        $json = array(
            'status' => 200,
            'result' => 24
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
}