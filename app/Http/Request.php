<?php

namespace App\Http;

class Request {

    /**
     * Intancia do Router
     * @var Router
     */
    private $router;

    /**
     * Método HTTP da requisicao
     */
    private string $httpMethod;

    /**
     * URI da página
     */
    private string $uri;

    /**
     * Parametros da URL ($_GET)
     */
    private array $queryParams = [];

    /**
     * Variaveis recebidas no POST da página ($_POST)
     */
    private array $postVars = [];

    /**
     * cabeçalho de requisição
     */
    private array $headers = [];

    // CONTRUTOR DA CLASSE
    public function __construct($router){
        $this->router      = $router;
        $this->queryParams = $_GET ?? [];
        //$this->postVars    = $_POST ?? [];
        $this->headers     = getallheaders();
        $this->httpMethod  = $_SERVER['REQUEST_METHOD'] ?? '';
        //$this->uri         = $_SERVER['REQUEST_URI'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }

    /**
     * Método responsável por definir as variáveis do POST
     */
    private function setPostVars()
    {
        //VERIFICA O MÉTODO DA REQUISIÇÃO
        if($this->httpMethod == 'GET') return false;

        //POST PADRÃO
        $this->postVars = $_POST ?? [];

        //POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }


    /**
     * Método responsável por definir a URI
     */
    public function setUri()
    {
        //URI COMPLETA (COM GET)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        //REMOVE GETS DA URI
        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    /**
     * Método responsável por retornar a instancia de Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição
     */
    public function getHttpMethod() : string
    {
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar o método URI da requisição
     */
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * Método responsável por retornar o método headers da requisição
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Método responsável por retornar o os parametros da URL da requisição
     */
    public function getQueryParams() : array
    {
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar o os variaveis POST da requisição
     */
    public function getPostVars() : array
    {
        return $this->postVars;
    }


}