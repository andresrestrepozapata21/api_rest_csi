<?php
/*=============================================
PETICION POST PARA CREAR EL USUARIO
=============================================*/
$url_created = 'https://app.chatiico.com.co/api/subscriber/create';
$data = array(
    "first_name" => "andres",
    "last_name" => "Restrepo",
    "name" => "Andres Restrepo",
    "phone" => "+573186337855",
    "email" => 'arz.950203@gmail.com',
    "gender" => 'male',
);
$json = json_encode($data);
$headers = array(
    'accept: application/json',
    'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR',
    'Content-Type: application/json'
);
$result_created = postChaticoWpp($url_created, $json, $headers);
file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result_created \r\n", FILE_APPEND);

$data_create = json_decode($result_created);

$user_ns = $data_create->data->user_ns;

echo $user_ns . "<br>";

/*=============================================
PETICION PUT PARA MODIFICAR EL CAMP 2
=============================================*/
$url_put = "https://app.chatiico.com.co/api/subscriber/set-user-fields-by-name";

$data = array(
    "user_ns" => $user_ns,
    "data" => [
        [
            "name" => "camp2",
            "var_ns" => "f49432v387748",
            "var_type" => "text",
            "value" => "456123"
        ]
    ]
); // Reemplaza con tus datos
$data_json = json_encode($data);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url_put);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

// Si es necesario autenticarse
// curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR';
$headers[] = 'Content-Length: ' . strlen($data_json);
// Si necesitas añadir otras cabeceras, como una API key, puedes hacerlo aquí
// $headers[] = 'X-API-KEY: tu_api_key';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // $result contiene la respuesta del servidor
    // puedes procesarla de la forma que necesites
    var_dump($result);
}

curl_close($ch);

/*=============================================
PETICION POST PARA ENVIAR EL MENSAJE
=============================================*/
$url_send = 'https://app.chatiico.com.co/api/subscriber/send-sub-flow';
$data = array(
    "user_ns" => $user_ns,
    "sub_flow_ns" => "f49432s248408"
);
$json = json_encode($data);
$headers = array(
    'accept: application/json',
    'Authorization: Bearer FKa1Ct0y93j1HObbmOsNyUth83scHBMzEs4S7LbgghqFo0quPJluL3MZKVPR',
    'Content-Type: application/json'
);
$result_send = postChaticoWpp($url_send, $json, $headers);
file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "API -> $result_send \r\n", FILE_APPEND);

http_response_code(200);
echo json_encode($result_send);

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