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

        $sql = "UPDATE $table SET codigo_verificacion = $data WHERE $nameId = $id";

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
