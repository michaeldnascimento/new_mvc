<?php

namespace App\Controller\Api;

use \App\Http\Request;
use \App\Model\Entity\User;
use \Firebase\JWT\JWT;
use Exception;

class Auth extends Api{

    /**
     * Método responsável por gerar um token JWT
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public static function generateToken(Request $request): array
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['email']) OR !isset($postVars['senha'])){
            throw new Exception("Os campos 'e-mail' e 'senha' são obrigatórios", 400);
        }

        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($postVars['email']);
        if(!$obUser instanceof User){
            throw new Exception("O usuário ou senha são inválidos", 400);
        }

        //VALIDA A SENHA DO USUÁRIO
        if(!password_verify($postVars['senha'], $obUser->senha)){
            throw new Exception("O usuário ou senha são inválidos", 400);
        }

        //PAYLOAD
        $payload = [
            'email' => $obUser->email
        ];

        //RETORNA O TOKEN GERADO
        return [
            'token' => JWT::encode($payload, getenv('JWT_KEY'))
        ];
    }

}