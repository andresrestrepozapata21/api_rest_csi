<?php

require_once "models/connection.php";
require_once "controllers/get.dataFilter.controller.php";

$response = new GetController();
$response->getClosePosition($table, $suffix, $table2, $suffix2, $data);
