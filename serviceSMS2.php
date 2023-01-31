<?php

date_default_timezone_set('America/Bogota');
include_once 'conexion.php';

require __DIR__ . '/vendor/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    $usuario = $data->usuario;
    $password = $data->password;
    $telefono = $data->telefono;
    $mensaje = $data->mensaje;
    $aplicacion = $data->aplicacion;
    $type = 1;

    if ($usuario == '00486966949' && $password == 'Juryzu57') {
        //Enviar Mensaje
        $api_key = "77ccdef01145863b7bf40252afde5da7023f621b";


        $url = 'https://api.cellvoz.com/v2/sms/single';
        $datos = '?apiKey=' . $api_key . '&account=' . $usuario . '&password=' . $password . '&message=' . $mensaje . '&number=' . '57' . $telefono . '&type=' . $type;

        $resultado_sms = CallAPI("GET", $url . $datos);
        //file_put_contents('log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $resultado_sms \r\n", FILE_APPEND);

        //print $message->sid;
        http_response_code(201);
        echo json_encode($resultado_sms);

        //Guardar en BD
        $fecha = date('Y-m-d h:i:s');
        $sentencia_insertar = "insert into mensajes (telefono,mensaje,fecha,respuesta,aplicacion) values ('$telefono','$mensaje','$fecha','$resultado_sms','$aplicacion')";
        $resultado_insertar = mysqli_query($conexion, $sentencia_insertar);
    } else {
        http_response_code(401);
        echo json_encode('no autorizado');
    }
}

/*=============================================
    METODO PARA LLAMAR EL API SMS DE TWILIO
    Method: POST, PUT, GET etc
    Data: array("param" => "value") ==> index.php?param=value
    =============================================*/
function CallAPI($method, $url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}
