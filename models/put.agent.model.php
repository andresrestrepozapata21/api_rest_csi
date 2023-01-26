<?php

require_once "connection.php";

class PutModel
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    static public function putData($table, $data, $nameId, $id)
    {

        /*=============================================
        Actualizamos registros
        =============================================*/

        $set = "";

        foreach ($data as $key => $value) {
            if($key != "id_usuario_agente"){
                $set .= $key . " = '" . $data->$key . "' ,";
            }
        }

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