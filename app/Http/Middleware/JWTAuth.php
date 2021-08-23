<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Model\Entity\User;
use Closure;
use Exception;
use Firebase\JWT\JWT;

class JWTAuth {

    /**
     * Método responsável por retornar uma instância de usuário autenticado
     * @param Request $request
     * @return User
     * @throws Exception
     */
    private function getJWTAuthUser(Request $request)
    {
        //HEADERS
        $headers = $request->getHeaders();

        //TOKEN PURO EM JWT
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
            //DECODE
            $decode = (array)JWT::decode($jwt, getenv('JWT_KEY'), ['HS256']);
        }catch (Exception $e){
            throw new Exception("Token inválido", 403);
        }

        //EMAIL
        $email = $decode['email'] ?? '';

        //BUSCA O USUÁRIO POR E-MAIL
        $obUser = User::getUserByEmail($email);


        //RETORNA O USUÁRIO
        return $obUser instanceof User ? $obUser : false;

    }

    /**
     * Método responsável por validar o acesso via JWT
     * @param Request $request
     * @throws Exception
     */
    private function auth(Request $request)
    {
        //VERIFICA O USUÁRIO RECEBIDO
        if($obUser = $this->getJWTAuthUser($request)){
            $request->user = $obUser;
            return true;
        }

        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new \Exception("Acesso negado", 403);
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

        //REALIZA A VALIDAÇÃO DO ACESSO VIA JWT AUTH
        $this->auth($request);


        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }


}