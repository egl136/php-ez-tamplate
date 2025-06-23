<?php
use App\Core\Router\Router;

$router = new Router();
$router->get('/sample', 'SampleController@getAll');
$router->get('/sample/{id}', 'SampleController@findId');

$router->dispatch($_SERVER['REQUEST_URI']);
