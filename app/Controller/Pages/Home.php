<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page {

    /**
     * Método responsavel por retornar o conteudo (view) da nossa home
     */
    public static function getHome(): string
    {

        //ORGANIZACAO
        $obOrganization = new Organization;

        //VIEW DA HOME
        $content =  View::render('pages/home', [
            'name'        => $obOrganization->name
        ]);

        //RETORNA A VIEW DA PAGINA
        return parent::getPage('HOME > WDEV', $content);
    }

}