<?php

$router->get('/sample', 'SampleController@getAll');
$router->get('/sample/{id}', 'SampleController@findId');
$router->delete('/sample/delete/{id}', 'SampleController@delete');
$router->post('/sample/create', 'SampleController@store');
$router->get('/user','UserController@getAll');
$router->get('/user/{id}','UserController@findId');
$router->get('/user/search/{id}','UserController@findId');
$router->post('/user/create','UserController@store');
