<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/post.controller.php";

//Condicional para verificar que sea el metodo POST
if (isset($_POST)) {
    //Valido que en el JSON Resquest venga el token de validacion
    if (isset($data->token)) {
        //Llamo el metodo de tokenValidate que me verifica que el token enviado si exista en base de datos
        $validate = Connection::tokenValidate($data->token, $userToken);
        //Si la respuesta es satisfactoria ingreso a esta estructura condicional
        if ($validate == "ok") {
            //Elimino el toquen del JSON Request
            unset($data->token);
            //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad los nombres de los metodos son dicientes para saber que hace cada uno
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
                $response->postAlert($data, $file, $video);
            } else if ($table == "reacciones_agentes") {
                $response = new PostController();
                $response->postReactionAgentAlert($table, $suffix, $data);
            } else if ($table == "reacciones_cliente_cliente") {
                $response = new PostController();
                $response->postReactionCustomerAlert($table, $suffix, $data);
            } else if ($table == "posiciones_clientes" || $table == "posiciones_agentes" || $table == "posiciones_sin_cobertura") {
                $response = new PostController();
                $response->postPosition($table, $suffix, $data);
            } else if ($table == "tipos_usuarios") {
                $response = new PostController();
                $response->postTypeUser($table, $suffix, $data);
            } else if ($table == "viajes") {
                $response = new PostController();
                $response->postTrip($table, $suffix, $data);
            } else if ($table == "paradas") {
                $response = new PostController();
                $response->postStop($data, $file);
            } else if ($table == "registros_fotograficos_viajes") {
                $response = new PostController();
                $response->postTripPicture($data, $file);
            } else if ($table == "ignoraciones") {
                $response = new PostController();
                $response->postIgnore($table, $suffix, $data);
            }
        }
        //En caso del que el token este vencido, envio un codigo 22
        if ($validate == "expired") {
            $json = array(
                'status' => 200,
                'result' => 22
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
        //En caso del que el token no sea correcto, envio un codigo 23
        if ($validate == "no-auth") {
            $json = array(
                'status' => 200,
                'result' => 23
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }
    //En caso del que no se envio un token, envio un codigo 24
    else {
        $json = array(
            'status' => 200,
            'result' => 24
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
}
