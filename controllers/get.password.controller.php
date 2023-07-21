<?php
//configuro la zona horaria
date_default_timezone_set('America/Bogota');
//requiro los scripts que necesito
require_once "models/get.filter.model.php";

//nombro la clase
class GetController
{
    /*=============================================
    Metodo para obtener la contrasena del usuario
    =============================================*/
    public function getData($table, $data)
    {
        /*=============================================
        Llamamos al modelo para consultar po medio del EMAIL seleccionando la contrasena y el telefono como retorno Del Cliente
        =============================================*/
        $response = GetModel::getDataFilter($table, "password, telefono_usuario_cliente", "email", $data->email);

        //si el resultado es diferente de vacio
        if (!empty($response)) {

            $password = $response[0]->password;
            $telefono = $response[0]->telefono_usuario_cliente;
            $mensaje = "La contraseña asociada al correo ingresado es: $password";

            //rutina para realizar el consumo a nuestra api csi para realizar envio de SMS
            $url = 'http://api.mipgenlinea.com/serviceSMS.php';
            $data = array(
                "usuario" => "smsFoxUser",
                "password" => "rhjIMEI3*",
                "telefono" => "+57" . $telefono,
                "mensaje" => $mensaje,
                "aplicacion" => "SMS Recuperar Contrasena",
            );
            $json = json_encode($data);
            $header = array('Content-Type: application/json');
            $resultado_sms = new  GetController();
            $result_SMS_cliente = $resultado_sms->CallAPI($url, $json, $header);
            file_put_contents('./log_recoverPassword_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result_SMS_cliente\n\r", FILE_APPEND);

            $response = array(
                "SMS_result" => $result_SMS_cliente
            );
            //retorno la respuesta con la contrasena
            $return = new GetController();
            $return->fncResponse($response);
        } else {
            //retorno codigo 1 que significa que el email no existe
            $response = array(
                "code" => 1
            );
            $return = new GetController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
                    METODOS AUXILIARES
    =============================================*/
    //metodo para llamar a la api de los sms
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
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if (isset($response['code'])) {
                $json  = array(
                    'status' => 200,
                    'result' => $response['code']
                );
            } else {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'detail' => $response
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
