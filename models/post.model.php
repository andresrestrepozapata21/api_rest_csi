<?php

require_once "models/connection.php";

class PostModel
{
    /*=============================================
    Peticion post para crear servicio
    =============================================*/
    static public function postService($data, $target_path_nuevo)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= "'".$value . "',";
        }

        $columns .= "imagen_servicio,";
        $params .= "'" . $target_path_nuevo . "',";

        $columns .= " date_created_servicio,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO servicios ($columns) VALUES ($params)";

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
        } else {
            return $link->errorInfo();
        }
    }

    /*=============================================
    Peticion post para crear planes
    =============================================*/
    static public function postPlan($data, $target_path_nuevo)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= "'".$value . "',";
        }

        $columns .= "ruta_imagen_plan,";
        $params .= "'" . $target_path_nuevo . "',";

        $columns .= " date_created_plan,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO planes ($columns) VALUES ($params)";
        
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
        } else {
            return $link->errorInfo();
        }
    }

    /*=============================================
    Peticion post para crear servicio por zona
    =============================================*/
    static public function postServicePerZone($data)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= "fk_" . $key . "_servicos_por_zona,";
            $params .= ":" . $key . ",";
        }

        $columns .= " date_created_servicos_por_zona,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO servicios_por_zona ($columns) VALUES ($params)";

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
        } else {
            return $link->errorInfo();
        }
    }

    /*=============================================
    Peticion post para crear alerta
    =============================================*/
    static public function postAlert($data, $foto)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns .= " ruta_imagen_alerta,";
        $params .= "'" . $foto . "',";

        $columns .= " date_created_alerta,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO alertas ($columns) VALUES ($params)";
        
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
        } else {
            return $link->errorInfo();
        }
    }
}
