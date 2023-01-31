<?php
date_default_timezone_set('America/Bogota');

require_once "models/put.resendMessage.model.php";
require_once "models/get.filter.model.php";

class PutController
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    public function putData($table, $suffix, $select, $data)
    {

        /*=============================================
        Validamos que el ID exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, "$select, telefono_$suffix, nombre_$suffix", "email", $data->email);

        if (!empty($response)) {

            $verificationCode = random_int(1000, 9999);
            $telefono = "telefono_$suffix";
            $telefono = $response[0]->$telefono;
            $nombre = "nombre_$suffix";
            $nombre = $response[0]->$nombre;

            $response = PutModel::putData($table, $verificationCode, $select, $response[0]->$select);

            

            /*=============================================
            Enviamos mensaje de texto con el nuevo codigo
            =============================================*/
            $mensaje = "Hola%20agente%20$nombre,%20tu%20codigo%20de%20verificacion%20CSI%20es:%20$verificationCode,%20ingresa%20este%20codigo%20en%20tu%20APP%20CSI%20para%20completar%20tu%20registro.";

            $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
            $datos = ['usuario' => '00486966949', 'password' => 'Juryzu57', 'telefono' => $telefono, 'mensaje' => $mensaje, 'fecha' => 'NA', 'aplicacion' => 'CSI ALERTA'];

            $resultado_sms = new  PutController();
            $result = $resultado_sms -> CallAPI("POST", $url, json_encode($datos));

            
            $return = new PutController();
            $return->fncResponse($response, $result);

            file_put_contents('./log_fecha: ' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result \r\n", FILE_APPEND);

        } else {
            $response = array(
                "code" => 1
            );
            $return = new PutController();
            $return->fncResponse($response, "No se envio mensaje");
        }
    }

    /*=============================================
    METODO PARA LLAMAR EL API SMS DE TWILIO
    Method: POST, PUT, GET etc
    Data: array("param" => "value") ==> index.php?param=value
    =============================================*/
    public function CallAPI($method, $url, $data=false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response, $result)
    {

        if (!empty($response)) {
            if ($response['code'] == 3) {
                
                $json  = array(

                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'detail' => $result
                );
            } else {
                $json = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'detail' => $result
                );
            }
        } else {
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
