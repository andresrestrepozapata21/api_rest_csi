<?php

require_once "models/connection.php";
require_once "controllers/put.checkAccount.controller.php";

    $response = new PutController();
    $response->putData($table, $select, $suffix, $data);

