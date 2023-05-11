<?php
date_default_timezone_set('America/Bogota');
require_once "models/connection.php";

class PostModel
{
    /*=============================================
    Peticion post para crear datos
    =============================================*/
    static public function postData($table, $data)
    {

        $columns = "";
        $params = "";

        $columns .= "fk_id_usuario_cliente_plan_comprado,";
        $params .= $data->id_usuario_cliente .",";

        $columns .= "fk_id_plan_plan_comprado,";
        $params .= $data->id_plan .",";

        $columns .= "date_created_plan_comprado,";
        $params .= "'" . date('Y-m-d H:i:s') ."',";

        $columns .= "activo_plan_comprado,";
        $params .= 1 .",";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

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
