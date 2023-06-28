<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/delete.controller.php";

//Valido que en el JSON Resquest venga el token de validacion
if (isset($data->token)) {
    //Llamo el metodo de tokenValidate que me verifica que el token enviado si exista en base de datos
    $validate = Connection::tokenValidate($data->token, $userToken);
    //Si la respuesta es satisfactoria ingreso a esta estructura condicional
    if ($validate == "ok") {
        //Elimino el toquen del JSON Request
        unset($data->token);
        //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad
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
        } else if ($table == "registros_fotograficos_viajes") {
            $response = new DeleteController();
            $response->deleteWithImage($table, $id, $nameId, $suffix);
        }
        //este caso es la excepcion cuando hay varios endpoints que son genericos y tienen la misma estructura, se reutiliza el metodo en cuestion
        else {
            $response = new DeleteController();
            $response->deleteData($table, $id, $nameId);
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
