<?php

require_once "models/connection.php";
require_once "models/post.model.php";

class PostController
{
    /*=============================================
    Peticion POST para registrar algo
    =============================================*/
    static public function postRegister($data)
    {
        $response = PostModel::postData($data);

        $return = new PostController();
        $return->fncResponse($response);
    }

    /*=============================================
    Peticion post para servicio
    =============================================*/
    static public function postService($data, $file)
    {

        /*=============================================
        Cargamos la imagen del servicio
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        $nombreArchivo = $file['name'];

        $target_path_nuevo = "src/images_services/";
        error_log("Nuevo Path: " . $target_path_nuevo);

        $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

        if (file_exists("./" . $target_path_nuevo)) {
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        } else {

            $response = PostModel::postService($data, $target_path_nuevo);
            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticion post para planes
    =============================================*/
    static public function postPlan($data, $file)
    {

        /*=============================================
        Cargamos la imagen del plan
        =============================================*/
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);

        $nombreArchivo = $file['name'];

        $target_path_nuevo = "src/images_plans/";
        error_log("Nuevo Path: " . $target_path_nuevo);

        $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

        if (file_exists("./" . $target_path_nuevo)) {
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        } else {

            $response = PostModel::postPlan($data, $target_path_nuevo);
            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

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
