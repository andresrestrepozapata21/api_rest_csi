<?php

use FTP\Connection as FTPConnection;

class Connection
{

    /*=============================================
    Información de la base de datos
    =============================================*/

    static public function infoDatabase()
    {
        $infoDB = array(
            "database" => "backend_csi",
            "user" => "root",
            "pass" => ""
        );
        return $infoDB;
    }

    /*=============================================
    Conexión de la base de datos
    =============================================*/

    static public function connect()
    {

        try {
            $link = new PDO(
                "mysql:host=localhost;dbname=" . Connection::infoDatabase()["database"],
                Connection::infoDatabase()['user'],
                Connection::infoDatabase()['pass']
            );

            $link->exec("set names utf8");
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        return $link;
    }

    /*=============================================
    Generar token de autenticación
    =============================================*/
    static public function jwt($id, $email)
    {

        $time = time();

        $token = array(
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60 * 60 * 24), //Tiempo en que expirará el token (1 día)
            "data" => [
                "id" => $id,
                "email" => $email
            ]
        );

        return $token;
    }
}