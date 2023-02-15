<?php

require_once "models/connection.php";
require_once "controllers/put.toUpdate.controller.php";

if (isset($token)) {

    $validate = Connection::tokenValidate($token, $userToken);

    if ($validate == "ok") {
        unset($data->token);
        $response = new PutController();
        $response->putImage($table, $suffix, $id, $file, $ruta);
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
} else {
    $json = array(
        'status' => 400,
        'result' => 'Error: Autorización Requerida'
    );
    echo json_encode($json, http_response_code($json["status"]));
    return;
}
