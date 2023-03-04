<?php

require_once "models/delete.model.php";
require_once "models/get.filter.model.php";

class DeleteController{

    /*=============================================
    Peticion Delete para eliminar datos
    =============================================*/
    static public function deleteData($table, $id, $nameId){

        $response = DeleteModel::deleteData($table, $id, $nameId);

        $return = new DeleteController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Peticion Delete para eliminar usuario
    =============================================*/
    static public function deleteUser($table, $id, $nameId, $suffix){

        $response = GetModel::getDataFilter($table, "foto_perfil_$suffix", "id_$suffix", $id);

        if (!empty($response)) {
            $direccion = "foto_perfil_$suffix";
            if ($response[0]->$direccion != null) {
                unlink($response[0]->$direccion);
            }
            $response = DeleteModel::deleteData($table, $id, $nameId);

            $return = new DeleteController();
            $return -> fncResponse($response);
        }else{
            $response = null;

            $return = new DeleteController();
            $return->fncResponse($response);
        }
        
    }

    /*=============================================
    Peticion Delete para eliminar registros con imagenes
    =============================================*/
    static public function deleteWithImage($table, $id, $nameId, $suffix){

        $response = GetModel::getDataFilter($table, "ruta_imagen_$suffix", "id_$suffix", $id);

        if (!empty($response)) {
            $direccion = "ruta_imagen_$suffix";
            if ($response[0]->$direccion != null) {
                unlink($response[0]->$direccion);
            }
            $response = DeleteModel::deleteData($table, $id, $nameId);

            $return = new DeleteController();
            $return -> fncResponse($response);
        }else{
            $response = null;

            $return = new DeleteController();
            $return->fncResponse($response);
        }
        
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response){

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