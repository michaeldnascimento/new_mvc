<?php

namespace App\Controller\Admin;

use App\Http\Request;
use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \App\Db\Pagination;

class User extends Page {

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @param Pagination $obPagination
     */
    private static function getUserItems(Request $request, &$obPagination): string
    {
        //USUÁRIOS
        $items = '';

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)){
            $items .=  View::render('admin/modules/users/item', [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ]);
        }

        //RETORNA OS DEPOIMENTOS
        return $items;
    }

    /**
     * Método responsável por retornar a renderização a view de listagem de usuários
     * @param Request $request
     * @return string
     */
    public static function getUsers(Request $request): string
    {
        //CONTEÚDO DE HOME
        $content = View::render('admin/modules/users/index', [
            'itens'       => self::getUserItems($request, $obPagination),
            'pagination'  => parent::getPagination($request, $obPagination),
            'status'      => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Usuários > WDEV', $content, 'users');


    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewUser(Request $request): string
    {
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'    => 'Cadastrar Usuário',
            'nome'     => '',
            'email'    => '',
            'status'   => self::getStatus($request)
        ]);

        return parent::getPanel('Cadastro usuário > WDEV', $content, 'users');
    }

    /**
     * Método responsável por cadastrar um depoimento no banco
     * @param Request $request
     * @return string
     */
    public static function setNewUser(Request $request): string
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $nome  = $postVars['nome'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA E-MAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);
        if ($obUser instanceof EntityUser){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        //NOVA INSTANCIA DE USUÁRIO
        $obUser = new EntityUser();
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha =  password_hash($senha, PASSWORD_DEFAULT);
        $obUser->cadastrar();


        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');

    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus(Request $request): string
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGEM DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluído com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O E-mail digitado já está sendo ultilizado por outro usuário!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um novo depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser(Request $request, int $id): string
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'    => 'Editar Usuário',
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Editar usuário > WDEV', $content, 'users');
    }

    /**
     * Método responsável por grava a ataulização de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser(Request $request, int $id): string
    {
        //OBTÉM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        $email = $postVars['email'] ?? '';
        $nome  = $postVars['nome'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA E-MAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser && $obUser->id != $id){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        //ATUALIZA A INSTANCIA
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha =  password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }


    /**
     * Método responsável por retornar o formulário de exclusão de um Usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser(Request $request, int $id): string
    {
        //OBTÉM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/delete', [
            'nome'     => $obUser->nome,
            'email'    => $obUser->email
        ]);

        return parent::getPanel('Excluir Usuário > WDEV', $content, 'users');
    }


    /**
     * Método responsável por excluir um Usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser(Request $request, int $id): string
    {
        //OBTÉM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //EXCLUI O DEPOIMENTO
        $obUser->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }


}