<?php

namespace App\Http\Middleware;
use App\Http\Request;
use App\Http\Response;
use Closure;
use Exception;

class Queue {

    /**
     * Mapeamento de middleware
     */
    private static array $map = [];

    /**
     * Mapeamento de middleware que serão carregados em todas as rotas
     */
    private static array $default = [];

    /**
     * Fila de middlewares a serem executados
     */
    private array $middlewares = [];

    /**
     * Função de execução do controlador
     */
    private Closure $controller;


    /**
     * Argumentos da função do controlador
     */
    private array $controllerArgs = [];

    /**
     * Método responsável por construir a classe de fila de middleware
     */
    public function __construct(array $middlewares, Closure $controller, array $controllerArgs)
    {
        $this->middlewares    = array_merge(self::$default,$middlewares);
        $this->controller     = $controller;
        $this->controllerArgs = $controllerArgs;
    }


    /**
     * Método responsável por definir o mapeamento de middlewares
     */
    public static function setMap(array $map)
    {
        self::$map = $map;
    }

    /**
     * Método responsável por definir o mapeamento de middlewares padrão em todas as rotas
     */
    public static function setDefault(array $default)
    {
        self::$default = $default;
    }

    /**
     * Método responsável por executar o próximo nivel da fila de middlewares
     * @throws Exception
     */
    public function next(Request $request): Response
    {

        //VERIFICA SE A FILA ESTÁ VAZIA
        if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        //MIDDLEWARE
        $middleware = array_shift($this->middlewares);

        //VERIFICA O MAPEAMENTO
        if(!isset(self::$map[$middleware])){
            throw new Exception("Problemas ao precessar o middleware da requisição", 500);
        }

        //NEXT
        $queue = $this;
        $next = function($request) use($queue){
            return $queue->next($request);
        };

        //EXECUTA O MIDDLEWARE
        return (new self::$map[$middleware])->handle($request,$next);

//        echo "<pre>";
//       print_r($next);
//        echo "<pre>";
////        print_r($this);
//        exit;
    }


}