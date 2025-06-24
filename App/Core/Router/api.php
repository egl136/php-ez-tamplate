<?php
use App\Core\Router\Router;

$router = new Router();
$router->get('/sample', 'SampleController@getAll');
$router->get('/sample/{id}', 'SampleController@findId');
$router->delete('/sample/delete/{id}', 'SampleController@delete');

$router->dispatch($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);
