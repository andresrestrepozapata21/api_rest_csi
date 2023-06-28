<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/put.checkAccount.controller.php";
    //Llamo el metodo para verificar y activar la cuenta
    $response = new PutController();
    $response->putData($table, $select, $suffix, $data);

