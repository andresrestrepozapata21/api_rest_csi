<?php

//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/post.Login.controller.php";

//Condicional para verificar que sea el metodo POST
if (isset($_POST)) {

    //Llamo el controlador donde esta el metodo que cordina la operacion de login
    $response = new PostController();
    $response->postLogin($table, $suffix, $data);
}
