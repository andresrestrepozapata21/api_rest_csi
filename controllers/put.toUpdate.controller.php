<?php

require_once "models/put.toUpdate.model.php";
require_once "models/get.filter.model.php";

class PutController
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    public function putData($table, $suffix, $select, $data)
    {

        /*=============================================
        Validamos que el ID exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, $select, $select, $data->$select);

        if (!empty($response)) {

            $response = PutModel::putData($table, $data, $select, $data->$select, $suffix);

            $return = new PutController();
            $return->fncResponse($response);
        } else {
            $response = array(
                "code" => 4
            );
            $return = new PutController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticiones PUT con imagen
    =============================================*/
    public function putWithImage($table, $suffix, $id, $file, $ruta, $data, $select)
    {
        $response = GetModel::getDataFilter($table, "email, foto_perfil_$suffix", "id_$suffix", $id);

        if (!empty($response)) {
            /*=============================================
            Validamos que el correo No exista en base de datos
            =============================================*/
            $response_email = GetModel::getDataFilter($table, "id_$suffix, email", "email", $data->email);

            if (empty($response_email)) {
                $direccion = "foto_perfil_$suffix";
                if ($response[0]->$direccion != null) {
                    unlink($response[0]->$direccion);
                }

                $target_path = "uploads/";
                $target_path = $target_path . basename($file['name']);

                error_log("Path: " . $target_path);

                $nombreArchivo = $file['name'];
                $id_plan = $id;

                $target_path_nuevo = $ruta;
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
                    $return = new PutController();
                    $return->fncResponse($response);
                } else {

                    $response = PutModel::putData($table, $data, $select, $data->$select, $suffix);
                    $responseImagen = PutModel::putImage($table, $target_path_nuevo, "id_" . $suffix, $id_plan, $suffix, "foto_perfil_$suffix");

                    move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                    $return = new PutController();
                    $return->fncResponse($responseImagen);
                }
            } else {
                $response = array(
                    "code" => 2
                );
                $return = new PutController();
                $return->fncResponse($response, "No se envio mensaje");
            }
        } else {
            $response = null;

            $return = new PutController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Peticiones PUT con imagen
    =============================================*/
    public function putWithImageAux($table, $suffix, $id, $file, $ruta, $data, $select)
    {
        $response = GetModel::getDataFilter($table, "ruta_imagen_$suffix", "id_$suffix", $id);
        
        if (!empty($response)) {
            $direccion = "ruta_imagen_$suffix";
            if ($response[0]->$direccion != null) {
                unlink($response[0]->$direccion);
            }

            $target_path = "uploads/";
            $target_path = $target_path . basename($file['name']);

            error_log("Path: " . $target_path);

            $nombreArchivo = $file['name'];
            $id_plan = $id;

            $target_path_nuevo = $ruta;
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
                $return = new PutController();
                $return->fncResponse($response);
            } else {

                $response = PutModel::putData($table, $data, $select, $data->$select, $suffix);
                $responseImagen = PutModel::putImage($table, $target_path_nuevo, "id_" . $suffix, $id_plan, $suffix, $direccion);

                move_uploaded_file($file['tmp_name'], "./" . $target_path_nuevo);

                $return = new PutController();
                $return->fncResponse($responseImagen);
            }
        } else {
            $response = null;

            $return = new PutController();
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
