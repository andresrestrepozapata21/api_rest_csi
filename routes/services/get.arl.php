<?php

require_once "models/connection.php";
require_once "controllers/get.arl.controller.php";

if (isset($_GET)) {

    $response = new GetController();
    $response->getData();

}