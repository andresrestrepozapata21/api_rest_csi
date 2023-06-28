<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/put.toUpdate.controller.php";

//Llamo el controlador donde esta el metodo que cordina la operacion de actualziar el token device de los usuarios
$response = new PutController();
$response->putData($table, $suffix, $select, $data);
