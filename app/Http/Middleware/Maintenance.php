<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;
use Exception;

class Maintenance {


    /**
     * Método responsável por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        //VERIFICA O ESTADO DE MANUTENÇÃO DA PAGINA
        if(getenv('MAINTENANCE') == 'true'){
            throw new Exception("Página em manutenção, tente novamente mais tarde.", 200);
        }
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }


}