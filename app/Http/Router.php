<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router {

    /**
     * URL completa do projeto
     */
    private string $url = '';


    /**
     * Prefixo de todas as rotas
     */
    private string $prefix = '';


    /**
     * Indece de rotas
     */
    private array $routes = [];


    /**
     * Instancia de Request
     * @var Request
     */
    private Request $request;


    /**
     * O content type padrão do response
     * @var string
     */
    private string $contentType = 'text/html';

    /**
     * Método responsável por iniciar a classe
     */
    public function __construct(string $url)
    {
        $this->request = new Request($this);
        $this->url     = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por alterar valor do content type
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }


    /**
     * Método responsável por definir o prefixo das rotas
     */
    private function setPrefix()
    {
        //INFORMAÇÃO DA URL ATUAL
        $parseUrl = parse_url($this->url);

        //DEFINIR O PREFIXO
        $this->prefix = $parseUrl['path'] ?? '';

    }

    /**
     * Método responsável por adicionar uma rota na yoclass
     */
    private function addRoute(string $method, string $route, array $params = [])
    {

        //VALIDAÇÃO DOS PARAMETROS
        foreach ($params as $key => $value){
            if ($value instanceof Closure){
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        //MIDDLEWARES DA ROTA
        $params['middlewares'] = $params['middlewares'] ?? [];

        //VARIAVEIS DA ROTA
        $params['variables'] = [];

        //PADRÃO DE VALIDAÇÃO DAS VARIAVEIS DAS ROTAS
        $patternVariable = '/{(.*?)}/';
        if(preg_match_all($patternVariable, $route, $matches)){
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        };

        //REMOVE BARRA NO FINAL DA ROTA
        $route = rtrim($route, '/');

        // PADRÃO DE VALIDAÇÃO DA URL
        $patternRoute = '/^' . str_replace('/','\/',$route). '$/';

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;

        //echo "<pre>";
        //print_r($this);
        //echo "<pre>";

    }

    /**
     * Método responsável por definir uma rota de GET
     */
    public function get(string $route, array $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de POST
     */
    public function post(string $route, array $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de PUT
     */
    public function put(string $route, array $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de DELETE
     */
    public function delete(string $route, array $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }



    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     */
    public function getUri() :string
    {
        //URI DA REQUEST
        $uri = $this->request->getUri();

        //FATIA A URI COM O PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //RETORNA A URI SEM PREFIXO E SEM A BARRA "/"
        return rtrim(end($xUri), '/');
    }

    /**
     * Método responsável por retornar os dados da rota atual
     * @throws Exception
     */
    private function getRoute() :array
    {
        //URI
        $uri = $this->getUri();

        //METHOD
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach ($this->routes as $patternRoute=>$methods)
        {
            //VERIFICAR SE A ROTA BATE O PADRÃO
            if(preg_match($patternRoute, $uri, $matches)){

                //VERIFICAR O METODO
                if (isset($methods[$httpMethod])){
                    //REMOVE A PRIMEIRA POSIÇÃO
                    unset($matches[0]);

                    //VARIAVEIS PROCESSADAS
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys,$matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;


                    //RETORNO DOS PARAMETROS DA ROTA
                    return $methods[$httpMethod];
                }
                throw new Exception("Método não permitido", 405);
            }
        }

        //URL NÃO ENCONTRADA
        throw new Exception("Url não encontrada", 404);

    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run(): Response
    {
        try {
            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();

            //VERIFICA O CONTROLADOR
            if (!isset($route['controller'])){
                throw new Exception("A URL não pode ser processada", 500);
            }

            //ARGUMENTOS DA FUNÇÃO
            $args = [];

            //REFLECTION
            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }


            //RETORNA A EXECUÇÃO DA FILA DE MIDDLEWARES
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);

            //RETORNA A EXECUSÃO
            //return call_user_func_array($route['controller'], $args);

        }catch (Exception $e){
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }

    }

    /**
     * Método responsável por retornar a mensagem de erro de acordo com content type
     * @param string $message
     * @return mixed
     */
    private function getErrorMessage(string $message)
    {
        switch ($this->contentType) {
            case 'application/json' :
                return [
                    'error' => $message
                ];
                break;

            default:
                return $message;
                break;
        }
    }

    /**
     * Método responsável por retornar a URL atual
     */
    public function getCurrentUrl(): string
    {
        return $this->url.$this->getUri();
    }

    /**
     * Método responsável por redirecionar a URL
     * @param string $router
     */
    public function redirect(string $router)
    {
        //URL
        $url = $this->url.$router;

        //EXECUTA O REDIRECT
        header('location: '.$url);
        exit;

    }



}