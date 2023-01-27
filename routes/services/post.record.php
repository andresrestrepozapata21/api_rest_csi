<?php

require_once "models/connection.php";
require_once "controllers/post.record.controller.php";

if (isset($_POST)) {

    $response = new PostController();
    $response->postRegister($table, $suffix, $data);

}