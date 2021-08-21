<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Db\Pagination;
use \App\Http\Request;

class Page {

    /**
     * Módulos disponível no painel
     */
    private static array $modules = [
        'home' =>[
            'label' => 'Home',
            'link' => URL.'/admin'
        ],
        'testimonies' =>[
            'label' => 'Depoimentos',
            'link' => URL.'/admin/testimonies'
        ],
        'users' =>[
            'label' => 'Usuários',
            'link' => URL.'/admin/users'
        ]
    ];


    /**
     * Método responsável por retornar o conteúdo (view) da estrutura genética da página do painel
     */
    public static function getPage(string $title,string $content): string
    {
        return View::render('admin/page', [
           'title'   => $title,
           'content' => $content
        ]);
    }

    /**
     * Método responsável por renderizar a view do menu do painel
     */
    private static function getMenu(string $currentModule): string
    {
        //LINKS DO MENU
        $links = '';

        //INTERA OS MÓDULOS
        foreach (self::$modules as $hash=>$module){
            $links .= View::render('admin/menu/link', [
                'label' => $module['label'],
                'link'  => $module['link'],
                'current' => $hash == $currentModule ? 'text-danger' : ''
            ]);
        }

        //RETORNA A RENDERIZACÃO DO MENU
        return View::render('admin/menu/box', [
            'links' => $links
        ]);

    }

    /**
     * Método responsável por rederizar a view do painel com conteúdos dinamicos
     */
    public static function getPanel(string $title, string $content, string $currentModule): string
    {

        //RENDERIZA A VIEW DO PAINEL
        $contentPanel = View::render('admin/panel', [
            'menu' => self::getMenu($currentModule),
            'content' => $content
        ]);

        //RETORNA A PÁGINA RENDERIZADA
        return self::getPage($title,$contentPanel);

    }

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
            $links .= View::render('admin/pagination/link', [
                'page'   => $page['page'],
                'link'   => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);

        }

        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('admin/pagination/box', [
            'links'   => $links
        ]);
    }


}