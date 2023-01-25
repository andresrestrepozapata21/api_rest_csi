<?php

require_once "models/connection.php";
require_once "models/post.customerLogin.model.php";
require_once "models/get.login.model.php";

require_once "vendor/autoload.php";

use Firebase\JWT\JWT;

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postLogin($data)
    {

        /*=============================================
        Validamos que el correo exita en base de datos
        =============================================*/
        $response = GetLoginModel::getDataFilter("usuarios_clientes", "id_usuario_cliente, email, password, token, token_exp", "email", $data->email);

        if (!empty($response)) {
            /*=============================================
            Encriptamos la contraseña
            =============================================*/
            $crypt = crypt($data->password, '$2a$07$azybxcags23425sdg23sdfhsd$');

            if ($response[0]->{"password"} == $crypt) {

                $token = Connection::jwt($response[0]->{"id_usuario_cliente"}, $response[0]->{"email"});

                $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46", "HS256");

                /*=============================================
                Actualizamos la base de datos con el token del usuario
                =============================================*/

                $data = array(
                    "token" => $jwt,
                    "token_exp" => $token["exp"]
                );

                $update = LoginModel::login("usuarios_clientes", $data, $response[0]->{"id_usuario_cliente"}, "id_usuario_cliente");

                if (isset($update['comment']) && $update['comment'] == "The process was successful") {

                    $response[0]->{"token"}  = $jwt;
                    $response[0]->{"token_exp"}  = $token["exp"];

                    $return = new PostController();
                    $return->fncResponse($response);
                }
            } else {
                $response = array(
                    "comment" => "Password incorrect"
                );
                $return = new PostController();
                $return->fncResponse($response);
            }
        } else {
            $response = array(
                "comment" => "The mail does not exist"
            );
            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {
        if (!empty($response)) {

            unset($response[0]->{"password"});

            $json  = array(

                'status' => 200,
                'result' => $response
            );
        } else if (isset($response['comment']) && $response['comment'] == "Password incorrect") {
            $json  = array(

                'status' => 400,
                'result' => $response
            );
        } else if (isset($response['comment']) && $response['comment'] == "The mail does not exist") {
            $json  = array(

                'status' => 400,
                'result' => $response
            );
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
