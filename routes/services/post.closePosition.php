<?php

//Requiero los scripts que necesito para utilizar sus metodos
require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

//Llamo el controlador donde esta el metodo que cordina la obtencion de los datos
$response = new GetController();
$response->getClosePosition($table, $suffix, $table2, $suffix2, $data);
