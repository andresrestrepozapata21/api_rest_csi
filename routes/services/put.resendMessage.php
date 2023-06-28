<?php
//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/put.resendMessage.controller.php";
//Llamo el metodo del controlador para reenvar el SMS
$response = new PutController();
$response->putData($table, $suffix, $select, $data);
