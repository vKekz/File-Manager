<?php

require_once "../config/config.php";

const EXPECTED_ROUTE = "/File-Manager/Backend/public/api";
$requestRoute = $_SERVER["REQUEST_URI"];

if (!str_starts_with($requestRoute, EXPECTED_ROUTE)) {
    return;
}

require_once "../src/App.php";

$sanitizedRoute = "api" . explode(EXPECTED_ROUTE, $requestRoute)[1];
$requestMethod = $_SERVER["REQUEST_METHOD"];

$app = new App();
$app->forwardRequestToController($sanitizedRoute, $requestMethod);