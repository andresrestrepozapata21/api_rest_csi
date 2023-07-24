<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

//Valido que en el JSON Resquest venga el token de validacion
if (isset($data->token)) {
    //Llamo el metodo de tokenValidate que me verifica que el token enviado si exista en base de datos
    $validate = Connection::tokenValidate($data->token, $userToken);
    //Si la respuesta es satisfactoria ingreso a esta estructura condicional
    if ($validate == "ok") {
        //Elimino el toquen del JSON Request
        unset($data->token);
        //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad
        if ($table == "planes_comprados") {
            $response = new GetController();
            $response->getDataPlanExistente($table, $data);
        } else if ($table == "planes_comprados_por_usuario") {
            $response = new GetController();
            $response->expirationPlanUser($data);
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
        } else if ($table == "alertas") {
            $response = new GetController();
            $response->getAlertsCostumer($data);
        } else if ($table == "alertasZona") {
            $response = new GetController();
            $response->getAlertsZone($data);
        } else if ($table == "viajes") {
            $response = new GetController();
            $response->getDataTrip($table, $select, $data, $id);
        } else if ($table == "viaje") {
            $table = "viajes";
            $response = new GetController();
            $response->getTrip($table, $select, $data, $id);
        } else if ($table == "puntos_ganados") {
            $response = new GetController();
            $response->getPointsUser($table, $data);
        }  else if ($table == "documentos") {
            $response = new GetController();
            $response->validateDocumentsCustomer($data);
        }
        //este caso es la excepcion cuando hay varios endpoints que son genericos y tienen la misma estructura, se reutiliza el metodo en cuestion
        else {
            $response = new GetController();
            $response->getData($table, $select, $data, $id);
        }
    }
    //En caso del que el token este vencido, envio un codigo 22
    if ($validate == "expired") {
        $json = array(
            'status' => 200,
            'result' => 22
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
    //En caso del que el token no sea correcto, envio un codigo 23
    if ($validate == "no-auth") {
        $json = array(
            'status' => 200,
            'result' => 23
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
}
//En caso del que no se envio un token, envio un codigo 24
else {
    $json = array(
        'status' => 200,
        'result' => 24
    );
    echo json_encode($json, http_response_code($json["status"]));
    return;
}
