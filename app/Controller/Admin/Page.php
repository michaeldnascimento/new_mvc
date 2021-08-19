<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Page {


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
}