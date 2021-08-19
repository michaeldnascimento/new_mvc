<?php

namespace App\Session\Admin;

use \App\Model\Entity\User;

class Login {

    /**
     * Método responsável por iniciar a sessão
     */
    private static function init()
    {
        //VERIFICA SE A SESSÃO NÃO ESTÁ ATIVA
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Método responsável por criar o login do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login(User $obUser): bool
    {
        //INICIA A SESSÃO
        self::init();

        //DEFINE A SESSÃO DO USUÁRIO
        $_SESSION['admin']['usuario'] = [
            'id'    => $obUser->id,
            'nome'  => $obUser->nome,
            'email' => $obUser->email
        ];

        //SUCESSO
        return true;

//        echo "<pre>";
//        print_r($obUser);
//        exit;

    }


    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged(): bool
    {
        //INICIA A SESSÃO
        self::init();

        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['admin']['usuario']['id']);

    }

    /**
     * Método responsável por executar o logout do usuário
     * @return boolean
     */
    public static function logout(): bool
    {
        //INICIA A SESSÃO
        self::init();

        //DESLOGA O URUÁRIO
        unset($_SESSION['admin']['usuario']);

        //SUCESSO
        return true;
    }
}