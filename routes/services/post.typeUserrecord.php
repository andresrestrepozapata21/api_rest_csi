<?php

require_once "models/connection.php";
require_once "controllers/post.typeUserrecord.controller.php";

if (isset($_POST)) {

    $response = new PostController();
    $response->postRegister($data);

}