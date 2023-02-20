<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if (isset($_POST)) {
    if (isset($data->token)) {
        $validate = Connection::tokenValidate($data->token, $userToken);

        if ($validate == "ok") {
            unset($data->token);
            if ($table == "servicios") {
                $response = new PostController();
                $response->postService($data, $file);
            } else if ($table == "planes") {
                $response = new PostController();
                $response->postPlan($data, $file);
            } else if ($table == "servicos_por_zona") {
                $response = new PostController();
                $response->postServicePerZone($data);
            } else if ($table == "alertas") {
                $response = new PostController();
                $response->postAlert($data, $file);
            } else if ($table == "posiciones_clientes" || $table == "posiciones_agentes") {
                $response = new PostController();
                $response->postPosition($table, $suffix, $data);
            } else if ($table == "tipos_usuarios") {
                $response = new PostController();
                $response->postTypeUser($table, $suffix, $data);
            }
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
}
