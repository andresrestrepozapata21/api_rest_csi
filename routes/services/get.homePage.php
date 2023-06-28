<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/get.homePage.controller.php";

//Valido que en el JSON Resquest venga el token de validacion
if (isset($data->token)) {
    //Llamo el metodo de tokenValidate que me verifica que el token enviado si exista en base de datos
    $validate = Connection::tokenValidate($data->token, $userToken);
    //Si la respuesta es satisfactoria ingreso a esta estructura condicional
    if ($validate == "ok") {
        //Elimino el toquen del JSON Request
        unset($data->token);
        //Llamo el controlador donde esta el metodo que cordina la obtencion de los datos del master
        $response = new GetControllerMaster();
        $response->getData($data);
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
