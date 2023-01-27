<?php

require_once "models/connection.php";
require_once "controllers/put.activateAccount.controller.php";

    $response = new PutController();
    $response->putData($table, $select, $suffix, $data);

