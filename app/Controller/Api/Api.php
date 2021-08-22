<?php

namespace App\Controller\Api;

use App\Db\Pagination;
use App\Http\Request;

class Api {

    /**
     * Método responsável por retornar os detalhes da API
     * @param Request $request
     * @return array
     */
    public static function getDetails(Request $request): array
    {
        return [
            'nome' => 'API - WDEV',
            'versao' => 'v1.0.0',
            'autor' => 'Michael',
            'email' => 'michaeldnascimento@hotmail.com'
        ];
    }

    /**
     * Método responsável por retornar os detalhes da paginação
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    protected static function getPagination(Request $request, $obPagination): array
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //PÁGINAS
        $pages = $obPagination->getPages();

        //RETORNO
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadePagina' => !empty($pages) ? count($pages) : 1
        ];

    }
}