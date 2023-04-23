<?php

require_once "models/connection.php";
require_once "models/put.checkAccount.model.php";
require_once "models/get.filter.model.php";

class PutController
{

    /*=============================================
    Peticiones PUT
    =============================================*/
    public function putData($table, $select, $suffix, $data)
    {

        /*=============================================
        Validamos que el ID exista en base de datos
        =============================================*/
        $response = GetModel::getDataFilter($table, $select . ",codigo_verificacion, email", $select, $data->$select);
        $id_usuario = $response[0]->$select;
        $email = $response[0]->email;

        if (!empty($response)) {

            if ($response[0]->codigo_verificacion == $data->codigo_verificacion) {

                $response = PutModel::putData($table, $select, $response[0]->$select, $suffix);

                if($suffix == "usuario_cliente"){
                    /*=============================================
                    Consultamos si este usuario existe en 
                    =============================================*/
                    $conexion = Connection::conexionAlternativa();
                    $sentencia_listar = "SELECT * FROM usuario_beneficiarios WHERE correo_usuario_beneficiario = '$email'";
                    $resultado_listado = mysqli_query($conexion, $sentencia_listar);

                    if(mysqli_num_rows($resultado_listado) > 0){
                        $fila = mysqli_fetch_assoc($resultado_listado);
                        $id_plan = $fila["fk_id_plan_usuario_beneficiario"];
                        $fecha_compra = $fila["fecha_compra_plan"];
                        $sentencia_insertar_plan_beneficiario = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,$id_plan,$id_usuario,'$fecha_compra')";
                        $resultado_insertar_plan_beneficiario = mysqli_query($conexion, $sentencia_insertar_plan_beneficiario);
                    }
                }

                $return = new PutController();
                $return->fncResponse($response);
            } else {
                $response = array(
                    "code" => 9
                );
                $return = new PutController();
                $return->fncResponse($response);
            }
        } else {
            $response = array(
                "code" => 1
            );
            $return = new PutController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if ($response['code'] == 3) {

                $json  = array(

                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            } else {
                $json = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            }
        } else {
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
