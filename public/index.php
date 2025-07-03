<?php
require_once __DIR__ . '/../App/bootstrap.php';
use App\Core\Router\Router;
use App\Core\Router\web;
use App\Core\Router\api;

require_once __DIR__ . '/../App/Core/Router/Router.php';
$router = new Router();
require_once __DIR__ . '/../App/Core/Router/web.php';
require_once __DIR__ . '/../App/Core/Router/api.php';

$router->dispatch($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);