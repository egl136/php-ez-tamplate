<?php

$router = new Router();
$router->get('/sample', 'SampleController@getAll');
$router->get('/sample/{id}', 'SampleController@findId');
$router->delete('/sample/delete/{id}', 'SampleController@delete');
$router->post('/sample/create', 'SampleController@store');

