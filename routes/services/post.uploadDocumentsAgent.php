<?php

require_once "models/connection.php";
require_once "controllers/post.uploadDocumentsAgent.controller.php";

if (isset($_POST)) {

    $response = new PostController();
    $response->postRegister($table, $suffix, $id, $file);

}