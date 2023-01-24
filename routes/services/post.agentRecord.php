<?php

require_once "models/connection.php";
require_once "controllers/post.agentRecord.controller.php";

if (isset($_POST)) {

    $response = new PostController();
    $response->postRegister($_POST);

}