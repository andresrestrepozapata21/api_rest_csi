<?php

require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

if (isset($data->token)) {

    $validate = Connection::tokenValidate($data->token, $userToken);

    if ($validate == "ok") {
        unset($data->token);
        if ($table == "planes_comprados") {
            $response = new GetController();
            $response->getDataPlanExistente($table, $data);
        } else if ($table == "zonas") {
            $response = new GetController();
            $response->getCodeZone($data);
        } else if ($table == "zona") {
            $response = new GetController();
            $response->getZone($data);
        } else if ($table == "establecimientos") {
            $response = new GetController();
            $response->getLocal($data);
        } else if ($table == "servicios_por_zona") {
            $response = new GetController();
            $response->getServicesPerZone($data);
        }  else if ($table == "alertas") {
            $response = new GetController();
            $response->getAlertsCostumer($data);
        } else if ($table == "viajes") {
            $response = new GetController();
            $response->getDataTrip($table, $select, $data, $id);
        } else if ($table == "viaje") {
            $table = "viajes";
            $response = new GetController();
            $response->getTrip($table, $select, $data, $id);
        } else {
            $response = new GetController();
            $response->getData($table, $select, $data, $id);
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
