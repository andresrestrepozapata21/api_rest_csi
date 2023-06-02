<?php
date_default_timezone_set('America/Bogota');

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
            $params .= "'" . $value . "',";
        }

        $columns .= "ruta_imagen_servicio,";
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
            $params .= "'" . $value . "',";
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
    Peticion post para crear la foto de un viaje
    =============================================*/
    static public function postTripPicture($data, $target_path_nuevo)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= "'" . $value . "',";
        }

        $columns .= "ruta_imagen_registro_fotografico_viaje,";
        $params .= "'" . $target_path_nuevo . "',";

        $columns .= " date_created_registro_fotografico_viaje,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO registros_fotograficos_viajes ($columns) VALUES ($params)";

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
    static public function postAlert($data, $fotos)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        if($fotos != null){
            $contador = 1;
            foreach ($fotos as $value) {
                $columns .= " ruta" . $contador . "_imagen_alerta,";
                $params .= "'" . $value . "',";
                $contador++;
            }
        }

        $columns .= " estado_alerta,";
        $params .= "1,";

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

    /*=============================================
    Peticion post para crear las pociciones de los clientes o de los agentes
    =============================================*/
    static public function postWithoutPhoto($table, $suffix, $data)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .=  $key . "_$suffix,";
            $params .= ":" . $key . ",";
        }

        $columns .= " date_created_$suffix,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

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
    Peticion post para crear un viaje
    =============================================*/
    static public function postTrip($table, $suffix, $data)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .=  $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns .= " activo_$suffix,";
        $params .= "" . 1 . ",";

        $columns .= " cancelado_$suffix,";
        $params .= "" . 0 . ",";

        $columns .= " confirmacion_llegada_destino_$suffix,";
        $params .= "" . 0 . ",";

        $columns .= " date_created_$suffix,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

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
    Peticion post para crear un viaje
    =============================================*/
    static public function postStop($data, $target_path_nuevo)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= "'" . $value . "',";
        }

        $columns .= "foto_parada,";
        $params .= "'" . $target_path_nuevo . "',";

        $columns .= " date_created_parada,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO paradas ($columns) VALUES ($params)";

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
    Peticion post para registrar una reaccion del agente
    =============================================*/
    static public function postReactionAgentAlert($table, $suffix, $data)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .=  $key . "_$suffix,";
            $params .= ":" . $key . ",";
        }

        $columns .= " confirmacion_agente_$suffix,";
        $params .= "0,";

        $columns .= " confirmacion_cliente_$suffix,";
        $params .= "0,";

        $columns .= "notificacion_agente_$suffix,";
        $params .= "0,";

        $columns .= " notificacion_cliente_$suffix,";
        $params .= "0,";

        $columns .= " date_created_$suffix,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

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
    Peticion post para registrar una reaccion del cliente
    =============================================*/
    static public function postReactionCustomerAlert($table, $suffix, $data)
    {
        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .=  $key . "_$suffix,";
            $params .= ":" . $key . ",";
        }

        $columns .= " date_created_$suffix,";
        $params .= "'" . date('Y-m-d H:i:s') . "',";

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

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
