<?php

require_once "../config/config.php";
require_once "../src/App.php";

$app = new App();
$app->forwardRequestToController($_GET["route"], $_SERVER["REQUEST_METHOD"]);