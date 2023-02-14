<?php

date_default_timezone_set('America/Bogota');
header('Access-Control-Allow-Origin: *');

require_once "models/connection.php";
require_once "models/put.toUpdate.model.php";
require_once "models/get.filter.model.php";


//include_once '../cors.php';

class PostController
{
    /*=============================================
    Peticion post para crear foto de perfil del cliente
    =============================================*/
    static public function postRegister($table, $suffix, $id, $file)
    {

        $response = GetModel::getDataFilter($table, "foto_perfil_$suffix", "id_$suffix", $id);

        if (!empty($response)) {

            $direccion = "foto_perfil_$suffix";
            if ($response[0]->$direccion != null) {
                unlink($response[0]->$direccion);
            }

            $target_path = "uploads/";
            $target_path = $target_path . basename($file['name']);

            error_log("Path: " . $target_path);


            $nombreArchivo = $file['name'];
            $id_usuario = $id;

            $target_path_nuevo = "src/perfile_pictures/clients/" . $id_usuario . "/";
            error_log("Nuevo Path: " . $target_path_nuevo);

            if (!file_exists("./" . $target_path_nuevo)) {
                if (mkdir("./" . $target_path_nuevo, 0777, true)) {
                    error_log("Exito! Carpeta creada:" . $target_path_nuevo);
                } else {
                    error_log(" :( No pudo crear:" . $target_path_nuevo);
                }
            } else {
                error_log("Carpeta existente:" . $target_path_nuevo);
            }

            $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

            if (file_exists("./" . $target_path_nuevo)) {
                $response = array(
                    "code" => 13
                );
                $return = new PostController();
                $return->fncResponse($response);
            } else {

                $responsePendiente = PutModel::putDataPerfilePicture($table, $target_path_nuevo, "id_" . $suffix, $id_usuario, $suffix);

                move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                $return = new PostController();
                $return->fncResponse($responsePendiente);
            }
        } else {
            $response = null;

            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticion post para crear foto de perfil del agente
    =============================================*/
    static public function postRegisterAgent($table, $suffix, $id, $file)
    {
        $response = GetModel::getDataFilter($table, "foto_perfil_$suffix", "id_$suffix", $id);

        if (!empty($response)) {

            $direccion = "foto_perfil_$suffix";
            if ($response[0]->$direccion != null) {
                unlink($response[0]->$direccion);
            }

            $target_path = "uploads/";
            $target_path = $target_path . basename($file['name']);

            error_log("Path: " . $target_path);

            $nombreArchivo = $file['name'];
            $id_usuario = $id;

            $target_path_nuevo = "src/perfile_pictures/agents/" . $id_usuario . "/";
            error_log("Nuevo Path: " . $target_path_nuevo);

            if (!file_exists("./" . $target_path_nuevo)) {
                if (mkdir("./" . $target_path_nuevo, 0777, true)) {
                    error_log("Exito! Carpeta creada:" . $target_path_nuevo);
                } else {
                    error_log(" :( No pudo crear:" . $target_path_nuevo);
                }
            } else {
                error_log("Carpeta existente:" . $target_path_nuevo);
            }

            $target_path_nuevo = $target_path_nuevo . $nombreArchivo;

            if (file_exists("./" . $target_path_nuevo)) {
                $response = array(
                    "code" => 13
                );
                $return = new PostController();
                $return->fncResponse($response);
            } else {

                $responsePendiente = PutModel::putDataPerfilePicture($table, $target_path_nuevo, "id_" . $suffix, $id_usuario, $suffix);

                move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                $return = new PostController();
                $return->fncResponse($responsePendiente);
            }
        } else {
            $response = null;

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
