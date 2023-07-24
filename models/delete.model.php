<?php

require_once "models/connection.php";
require_once "models/get.filter.model.php";

class DeleteModel
{
    /*=============================================
    Peticion delete para eliminar datos
    =============================================*/
    static public function deleteData($table, $id, $nameId)
    {
        /*=============================================
        Validar el ID
        =============================================*/
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id);

        if (empty($response)) {
            return null;
        }

        /*=============================================
        Eliminar registros
        =============================================*/

        $sql = "DELETE FROM $table WHERE $nameId = :$nameId";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $response = array(
                'code' => 3
            );

            return $response;
        } catch (PDOException $e) {
            $response = array(
                'code' => 12
            );

            return $response;
        }
    }
    /*=============================================
    Peticion delete para eliminar datos
    =============================================*/
    static public function desactiveUser($table, $id, $nameId, $suffix)
    {
        /*=============================================
        Validar el ID
        =============================================*/
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id);

        if (empty($response)) {
            return null;
        }

        /*=============================================
        Desactivamos el usuario $suffix (cliente o agente)
        =============================================*/

        $sql = "UPDATE $table SET activo_$suffix = 0 WHERE $nameId = :$nameId";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $response = array(
                'code' => 3
            );

            return $response;
        } catch (PDOException $e) {
            $response = array(
                'code' => 12
            );

            return $response;
        }
    }
}
