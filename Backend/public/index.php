<?php

require_once "../config/config.php";

use App\App;

$app = new App();
$app->handleRequest($_GET["route"], $_SERVER["REQUEST_METHOD"]);