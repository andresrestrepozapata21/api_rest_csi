<?php

require_once "models/connection.php";
require_once "controllers/get.data.controller.php";

if (isset($_GET)) {

    $response = new GetController();
    $response->getData($table, $select);

}