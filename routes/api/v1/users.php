<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA DE LISTAGEM DE USUÁRIOS
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api',
        //'user-basic-auth'
        'jwt-auth',
        'cache'
    ],
   function($request){
    return new Response(200, Api\Users::getUsers($request), 'application/json');
   }
]);

//ROTA DE CONSULTA DO USUÁRIO ATUAL
$obRouter->get('/api/v1/users/me', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request){
        return new Response(200, Api\Users::getCurrentUser($request), 'application/json');
    }
]);

//ROTA DE CONSULTA INDIVIDUAL DE USUÁRIOS
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        //'user-basic-auth'
        'jwt-auth'
    ],
    function($request, $id){
        return new Response(200, Api\Users::getUser($request, $id), 'application/json');
    }
]);

//ROTA DE CADASTRO DE USUÁRIOS
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api',
        //'user-basic-auth'
        'jwt-auth'
    ],
    function($request){
        return new Response(201, Api\Users::setNewUser($request), 'application/json');
    }
]);

//ROTA DE ATUALIZAÇÃO DE USUÁRIOS
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        //'user-basic-auth'
        'jwt-auth'
    ],
    function($request, $id){
        return new Response(200, Api\Users::setEditUser($request, $id), 'application/json');
    }
]);

//ROTA DE EXCLUSÃO DE USUÁRIOS
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        //'user-basic-auth'
        'jwt-auth'
    ],
    function($request, $id){
        return new Response(200, Api\Users::setDeleteUser($request, $id), 'application/json');
    }
]);