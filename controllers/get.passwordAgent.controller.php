<?php
//requiro los scripts que necesito
require_once "models/get.filter.model.php";

//nombro la clase
class GetController
{
    /*=============================================
    Metodo para obtener la contrasena del usuario
    =============================================*/
    public function getData($data)
    {
        /*=============================================
        Llamamos al modelo para consultar po medio del EMAIL seleccionando la contrasena como retorno del Agente
         =============================================*/
        $response = GetModel::getDataFilter("usuarios_agentes", "password", "email", $data->email);

        //si el resultado es diferente de vacio
        if (!empty($response)) {
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
