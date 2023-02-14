<?php

require_once "connection.php";

class GetHomePageModel
{

    /*=============================================
    Peticiones GET para traer los datos del usuario
    =============================================*/
    static public function getUsuario($id_usuario_cliente)
    {

        $sql = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente = '$id_usuario_cliente'";

        $stmt = Connection::connect()->prepare($sql);
        try{
            $stmt->execute();
        }catch(PDOException $e){
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
    Peticiones GET para traer el plan comprado por el usuario
    =============================================*/
    static public function getPlanUsuario($id)
    {

        $sql = "SELECT * FROM planes_comprados pc INNER JOIN planes p ON p.id_plan=pc.fk_id_plan_plan_comprado WHERE fk_id_usuario_cliente_plan_comprado = $id AND activo_plan_comprado = 1";

        $stmt = Connection::connect()->prepare($sql);
        try{
            $stmt->execute();
        }catch(PDOException $e){
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}