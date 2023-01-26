<?php
require_once "connection.php";

class GetIdModel
{

    /*=============================================
    Peticiones GET con filtro para ver si existe el usuario cliente o agente que se necesite para logear
    =============================================*/
    static public function getDataFilter($table, $select, $linkTo, $equalTo)
    {

        /*=============================================
        Validar exitencia de una tabla en la BD
        =============================================*/
        $linkToArray = explode(",", $linkTo);

        $equalToArray = explode("_", $equalTo);

        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = '$equalToArray[0]'";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
