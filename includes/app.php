<?php
//DEFINE TIMEZONE SISTEMA
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

//COMPOSER - AUTOLOAD
require __DIR__ . '/../vendor/autoload.php';

use \App\Utils\View;
use \App\Common\Environment;
use \App\Db\Database;
use \App\Http\Middleware\Queue as MiddlewareQueue;

//CARREGA AS VARIAVEIS DE AMBIENTE DO PROJETO
Environment::load(__DIR__. '/../');

//DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
);

//DEFINE A CONSTANTE DE URL
define('URL', getenv('URL'));

//DEFINE O VALOR PADRÃO DAS VARIAVEIS
View::init([
    'URL' => URL
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARE
MiddlewareQueue::setMap([
    'maintenance'           => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login'  => \App\Http\Middleware\RequireAdminLogin::class,
    'api'                   => \App\Http\Middleware\Api::class,
    'user-basic-auth'       => \App\Http\Middleware\UserBasicAuth::class,
    'jwt-auth'              => \App\Http\Middleware\JWTAuth::class,
    'cache'              => \App\Http\Middleware\Cache::class
]);


//DEFINE O MAPEAMENTO DE MIDDLEWARE PADRÕES (EXECUTADOS EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance'
]);