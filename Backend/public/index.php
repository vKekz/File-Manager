<?php

require_once "../config/config.php";
require_once "../src/App.php";

$requestBody = json_decode(file_get_contents("php://input"));

$app = new App();
$app->handleRequest($_GET["route"], $_SERVER["REQUEST_METHOD"]);