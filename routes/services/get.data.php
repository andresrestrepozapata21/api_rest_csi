<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/get.data.controller.php";
//Condicional para verificar que sea el metodo GET
if (isset($_GET)) {
    //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad
    if ($table == "alertas") {
        $response = new GetController();
        $response->getDataAlerts($table, $select);
    } else if ($table == "planes") {
        $response = new GetController();
        $response->getDataPlans($table, $select);
    } else if ($table == "anuncios_popup") {
        $response = new GetController();
        $response->getDataPopup($table, $select);
    }
    //este caso es la excepcion cuando hay varios endpoints que son genericos y tienen la misma estructura, se reutiliza el metodo en cuestion
    else {
        $response = new GetController();
        $response->getData($table, $select);
    }
}
