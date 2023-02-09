<?php

require_once "models/connection.php";
require_once "controllers/post.uploadDocuments.controller.php";

if (isset($_POST)) {
    
    if($table == "usuarios_clientes"){
        $response = new PostController();
        $response->postRegister($table, $suffix, $id, $file1, $file2, $file3);
    } else if ($table == "usuarios_agentes"){
        $response = new PostController();
        $response->postRegisterAgent($table, $suffix, $id, $file);
    }
}