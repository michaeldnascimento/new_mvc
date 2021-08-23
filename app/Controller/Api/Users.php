<?php

namespace App\Controller\Api;

use \App\Db\Pagination;
use \App\Http\Request;
use \App\Model\Entity\User as EntityUser;
use \Exception;

class Users extends Api{

    /**
     * Método responsável por obter a renderização dos itens de usuarios para API
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    private static function getUserItems(Request $request, &$obPagination): array
    {
        //USUÁRIOS
        $items = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        //RESULTADOS DA API
        $results = EntityUser::getUsers(null, 'id ASC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)){
            $items[] =  [
                'id'       => (int)$obUser->id,
                'nome'     => $obUser->nome,
                'email'    => $obUser->email
            ];
        }

        //RETORNA OS USUÁRIOS
        return $items;
    }

    /**
     * Método responsável por retornar os usuários cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers(Request $request): array
    {
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um usuário
     * @param Request $request
     * @param integer $id
     * @return array
     * @throws Exception
     */
    public static function getUser(Request $request, int $id): array
    {
        //VALIDA O ID DO USUÁRIO
        if (!is_numeric($id)){
            throw new Exception("O ID ".$id." não é válido.", 400);
        }

        //BUSCA USUÁRIO
        $obUser =  EntityUser::getUserById($id);

        //VALIDA SE O DEPOIMENTO EXISTE
        if (!$obUser instanceof EntityUser){
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }

        //RETORNA OS DETALHES DO DEPOIMENTOS
        return [
            'id'       => (int)$obUser->id,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email
        ];


    }


    /**
     * Método responsável por retornar o usuário atualmente conectado
     * @param Request $request
     * @return array
     */
    public static function getCurrentUser(Request $request): array
    {
        //USUÁRIO ATUAL
        $obUser = $request->user;

        //RETORNA OS DETALHES DO DEPOIMENTOS
        return [
            'id'       => (int)$obUser->id,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email
        ];

    }


    /**
     * Método responsável por cadastrar um novo usuário
     * @param Request $request
     * @throws Exception
     * @return array
     */
    public static function setNewUser(Request $request): array
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) OR !isset($postVars['email']) OR !isset($postVars['senha'])){
            throw new Exception("Os campos 'nome' e 'email' e 'senha' são obrigatórios.", 400);
        }

        //VALIDA A DUPLICAÇÃO DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if ($obUserEmail instanceof EntityUser){
            throw new Exception("O e-mail '".$postVars['email']."'já está em uso.", 400);
        }


        //NOVA INSTANCIA DE USUÁRIO
        $obUser = new EntityUser();
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //RETORNA OS DETALHES DO USUÁRIO CADASTRADO
        return [
            'id'       => (int)$obUser->id,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email
        ];
    }


    /**
     * Método responsável por atualizar um usuário
     * @param Request $request
     * @param integer $id
     * @throws Exception
     * @return array
     */
    public static function setEditUser(Request $request, int $id): array
    {

        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) OR !isset($postVars['email']) OR !isset($postVars['senha'])){
            throw new Exception("Os campos 'nome' e 'email' e 'senha' são obrigatórios.", 400);
        }

        //BUSCAR O USUÁRIO NO BANCO
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }


        //VALIDA A DUPLICAÇÃO DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id){
            throw new Exception("O e-mail '".$postVars['email']."'já está em uso.", 400);
        }


        //ATUALIZA O USUÁRIO
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->atualizar();


        //RETORNA OS DETALHES DO USUÁRIO ATUALIZADO
        return [
            'id'       => (int)$obUser->id,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
        ];
    }


    /**
     * Método responsável por excluir um usuário
     * @param Request $request
     * @param integer $id
     * @throws Exception
     * @return array
     */
    public static function setDeleteUser(Request $request, int $id): array
    {

        //BUSCAR O USUÁRIO NO BANCO
        $obUser = EntityUser::getUserById($id);

        //VALIDA SE O USUÁRIO EXISTE
        if(!$obUser instanceof EntityUser){
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }

        //IMPEDE A EXCLUSÃO DO PRÓPRIO CADASTRO
        if($obUser->id == $request->user->id){
            throw new Exception("Não é prossível excluir o cadastro atualmente conectado", 404);
        }

        //EXCLUI O USUÁRIO
        $obUser->excluir();

        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso'       => true
        ];
    }
}