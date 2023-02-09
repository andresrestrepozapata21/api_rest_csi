<?php
date_default_timezone_set('America/Bogota');

require_once "models/connection.php";

class PostModel
{
    /*=============================================
    Peticion post para crear datos
    =============================================*/
    static public function postData($table, $suffix, $id, $ruta)
    {

        $foranea = "fk_id_". $suffix . "_documento";
        $fecha = date('Y-m-d H:i:s');

        $sql = "INSERT INTO $table (ruta_documento, $foranea, date_created_documento) VALUES ('$ruta', $id, '$fecha')";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        if ($stmt->execute()) {
            $response = array(
                'lastId' => $link->lastInsertId(),
                'code' => 3
            );

            return $response;
        }else{
            return $link->errorInfo();
        }
    }
}
