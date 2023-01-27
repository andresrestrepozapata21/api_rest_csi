<?php
date_default_timezone_set('America/Bogota');

require_once "models/connection.php";
require_once "models/post.record.model.php";
require_once "models/get.filter.model.php";

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postRegister($table, $suffix, $data)
    {
       

        /*=============================================
        Validamos que el correo No exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, "id_$suffix, email", "email", $data->email);

        if (empty($response)) {
            //$crypt = crypt($data->password, '$2a$07$azybxcags23425sdg23sdfhsd$');
            //$data->password = $crypt;

            $response = PostModel::postData($table, $suffix, $data);

            $return = new PostController();
            $return->fncResponse($response);

            /*=============================================
            Obtenemos el codigo de verificaion de base de datos
            =============================================*/
            $verification_code = GetModel::getDataFilter($table, "codigo_verificacion", "email", $data->email);

            $codigo = $verification_code[0]->codigo_verificacion;
            $telefono = "telefono_$suffix";
            $telefono = $data->$telefono;
            $nombre = "nombre_$suffix";
            $nombre = $data->$nombre;

            $mensaje = "Hola agente $nombre, tu código de verificacion CSI es: $codigo, ingresa este codigo en tu APP CSI para completar tu registro";

            $url = 'http://api.mipgenlinea.com/serviceSMS.php';
            $datos = ['usuario' => 'smsFoxUser', 'password' => 'rhjIMEI3*', 'telefono' => '+57' . $telefono, 'mensaje' => $mensaje, 'fecha' => 'NA', 'aplicacion' => 'CSI ALERTA'];

            $resultado_sms = new  PostController();
            $resultado_sms -> CallAPI("POST", $url, json_encode($datos));
            file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API ->", FILE_APPEND);

        } else {
            $response = array(
                "code" => 2
            );
            $return = new PostController();
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
            if($response['code'] == 3){
                $json  = array(
                    
                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            }else{
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
