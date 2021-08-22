<?php

namespace App\Controller\Api;

use \App\Db\Pagination;
use \App\Http\Request;
use \App\Model\Entity\Testimony as EntityTestimony;
use \Exception;

class Testimony extends Api{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    private static function getTestimonyItems(Request $request, &$obPagination): array
    {
        //DEPOIMENTOS
        $items = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityTestimony::getTestimonies(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obTestimony = $results->fetchObject(EntityTestimony::class)){
            $items[] =  [
                'id'       => (int)$obTestimony->id,
                'nome'     => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data'     => $obTestimony->data
            ];
        }

        //RETORNA OS DEPOIMENTOS
        return $items;
    }

    /**
     * Método responsável por retornar os depoimentos cadastrados
     * @param Request $request
     * @return array
     */
    public static function getTestimonies(Request $request): array
    {
        return [
            'depoimentos' => self::getTestimonyItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     * @throws Exception
     */
    public static function getTestimony(Request $request, $id): array
    {
        //VALIDA O ID DO DEPOIMENTO
        if (!is_numeric($id)){
            throw new Exception("O ID ".$id." não é válido.", 400);
        }

        //BUSCA DEPOIMENTO
        $obTestimony =  EntityTestimony::getTestimonyById($id);

        //VALIDA SE O DEPOIMENTO EXISTE
        if (!$obTestimony instanceof EntityTestimony){
            throw new Exception("O depoimento ".$id." não foi encontrado.", 404);
        }

        //RETORNA OS DETALHES DO DEPOIMENTOS
        return [
            'id'       => (int)$obTestimony->id,
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data'     => $obTestimony->data
        ];


    }


    /**
     * Método responsável por cadastrar um novo depoimento
     * @param Request $request
     * @throws Exception
     * @return array
     */
    public static function setNewTestimony(Request $request): array
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) OR !isset($postVars['mensagem'])){
            throw new Exception("Os campos 'nome' e 'mensagem' são obrigatórios.", 400);
        }

        //NOVO DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();

        //RETORNA OS DETALHES DO DEPOIMENTO CADASTRADO
        return [
            'id'       => (int)$obTestimony->id,
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data'     => $obTestimony->data
        ];
    }


    /**
     * Método responsável por atualizar um depoimento
     * @param Request $request
     * @param integer $id
     * @throws Exception
     * @return array
     */
    public static function setEditTestimony(Request $request, int $id): array
    {

        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) OR !isset($postVars['mensagem'])){
            throw new Exception("Os campos 'nome' e 'mensagem' são obrigatórios.", 400);
        }

        //BUSCAR O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            throw new Exception("O depoimento ".$id." não foi encontrado.", 404);
        }


        //ATUALIZA O DEPOIMENTO
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->atualizar();

        //RETORNA OS DETALHES DO DEPOIMENTO ATUALIZADO
        return [
            'id'       => (int)$obTestimony->id,
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data'     => $obTestimony->data
        ];
    }


    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param integer $id
     * @throws Exception
     * @return array
     */
    public static function setDeleteTestimony(Request $request, int $id): array
    {

        //BUSCAR O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            throw new Exception("O depoimento ".$id." não foi encontrado.", 404);
        }

        //EXCLUI O DEPOIMENTO
        $obTestimony->excluir();

        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso'       => true
        ];
    }
}