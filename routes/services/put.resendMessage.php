<?php

require_once "models/connection.php";
require_once "controllers/put.resendMessage.controller.php";

    $response = new PutController();
    $response->putData($table, $suffix, $select, $data);

