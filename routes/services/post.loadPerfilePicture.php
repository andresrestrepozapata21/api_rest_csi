<?php

//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/post.loadPerfilePicture.controller.php";

//Condicional para verificar que sea el metodo POST
if (isset($_POST)) {
    //Armo un estructura condicional para validar que operacion necesito hacer y con base a esto llamo el metodo necesario segun la necesidad y de ahi llamo los metodos para cargar la foto del cliente o del agente
    if($table == "usuarios_clientes"){
        $response = new PostController();
        $response->postRegister($table, $suffix, $id, $file);
    } else if ($table == "usuarios_agentes"){
        $response = new PostController();
        $response->postRegisterAgent($table, $suffix, $id, $file);
    }
}