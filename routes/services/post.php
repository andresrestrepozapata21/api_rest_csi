<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if (isset($_POST)) {

    if ($table == "servicios") {
        $response = new PostController();
        $response->postService($data, $file);
    } else {
        $response = new PostController();
        $response->postRegister($data);
    }
}
