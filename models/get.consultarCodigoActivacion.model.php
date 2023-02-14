<?php
require_once "connection.php";

class GetCodeModel
{

    /*=============================================
    Peticiones GET para validar si el codigo existe y esta activo
    =============================================*/
    static public function getDataFilter($data)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT * FROM codigos_activacion WHERE codigo_activacion = '$data->codigo_activacion' AND activo_codigo_activacion = 1";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
    Peticiones GET para validar si el codigo existe y esta activo
    =============================================*/
    static public function getPlanUsuario($id_usuario_cliente)
    {
        /*=============================================
        Consulta SQL
        =============================================*/
        $sql = "SELECT * FROM planes_comprados WHERE fk_id_usuario_cliente_plan_comprado = $id_usuario_cliente AND activo_plan_comprado = 1";

        $stmt = Connection::connect()->prepare($sql);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
