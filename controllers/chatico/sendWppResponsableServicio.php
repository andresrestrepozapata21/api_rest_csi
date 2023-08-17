<?php

date_default_timezone_set('America/Bogota');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    $user = $data->user;
    $password = $data->password;
    $first_name = $data->first_name;
    $last_name = $data->last_name;
    $name = $data->name;
    $phone = $data->phone;
    $email = $data->email;
    $gender = $data->gender;
    $service = $data->service;
    $nameVictim = $data->nameVictim;
    $phoneVictim = $data->phoneVictim;
    $emailVictim = $data->emailVictim;

    if ($user == 'smsFoxUser' && $password == 'rhjIMEI3*') {
        /*=============================================
        PETICION POST PARA CREAR EL USUARIO
        =============================================*/
        $url_created = 'https://app.chatiico.com.co/api/subscriber/create';
        $data_created = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "name" => $name,
            "phone" => $phone,
            "email" => $email,
            "gender" => $gender,
        );
        $json = json_encode($data_created);
        $headers = array(
            'accept: application/json',
            'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR',
            'Content-Type: application/json'
        );
        $result_created = postChaticoWpp($url_created, $json, $headers);
        file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result_created \r\n", FILE_APPEND);
        $json_result_created = json_decode($result_created);
        //guardo el User NS para usarlo en la siguiente peticion
        $user_ns = $json_result_created->data->user_ns;

        /*=============================================
        PETICION PUT PARA MODIFICAR EL CAMP 2
        =============================================*/
        $url_put = "https://app.chatiico.com.co/api/subscriber/set-user-fields-by-name";
        $data = array(
            "user_ns" => $user_ns,
            "data" => [
                [
                    "name" => "servicioResponsable",
                    "var_ns" => "f49432v422163",
                    "var_type" => "text",
                    "value" => $service
                ],
                [
                    "name" => "nombreCliente",
                    "var_ns" => "f49432v422165",
                    "var_type" => "text",
                    "value" => $nameVictim
                ],
                [
                    "name" => "telefonoCliente",
                    "var_ns" => "f49432v422167",
                    "var_type" => "text",
                    "value" => $phoneVictim
                ],
                [
                    "name" => "emailCliente",
                    "var_ns" => "f49432v422169",
                    "var_type" => "text",
                    "value" => $emailVictim
                ]
            ]
        ); // Reemplaza con tus datos
        $data_json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_put);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR';
        $headers[] = 'Content-Length: ' . strlen($data_json);
        // Si necesitas añadir otras cabeceras, como una API key, puedes hacerlo aquí
        // $headers[] = 'X-API-KEY: tu_api_key';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);
            file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result \r\n", FILE_APPEND);
        } else {
            // $result contiene la respuesta del servidor
            // puedes procesarla de la forma que necesites
            //var_dump($result);
            file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result \r\n", FILE_APPEND);
        }
        curl_close($ch);

        /*=============================================
        PETICION POST PARA ENVIAR EL MENSAJE
        =============================================*/
        $url_send = 'https://app.chatiico.com.co/api/subscriber/send-sub-flow';
        $data_send = array(
            "user_ns" => $user_ns,
            "sub_flow_ns" => "f49432s273095"
        );
        $json = json_encode($data_send);
        $headers = array(
            'accept: application/json',
            'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR',
            'Content-Type: application/json'
        );
        $result_send = postChaticoWpp($url_send, $json, $headers);
        file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result_send \r\n", FILE_APPEND);

        //envio la respuesta
        http_response_code(200);
        echo json_encode($result_send);
    } else {
        //envio el error
        http_response_code(401);
        echo json_encode('no autorizado');
    }
}

/*=============================================
METODO PARA LLAMAR EL API Chatico
=============================================*/
function postChaticoWpp($url, $json, $header)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return $response;
}
