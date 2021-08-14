<?php

namespace App\Common;

class Environment {

    /**
     * Método responsavel por carregar as variaveis de ambiente do projeto
     */
    public static function load($dir)
    {
        //VERIFICAR SE O ARQUIVO .ENV EXISTE
        if(!file_exists($dir.'/.env')){
            return false;
        }

        //DEFINE AS VARIAVEIS DE AMBIENTE
        $lines = file($dir.'/.env');
        foreach ($lines as $line){
            putenv(trim($line));
        }
    }
}