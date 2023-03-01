<?php

date_default_timezone_set('America/Bogota');
require_once "models/get.filter.model.php";

use FTP\Connection as FTPConnection;

class Connection
{

    /*=============================================
    Información de la base de datos
    =============================================*/

    static public function infoDatabase()
    {
        $infoDB = array(
            "database" => "mipgenlinea_csi",
            "user" => "mipgenlinea_csi",
            "pass" => "+avU~Yj(]FjZ"
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
            "exp" => $time + (60 * 60 * 24 * 168), //Tiempo en que expirará el token (1 semana)
            "data" => [
                "id" => $id,
                "email" => $email
            ]
        );

        return $token;
    }

    /*=============================================
    Conexion alternativa
    =============================================*/
    static public function conexionAlternativa()
    {

        $db_host = "localhost";
        $db_user = "mipgenlinea_csi";
        $db_pass = "+avU~Yj(]FjZ";
        $db_name = "mipgenlinea_csi";
        $conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if (!$conexion->set_charset("utf8")) {
            printf("", $conexion->error);
        } else {
            printf("", $conexion->character_set_name());
        }

        return $conexion;
    }

    /*=============================================
    Validar el token de seguridad
    =============================================*/
    static public function tokenValidate($token, $table)
    {
        /*=============================================
        Traemos el usuario de acuerdo al token
        =============================================*/
        $user = GetModel::getDataFilter($table, "token_exp", "token", $token);

        if (!empty($user)) {
            /*=============================================
            Validamos que el token no haya expirado
            =============================================*/
            $time = time();

            if ($time < $user[0]->{"token_exp"}) {
                return "ok";
            } else {
                return "expired";
            }
        } else {
            return "no-auth";
        }
    }
}
