<?php

use PostController as GlobalPostController;

date_default_timezone_set('America/Bogota');
header('Access-Control-Allow-Origin: *');

require_once "models/connection.php";
require_once "models/post.uploadDocuments.model.php";
require_once "models/put.updatePending.model.php";

//include_once '../cors.php';

class PostController
{
    
    /*=============================================
    Peticion post para crear documento del cliente
    =============================================*/
    static public function postRegister($table, $suffix, $id, $file1, $file2, $file3)
    {
        $response1 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file1);
        $response2 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file2);
        $response3 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file3);

        if($response1["code"] == 3 && $response2["code"] == 3 && $response3["code"] == 3){
            $response = array(
                "code" => 3
            );
            $return = new PostController();
            $return->fncResponse($response);
        }else{
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticion post para crear documento del agente
    =============================================*/
    static public function postRegisterAgent($table, $suffix, $id, $file1, $file2, $file3)
    {
        $response1 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file1);
        $response2 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file2);
        $response3 = PostController::cargarDocumentoCliente($table, $suffix, $id, $file3);

        if($response1["code"] == 3 && $response2["code"] == 3 && $response3["code"] == 3){
            $response = array(
                "code" => 3
            );
            $return = new PostController();
            $return->fncResponse($response);
        }else{
            $response = array(
                "code" => 13
            );
            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Funcion para cargar un documento
    =============================================*/
    public function cargarDocumentoCliente($table, $suffix, $id, $file){
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);


        $nombreArchivo = $file['name'];
        $id_usuario = $id;

        //$id_usuario = '99';

        $target_path_nuevo = "documentos_clientes/" . $id_usuario . "/";
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
            
            return $response;
        } else {

            $response = PostModel::postData("documentos", $suffix, $id_usuario, $target_path_nuevo);

            $responsePendiente = PutPendingModel::putData($table, "id_" . $suffix, $id_usuario, $suffix);

            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

            return $responsePendiente;
        }
    }

    /*=============================================
    Funcion para cargar un documento del Agente
    =============================================*/
    public function cargarDocumentoAgente($table, $suffix, $id, $file){
        $target_path = "uploads/";
        $target_path = $target_path . basename($file['name']);

        error_log("Path: " . $target_path);


        $nombreArchivo = $file['name'];
        $id_usuario = $id;

        //$id_usuario = '99';

        $target_path_nuevo = "documentos_agentes/" . $id_usuario . "/";
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
            return $response;
        } else {

            $response = PostModel::postData("documentos", $suffix, $id_usuario, $target_path_nuevo);

            $responsePendiente = PutPendingModel::putData($table, "id_" . $suffix, $id_usuario, $suffix);

            move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

            return $responsePendiente;
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
