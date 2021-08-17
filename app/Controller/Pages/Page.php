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

        //RENDERIZA OS LINKS
        foreach ($pages as $page){

            //ALTERA A PÁGINA
            $queryParams['page'] = $page['page'];

            //LINK
            $link = $url. '?' .http_build_query($queryParams);


            //VIEW
            $links .= View::render('pages/pagination/link', [
                'page'   => $page['page'],
                'link'   => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);

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