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
        $response = GetModel::getDataFilter($table, "$select, telefono_$suffix, nombre_$suffix", "id_$suffix", $data->$select);

        if (!empty($response)) {

            $verificationCode = random_int(100000, 999999);
            $telefono = "telefono_$suffix";
            $telefono = $response[0]->$telefono;
            $nombre = "nombre_$suffix";
            $nombre = $response[0]->$nombre;

            $response = PutModel::putData($table, $verificationCode, $select, $response[0]->$select);

            /*=============================================
            Enviamos mensaje de texto con el nuevo codigo
            =============================================*/
            $mensaje = "Hola $nombre, tu codigo de verificacion CSI es: $verificationCode, ingresa este codigo en tu APP CSI para completar tu registro.";

            $url = 'http://api.mipgenlinea.com/serviceSMS2.php';
            //$datos = ['usuario' => '00486966949', 'password' => 'Juryzu57', 'telefono' => $telefono, 'mensaje' => $mensaje, 'fecha' => 'NA', 'aplicacion' => 'CSI ALERTA'];

            $data = array(
                "usuario" => "00486966949",
                "password" => "Juryzu57",
                "telefono" => $telefono,
                "mensaje" => $mensaje,
                "aplicacion" => "SMS Test Unitario"
            );
            $json = json_encode($data);
            $header = array('Content-Type: application/json');

            $resultado_sms = new  PutController();
            $result = $resultado_sms->CallAPI($url, $json, $header);

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
    METODO PARA LLAMAR EL API serviceSMS2
    =============================================*/
    function CallAPI($url, $json, $header)
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
                    'detail' => json_decode($result)
                );
            } else {
                $json = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'method' => $_SERVER['REQUEST_METHOD'],
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
