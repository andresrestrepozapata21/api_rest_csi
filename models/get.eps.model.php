<?php

require_once "connection.php";

class GetModel
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    static public function getData()
    {

        $sql = "SELECT nombre_eps FROM eps_vigentes";

        $stmt = Connection::connect()->prepare($sql);
        try{
            $stmt->execute();
        }catch(PDOException $e){
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}