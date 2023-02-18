<?php

require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

if (isset($_GET)) {

    if (isset($data->token)) {

        $validate = Connection::tokenValidate($data->token, $userToken);

        if ($validate == "ok") {
            unset($data->token);
            if ($table == "planes_comprados") {
                $response = new GetController();
                $response->getDataPlanExistente($table, $data);
            } else if ($table == "zonas") {
                $response = new GetController();
                $response->getLocalZone($table, $data, $id, $select);
            } else {
                $response = new GetController();
                $response->getData($table, $select, $data, $id);
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
}
