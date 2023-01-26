<?php

require_once "models/connection.php";

class PostModel
{
    /*=============================================
    Peticion post para crear datos
    =============================================*/
    static public function postData($data)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO usuarios_agentes ($columns) VALUES ($params)";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindParam(":" . $key, $data->$key, PDO::PARAM_STR);
        }

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
