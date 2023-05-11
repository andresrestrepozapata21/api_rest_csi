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
    Peticiones GET con filtro
    =============================================*/
    static public function getDataFilterTrips($table, $select, $linkTo, $equalTo)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT $select FROM $table WHERE $linkTo = '$equalTo' ORDER BY date_created_viaje DESC";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
    Peticiones GET con filtro
    =============================================*/
    static public function getTrip($table, $select, $linkTo, $equalTo)
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
    Peticiones GET con filtro para saber si el usuario tiene un plan comprado y activado
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

    /*=============================================
    Peticiones GET con filtro para saber si el servicio ya esta registrado en la zona a la que lo quieres agregar
    =============================================*/
    static public function getDataFilterServicePerZone($table, $data)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT * FROM $table WHERE fk_id_servicio_servicos_por_zona = $data->id_servicio AND fk_id_zona_servicos_por_zona = $data->id_zona";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
