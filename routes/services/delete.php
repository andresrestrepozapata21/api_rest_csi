<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";


if (isset($data->token)) {

    $validate = Connection::tokenValidate($data->token, $userToken);

    if ($validate == "ok") {
        unset($data->token);
        if ($table == "usuarios_clientes") {
            $response = new DeleteController();
            $response->deleteUser($table, $id, $nameId, $suffix);
        } else if ($table == "usuarios_agentes") {
            $response = new DeleteController();
            $response->deleteUser($table, $id, $nameId, $suffix);
        } else if ($table == "planes") {
            $response = new DeleteController();
            $response->deleteWithImage($table, $id, $nameId, $suffix);
        } else if ($table == "servicios") {
            $response = new DeleteController();
            $response->deleteWithImage($table, $id, $nameId, $suffix);
        } else{
            $response = new DeleteController();
            $response->deleteData($table, $id, $nameId);
        }
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
