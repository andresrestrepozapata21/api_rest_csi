<?php

date_default_timezone_set('America/Bogota');

require_once "connection.php";

class PutModel
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    static public function putData($table, $data, $nameId, $id, $suffix)
    {

        /*=============================================
        Actualizamos registros
        =============================================*/

        $set = "";

        foreach ($data as $key => $value) {
            if($key != $nameId){
                $set .= $key . " = '" . $data->$key . "' ,";
            }
        }

        $fecha = date('Y-m-d H:i:s');
        $set .= " date_update_" . $suffix . " = '" . $fecha . "',"; 
        $set = substr($set, 0, -1);

        $sql = "UPDATE $table SET $set WHERE $nameId = $id";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        try {
            $stmt->execute();
            $response = array(
                'code' => 3
            );

            return $response;
        } catch (PDOException $e) {
            $response = array(
                'code' => 7
            );

            return $response;
        }
    }

    /*=============================================
    Peticiones PUT para foto de perfil
    =============================================*/
    static public function putDataPerfilePicture($table, $data, $nameId, $id, $suffix)
    {

        /*=============================================
        Actualizamos registros
        =============================================*/

        $set = "";

        $set .= " foto_perfil_" . $suffix . " = '" . $data . "',"; 

        $fecha = date('Y-m-d H:i:s');
        $set .= " date_update_" . $suffix . " = '" . $fecha . "',"; 
        $set = substr($set, 0, -1);

        $sql = "UPDATE $table SET $set WHERE $nameId = $id";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        try {
            $stmt->execute();
            $response = array(
                'code' => 3
            );

            return $response;
        } catch (PDOException $e) {
            $response = array(
                'code' => 7
            );

            return $response;
        }
    }

    /*=============================================
    Peticiones PUT para Imagenes
    =============================================*/
    static public function putImage($table, $data, $nameId, $id, $suffix, $columnImg)
    {

        /*=============================================
        Actualizamos registros
        =============================================*/

        $set = "";

        $set .= $columnImg . " = '" . $data . "',"; 

        $fecha = date('Y-m-d H:i:s');
        $set .= " date_update_" . $suffix . " = '" . $fecha . "',"; 
        $set = substr($set, 0, -1);

        $sql = "UPDATE $table SET $set WHERE $nameId = $id";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        try {
            $stmt->execute();
            $response = array(
                'code' => 3
            );

            return $response;
        } catch (PDOException $e) {
            $response = array(
                'code' => 7
            );

            return $response;
        }
    }
}