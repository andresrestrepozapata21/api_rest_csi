<?php
date_default_timezone_set('America/Bogota');

require_once "models/connection.php";

class LoginModel
{
    /*=============================================
    Peticion put para editar datos
    =============================================*/
    static public function login($table, $data, $id, $nameId, $suffix)
    {

        /*=============================================
        Actualizamos registros
        =============================================*/

        $set = "";

        foreach ($data as $key => $value) {
            $set .= $key . " = :" . $key . ",";
        }

        $set .= " lastlogin_$suffix = '" . date('Y-m-d H:i:s') . "',";

        $set = substr($set, 0, -1);

        $sql = "UPDATE $table SET $set WHERE $nameId = :$nameId";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }
        
        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $response = array(
                'code' => 3
            );

            return $response;
        } else {
            return $link->errorInfo();
        }
    }
}
