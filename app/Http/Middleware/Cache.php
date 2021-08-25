<?php

namespace App\Http\Middleware;

use \App\Utils\Cache\File as CacheFile;
use \App\Http\Request;
use \App\Http\Response;
use Closure;
use Exception;

class Cache {

    /**
     * Método responsável por verificar se a request atual pode ser cacheada
     * @param Request $request
     * @return bool
     */
    private function isCacheable(Request $request): bool
    {
        //VALIDA O TEMPO DE CACHE
        if (getenv('CACHE_TIME') <= 0){
            return false;
        }

        //VALIDA O MÉTODO DA REQUISIÇÃO
        if ($request->getHttpMethod() != 'GET'){
            return false;
        }

        //VALIDA O HEADER DE CACHE - PODE PARA USUÁRIO DECIDIR OU NÃO EM UTILIZAR O CACHE - CASO O USUÁRIO SELECIONE no-cache VAI RETORNA COMO false
        $headers = $request->getHeaders();
        if (isset($headers['Cache-Control']) AND $headers['Cache-Control'] == 'no-cache'){
            return false;
        }

        //CACHEÁVEL
        return true;
    }

    /**
     * Método responsável por retornar a hash do cache
     * @param Request $request
     * @return string
     */
    public function getHash(Request $request): string
    {
        //URI DA ROTA
        $uri = $request->getRouter()->getUri();

        //QUERY PARAMS
        $queryParam = $request->getQueryParams();

        //SE O QUERY PARAMS FOR DIFERENTE DE VAZIO, ELE CONCATENA ? COM .http_build_query QUE TRANSFORMA ARRAY EM PARAMETROS DE URL SE NÃO MATEM ELE VAZIO
        $uri .= !empty($queryParam) ? '?' .http_build_query($queryParam) : '';

        //REMOVE AS BARRAS E RETORNA A HASH
        return rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');

    }

    /**
     * Método responsável por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        //VERIFICA SE A REQUEST ATUAL É CACHEÁVEL
        if (!$this->isCacheable($request)) return $next($request);

        //HASH DO CACHE
        $hash = $this->getHash($request);

        //RETORNA OS DADOS DO CACHE
        return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use($request, $next){
            return $next($request);
        });
    }


}