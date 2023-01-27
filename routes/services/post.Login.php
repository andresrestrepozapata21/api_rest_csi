<?php

require_once "models/connection.php";
require_once "controllers/post.Login.controller.php";

if (isset($_POST)) {

    $response = new PostController();
    $response->postLogin($table, $suffix, $data);

}