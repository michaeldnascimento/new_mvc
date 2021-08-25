<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies', [
    'middlewares' => [
        'api',
        'cache'
    ],
   function($request){
    return new Response(200, Api\Testimony::getTestimonies($request), 'application/json');
   }
]);

//ROTA DE CONSULTA INDIVIDUAL DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api'
    ],
    function($request, $id){
        return new Response(200, Api\Testimony::getTestimony($request, $id), 'application/json');
    }
]);

//ROTA DE CADASTRO DE DEPOIMENTO
$obRouter->post('/api/v1/testimonies', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request){
        return new Response(201, Api\Testimony::setNewTestimony($request), 'application/json');
    }
]);

//ROTA DE ATUALIZAÇÃO DE DEPOIMENTO
$obRouter->put('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, Api\Testimony::setEditTestimony($request, $id), 'application/json');
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTO
$obRouter->delete('/api/v1/testimonies/{id}', [
    'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response(200, Api\Testimony::setDeleteTestimony($request, $id), 'application/json');
    }
]);