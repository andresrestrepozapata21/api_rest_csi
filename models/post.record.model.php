<?php

date_default_timezone_set('America/Bogota');

require_once "models/connection.php";

class PostModel
{
    /*=============================================
    Peticion post para crear datos
    =============================================*/
    static public function postData($table, $suffix, $data)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns .= "activo_$suffix,";
        $params .= 4 .",";

        $columns .= "estado_$suffix,";
        $params .= 1 .",";

        $columns .= "eliminado_$suffix,";
        $params .= 0 .",";

        
        if($table == "usuarios_clientes"){
            $columns .= "presentacion_inicial_popup_$suffix,";
            $params .= 1 .",";
            
            $columns .= "anuncio_popup_$suffix,";
            $params .= 0 .",";
        }
        
        $verificationCode = random_int(100000, 999999);
        $columns .= "codigo_verificacion,";
        $params .= $verificationCode .",";
        
        $columns .= " date_created_$suffix,";
        $params .= "'" . date('Y-m-d H:i:s') ."',";

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
        }else{
            return $link->errorInfo();
        }
    }
}
