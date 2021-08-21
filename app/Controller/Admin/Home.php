<?php

namespace App\Controller\Admin;

use App\Http\Request;
use \App\Utils\View;

class Home extends Page {

    /**
     * Método responsável por retornar a renderização a view de home do painel
     * @param Request $request
     * @return string
     */
    public static function getHome(Request $request): string
    {
        //CONTEÚDO DE HOME
        $content = View::render('admin/modules/home/index', []);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Home - WDEV', $content, 'home');


    }


}