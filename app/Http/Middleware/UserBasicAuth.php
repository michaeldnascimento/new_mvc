<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Model\Entity\User;
use Closure;
use Exception;

class UserBasicAuth {

    /**
     * Método responsável por retornar uma instância de usuário autenticado
     * @return User
     */
    private function getBasicAuthUser()
    {
        //VERIFICA A EXISTÊNCIA DOS DADOS DE ACESSO
        if (!isset($_SERVER['PHP_AUTH_USER']) OR !isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }

        //BUSCA O USUÁRIO POR E-MAIL
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        //VERIFICA A INSTANCIA
        if (!$obUser instanceof User){
            return false;
        }

        //VALIDA SENHA E RETORNA USUÁRIO
        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;

    }

    /**
     * Método responsável por validar o acesso via HTTP BASIC AUTH
     * @param Request $request
     * @throws Exception
     */
    private function basicAuth(Request $request)
    {
        //VERIFICA O USUÁRIO RECEBIDO
        if($obUser = $this->getBasicAuthUser()){
            $request->user = $obUser;
            return true;
        }

        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new \Exception("Usuário ou senha inválidos", 403);
    }

    /**
     * Método responsável por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {

        //REALIZA A VALIDAÇÃO DO ACESSO VIA BASIC AUTH
        $this->basicAuth($request);


        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }


}