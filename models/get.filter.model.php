<?php
require_once "connection.php";

class GetModel
{

    /*=============================================
    Peticiones GET con filtro
    =============================================*/
    static public function getDataFilter($table, $select, $linkTo, $equalTo)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT $select FROM $table WHERE $linkTo = '$equalTo'";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
    Peticiones GET con filtro para saber que usuario tiene el usuario
    =============================================*/
    static public function getDataFilterPlanExistente($table, $data)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT * FROM $table WHERE fk_id_usuario_cliente_plan_comprado = $data->id_usuario_cliente AND activo_plan_comprado = 1";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
