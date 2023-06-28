<?php

//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/post.record.controller.php";

//Condicional para verificar que sea el metodo POST
if (isset($_POST)) {
    //Llamo el controlador donde esta el metodo que cordina la operacion de registrar sin foto
    $response = new PostController();
    $response->postRegister($table, $suffix, $data);

}