<?php

require_once "models/connection.php";
require_once "controllers/put.toUpdate.controller.php";

if (isset($data->token)) {

    $validate = Connection::tokenValidate($data->token, $userToken);

    if ($validate == "ok") {
        unset($data->token);
        if ($table == "usuarios_clientes") {
            $response = new PutController();
            $response->putWithImage($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "usuarios_agentes") {
            $response = new PutController();
            $response->putWithImage($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "planes") {
            $response = new PutController();
            $response->putWithImageAux($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "servicios") {
            $response = new PutController();
            $response->putWithImageAux($table, $suffix, $id, $file, $ruta, $data, $select);
        } else {
            $response = new PutController();
            $response->putData($table, $suffix, $select, $data);
        }
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
} else {
    $json = array(
        'status' => 200,
        'result' => 24
    );
    echo json_encode($json, http_response_code($json["status"]));
    return;
}
