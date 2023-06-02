<?php

require_once "models/connection.php";
require_once "models/post.Login.model.php";
require_once "models/get.filter.model.php";
require_once "models/get.homePage.model.php";


require_once "vendor/autoload.php";

use Firebase\JWT\JWT;

class PostController
{
    /*=============================================
    Peticion post para crear cliente
    =============================================*/
    static public function postLogin($table, $suffix, $data)
    {

        /*=============================================
        Validamos que el correo exita en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, "id_$suffix, email, password, token, token_exp, activo_$suffix", "email", $data->email);

        if (!empty($response)) {
            if ($response[0]->{"activo_$suffix"} == 1) {
                if ($response[0]->{"password"} == $data->password) {

                    $token = Connection::jwt($response[0]->{"id_$suffix"}, $response[0]->{"email"});

                    $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46", "HS256");

                    /*=============================================
                    Actualizamos la base de datos con el token del usuario
                    =============================================*/

                    $data = array(
                        "token" => $jwt,
                        "token_exp" => $token["exp"]
                    );

                    $update = LoginModel::login($table, $data, $response[0]->{"id_$suffix"}, "id_$suffix", $suffix);

                    if (isset($update['code']) && $update['code'] == 3) {

                        $response[0]->{"token"} = $jwt;
                        $response[0]->{"token_exp"} = $token["exp"];

                        $id_ = "id_$suffix";
                        $id = $response[0]->$id_;

                        $responsePlan = GetHomePageModel::getPlanUsuario($id);


                        if (isset($responsePlan[0]->id_plan)) {
                            $id_plan = $responsePlan[0]->id_plan;
                            $response[0]->{"id_plan"} = (int) $id_plan;
                        } else {
                            $response[0]->{"id_plan"} = 0;
                        }

                        $return = new PostController();
                        $return->fncResponse($response, $suffix);
                    }
                } else {
                    $response = array(
                        "id_usuario" => $response[0]->{"id_$suffix"},
                        "activo" => $response[0]->{"activo_$suffix"},
                        "code" => 0
                    );
                    $return = new PostController();
                    $return->fncResponse($response, $suffix);
                }
            } else {
                $response = array(
                    "id_usuario" => $response[0]->{"id_$suffix"},
                    "activo" => $response[0]->{"activo_$suffix"},
                    "code" => 8
                );
                $return = new PostController();
                $return->fncResponse($response, $suffix);
            }
        } else {
            $response = array(
                "id_usuario" => $response[0]->{"id_$suffix"},
                "activo" => "no existe",
                "code" => 1
            );
            $return = new PostController();
            $return->fncResponse($response, $suffix);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response, $suffix)
    {
        if (!empty($response)) {

            unset($response[0]->{"password"});

            if (isset($response['code'])) {
                $json  = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'id_usuario' => (int) $response["id_usuario"],
                    'activo_' . $suffix => (int) $response['activo']
                );
            } else {
                $select = "activo_" . $suffix;
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'activo_' . $suffix => (int) $response[0]->$select,
                    'id_plan' => $response[0]->id_plan,
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
