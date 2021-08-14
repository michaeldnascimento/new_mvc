<?php

namespace App\Controller\Pages;

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