<?php

require_once "models/connection.php";
require_once "controllers/post.loadPerfilePicture.controller.php";

if (isset($_POST)) {
    
    if($table == "usuarios_clientes"){
        $response = new PostController();
        $response->postRegister($table, $suffix, $id, $file);
    } else if ($table == "usuarios_agentes"){
        $response = new PostController();
        $response->postRegisterAgent($table, $suffix, $id, $file);
    }
}