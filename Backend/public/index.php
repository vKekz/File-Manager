<?php

require_once "../config/config.php";
require_once "../src/App.php";

// TODO: Not create multiple times, if possible
$app = new App();

$requestRoute = $_GET["route"] ?? "";
$app->handleRequest($requestRoute);