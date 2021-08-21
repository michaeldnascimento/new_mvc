<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\User;
use \App\Utils\View;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page {

    /**
     * Método responsável por retornar a renderização da página de login
     */
    public static function getLogin(Request $request, string $errorMessage = null): string
    {
        //STATUS > Se o errorMessage não for nulo, ele vai exibir a msg, se não ele não vai exibir nada
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('admin/login', [
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login > WDEV', $content);
    }

    /**
     * Método responsável por definir o login usuário

     */
    public static function setLogin(Request $request)
    {

        //POST VARS
        $postVars = $request->getPostVars();
        $email    = $postVars['email'] ?? '';
        $senha    = $postVars['senha'] ?? '';

        //BUSCA USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($email);
        if (!$obUser instanceof User){
            return self::getLogin($request, 'E-mail ou senha inválido' );
        }

        //VERIFICA A SENHA DO USUÁRIO > verifica a senha passada, e a senha do banco
        if (!password_verify($senha, $obUser->senha)){
            return self::getLogin($request, 'E-mail ou senha inválido' );
        }

        //CRIA A SESSÃO DE LOGIN
        SessionAdminLogin::login($obUser);

        //REDIRECIONA O USUÁRIO PARA A HOME DO ADMIN
        $request->getRouter()->redirect('/admin');

    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     */
    public static function setLogout(Request $request)
    {

        //DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::logout();

        //REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/admin/login');
    }

}