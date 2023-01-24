<?php
require_once "connection.php";

class GetLoginModel
{

    /*=============================================
    Peticiones GET con filtro
    =============================================*/
    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    {

        
        /*=============================================
        Validar exitencia de una tabla en la BD
        =============================================*/
        $linkToArray = explode(",", $linkTo);
        $selectArray = explode(",", $select);

        foreach ($linkToArray as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        $equalToArray = explode("_", $equalTo);
        $linkToText = "";

        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {
                if ($key > 0) {
                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }

        /*=============================================
        Sin ordenar y sin limitar datos
        =============================================*/
        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = '$equalToArray[0]' $linkToText";

        /*=============================================
        Ordenar sin limitar datos
        =============================================*/
        if ($orderBy != null && $orderMode != null && $starAt == null && $endAt == null) {
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
        }

        /*=============================================
        Ordenar y limitar datos
        =============================================*/
        if ($orderBy != null && $orderMode != null && $starAt != null && $endAt != null) {
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $starAt, $endAt";
        }

        /*=============================================
        Sin ordenar y limitando datos
        =============================================*/
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $starAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
