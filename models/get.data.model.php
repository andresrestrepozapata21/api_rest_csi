<?php

require_once "connection.php";

class GetAllModel
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    static public function getData($table, $select)
    {

        $sql = "SELECT $select FROM $table";

        $stmt = Connection::connect()->prepare($sql);
        try{
            $stmt->execute();
        }catch(PDOException $e){
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    static public function getDataAlerts($table, $select)
    {

        $sql = "SELECT $select FROM $table ORDER BY date_created_alerta DESC";

        $stmt = Connection::connect()->prepare($sql);
        try{
            $stmt->execute();
        }catch(PDOException $e){
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}