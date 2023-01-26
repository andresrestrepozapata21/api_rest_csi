<?php

require_once "models/connection.php";
require_once "controllers/put.customer.controller.php";

    $response = new PutController();
    $response->putData($data);

