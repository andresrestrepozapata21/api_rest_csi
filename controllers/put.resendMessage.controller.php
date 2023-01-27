<?php

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

            $return = new PutController();
            $return->fncResponse($response);

            /*=============================================
            Enviamos mensaje de texto con el nuevo codigo
            =============================================*/
            $mensaje = "Hola agente $nombre, tu código de verificacion CSI es: $verificationCode, ingresa este codigo en tu APP CSI para completar tu registro";

            $url = 'http://api.mipgenlinea.com/serviceSMS.php';
            $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => '+57' . $telefono, 'mensaje' => $mensaje, 'fecha' => 'NA', 'aplicacion' => 'CSI ALERTA'];

            $resultado_sms = new  PutController();
            $result = $resultado_sms -> CallAPI("POST", $url, json_encode($datos));
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result \r\n", FILE_APPEND);

        } else {
            $response = array(
                "code" => 1
            );
            $return = new PutController();
            $return->fncResponse($response);
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
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if ($response['code'] == 3) {
                
                $json  = array(

                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            } else {
                $json = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'method' => $_SERVER['REQUEST_METHOD']
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
