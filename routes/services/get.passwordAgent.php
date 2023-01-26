<?php

require_once "models/connection.php";
require_once "controllers/get.passwordAgent.controller.php";

if (isset($_GET)) {

    $response = new GetController();
    $response->getData($data);
}