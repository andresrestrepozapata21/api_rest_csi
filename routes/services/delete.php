<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

    $response = new DeleteController();
    $response->deleteData($table, $id, $nameId);
