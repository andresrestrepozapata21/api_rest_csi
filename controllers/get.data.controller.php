<?php
//requiro los scripts que necesito
require_once "models/get.data.model.php";

class GetController{

    /*=============================================
    Peticiones GET, este metodo es reutilizable para obtener informacion, es solo validar que datos me esta entregando el Servicio y como quedaria armado el modelo
    =============================================*/
    public function getData($table, $select){

        $response = GetAllModel::getData($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

     /*=============================================
    Peticiones GET para obtener los datos de los panes, validar que datos me esta entregando el Servicio y como quedaria armado el modelo
    =============================================*/
    public function getDataPlans($table, $select){

        $response = GetAllModel::getDataPlans($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Peticiones GET para obtener los datos de las alertas, validar que datos me esta entregando el Servicio y como quedaria armado el modelo
    =============================================*/
    public function getDataAlerts($table, $select){

        $response = GetAllModel::getDataAlerts($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Peticiones GET para obtener los datos del popup que este activo en base de datos en su tabla, validar que datos me esta entregando el Servicio y como quedaria armado el modelo
    =============================================*/
    public function getDataPopup($table, $select){

        $response = GetAllModel::getDataPopup($table, $select);
        
        $return = new GetController();
        $return -> fncResponse($response);
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response){

        if(!empty($response)){
            $json  = array(

                'status' => 200,
                'total' => count($response),
                'result' => 3,
                'detail' => $response
            );
        }else{
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }

}