<?php
date_default_timezone_set('America/Bogota');

require_once "connection.php";

class PutModel
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    static public function putData($table, $nameId, $id, $suffix)
    {
        /*=============================================
        Actualizamos registros
        =============================================*/

        $fecha = date('Y-m-d H:i:s');

        $sql = "UPDATE $table SET activo_$suffix = 1, fecha_verificacion_pin = '$fecha' WHERE $nameId = $id";

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
