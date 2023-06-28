<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/get.password.controller.php";

//Condicional para verificar que sea el metodo GET
if (isset($_GET)) {
    //Llamo el controlador donde esta el metodo que cordina la obtencion de los datos
    $response = new GetController();
    $response->getData($table, $data);
}
