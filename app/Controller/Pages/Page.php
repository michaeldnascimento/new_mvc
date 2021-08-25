<?php

namespace App\Controller\Pages;

use App\Db\Pagination;
use App\Http\Request;
use \App\Utils\View;

class Page {

    /**
     * Método responsavel por renderizar o topo da pagina
     */
    private static function getHeader(): string
    {
        return View::render('pages/header');
    }

    /**
     * Método responsavel por renderizar o rodapé da pagina
     */
    private static function getFooter(): string
    {
        return View::render('pages/footer');
    }

    /**
     * Método responsável por retornar um link da paginação
     * @param array $queryParams
     * @param array $page
     * @param string $url
     * @return string
     */
    private static function getPaginationLink(array $queryParams, array $page, string $url, $label = null): string
    {
        //ALTERA A PÁGINA
        $queryParams['page'] = $page['page'];

        //LINK
        $link = $url. '?' .http_build_query($queryParams);


        //VIEW
       return View::render('pages/pagination/link', [
            'page'   => $label ?? $page['page'],
            'link'   => $link,
            'active' => $page['current'] ? 'active' : ''
        ]);
    }


    /**
     * Método responsavel por renderizar o layout de paginação
     */
    public static function getPagination(Request $request, Pagination $obPagination): string
    {
        //PAGINAS
        $pages = $obPagination->getPages();


        //VERIFICA A QUANTIDADE DE PÁGINAS
        if(count($pages) <= 1) return '';

        //LINKS
        $links = '';

        //URL ATUAL (SEM GETS)
        $url = $request->getRouter()->getCurrentUrl();

        //GET PARAMETROS
        $queryParams = $request->getQueryParams();

        //PÁGINA ATUAL
        $currentPage = $queryParams['page'] ?? 1;

        //LIMIT DA PÁGINAS
        $limit = getenv('PAGINATION_LIMIT');

        //MEIO DA PAGINAÇÃO
        $middle = ceil($limit/2);

        //INÍCIO DA PAGINAÇÃO
        $start = $middle > $currentPage ? 0 : $currentPage - $middle;

        //AJUSTA O FINAL DA PAGINAÇÃO
        $limit = $limit + $start;

        //AJUSTA O INÍCIO DA PAGINAÇÃO
        if($limit > count($pages)){
            $diff  = $limit - count($pages);
            $start = $start - $diff;
        }

        //LINKS INICIAL
        if($start > 0){
            $links .= self::getPaginationlink($queryParams, reset($pages),  $url, '<<');
        }


        //RENDERIZA OS LINKS
        foreach ($pages as $page){

            //VERIFICA O START DA PAGINAÇÃO
            if($page['page'] <= $start) continue;

            //VERIFICA O LIMITE DE PAGINAÇÃO
            if($page['page'] > $limit){
                $links .= self::getPaginationlink($queryParams, end($pages),  $url, '>>');
                break;
            }

            $links .= self::getPaginationlink($queryParams, $page,  $url);
        }

        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('pages/pagination/box', [
            'links'   => $links
        ]);
    }

    /**
     * Método responsavel por retornar o conteudo (view) da nossa home
     */
    public static function getPage($title, $content): string
    {
        return View::render('pages/page', [
            'title'   => $title,
            'header'  => self::getHeader(),
            'content' => $content,
            'footer'  => self::getFooter()
        ]);
    }

}