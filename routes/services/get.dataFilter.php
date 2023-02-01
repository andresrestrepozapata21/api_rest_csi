<?php

require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

if (isset($_GET)) {

    if ($table == "planes_comprados") {
        $response = new GetController();
        $response->getDataPlanExistente($table, $data);
    }else{
        $response = new GetController();
        $response->getData($table, $select, $data, $id_plan);
    }
}
