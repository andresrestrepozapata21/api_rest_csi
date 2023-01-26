<?php
require_once "connection.php";

class GetModel
{

    /*=============================================
    Peticiones GET con filtro para ver si existe el usuario cliente o agente que se necesite para logear
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
}
