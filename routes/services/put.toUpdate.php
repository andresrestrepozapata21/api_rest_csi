<?php

//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/put.toUpdate.controller.php";

//Valido que en el JSON Resquest venga el token de validacion
if (isset($data->token)) {
    //Llamo el metodo de tokenValidate que me verifica que el token enviado si exista en base de datos
    $validate = Connection::tokenValidate($data->token, $userToken);
    //Si la respuesta es satisfactoria ingreso a esta estructura condicional
    if ($validate == "ok") {
        //Elimino el toquen del JSON Request
        unset($data->token);
        //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad este endpoint es para actualziar los usuarios con foto, pero tambien hay metodos para actualiar otras cosas, poner cuidado de donde vienen y cual mentodo se esta llamando
        if ($table == "usuarios_clientes") {
            $response = new PutController();
            //Validamos si viene o no un archivo file en el FormData() Request, esto con la finalidad de definir a que metodo llamar, y asi asegurar si modificar el cliente con una foto o son foto de perfil
            if (empty($file['name'])) {
                $response->putData($table, $suffix, $select, $data);
            } else {
                $response->putWithImage($table, $suffix, $id, $file, $ruta, $data, $select);
            }
        } else if ($table == "usuarios_agentes") {
            $response = new PutController();
            $response->putWithImage($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "planes") {
            $response = new PutController();
            $response->putWithImageAux($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "servicios") {
            $response = new PutController();
            $response->putWithImageAux($table, $suffix, $id, $file, $ruta, $data, $select);
        } else if ($table == "paradas") {
            $response = new PutController();
            $response->putStop($table, $suffix, $id, $file, $ruta, $data, $select);
        } else {
            $response = new PutController();
            $response->putData($table, $suffix, $select, $data);
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
