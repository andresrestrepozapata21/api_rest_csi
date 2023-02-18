<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if (isset($_POST)) {

    if ($table == "servicios") {
        if (isset($data->token)) {
            $validate = Connection::tokenValidate($data->token, $userToken);

            if ($validate == "ok") {
                unset($data->token);
                $response = new PostController();
                $response->postService($data, $file);
            }

            if ($validate == "expired") {
                $json = array(
                    'status' => 303,
                    'result' => 'Error: El token a expirado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }

            if ($validate == "no-auth") {
                $json = array(
                    'status' => 400,
                    'result' => 'Error: El usuario no esta autorizado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }
        } else {
            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización Requerida'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    } else if ($table == "planes") {

        if (isset($data->token)) {
            $validate = Connection::tokenValidate($data->token, $userToken);

            if ($validate == "ok") {
                unset($data->token);
                $response = new PostController();
                $response->postPlan($data, $file);
            }

            if ($validate == "expired") {
                $json = array(
                    'status' => 303,
                    'result' => 'Error: El token a expirado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }

            if ($validate == "no-auth") {
                $json = array(
                    'status' => 400,
                    'result' => 'Error: El usuario no esta autorizado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }
        } else {
            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización Requerida'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    } else if ($table == "servicos_por_zona") {

        if (isset($data->token)) {
            $validate = Connection::tokenValidate($data->token, $userToken);

            if ($validate == "ok") {
                unset($data->token);
                $response = new PostController();
                $response->postServicePerZone($data);
            }

            if ($validate == "expired") {
                $json = array(
                    'status' => 303,
                    'result' => 'Error: El token a expirado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }

            if ($validate == "no-auth") {
                $json = array(
                    'status' => 400,
                    'result' => 'Error: El usuario no esta autorizado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }
        } else {
            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización Requerida'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    } else if ($table == "alertas") {

        if (isset($data->token)) {
            $validate = Connection::tokenValidate($data->token, $userToken);

            if ($validate == "ok") {
                unset($data->token);
                $response = new PostController();
                $response->postAlert($data, $file);
            }

            if ($validate == "expired") {
                $json = array(
                    'status' => 303,
                    'result' => 'Error: El token a expirado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }

            if ($validate == "no-auth") {
                $json = array(
                    'status' => 400,
                    'result' => 'Error: El usuario no esta autorizado'
                );
                echo json_encode($json, http_response_code($json["status"]));
                return;
            }
        } else {
            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización Requerida'
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    } else {
        $response = new PostController();
        $response->postRegister($data);
    }
}
