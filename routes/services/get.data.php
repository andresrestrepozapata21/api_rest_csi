<?php

require_once "models/connection.php";
require_once "controllers/get.data.controller.php";

if (isset($_GET)) {

    if ($table == "alertas") {
        $response = new GetController();
        $response->getDataAlerts($table, $select);
    }else{
        $response = new GetController();
        $response->getData($table, $select);
    }

}