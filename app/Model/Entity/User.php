<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;
use PDOStatement;

class User {

    /*
     * ID do Usuário
     */
    public int $id;

    /*
     * Nome do Usuário
     */
    public string $nome;

    /*
     * E-mail do Usuário
     */
    public string $email;

    /*
    * Senha do Usuário
    */
    public string $senha;


    /**
     * Método responsavel por retornar um usuário com base em seu e-mail
     * @return User
     */
    public static function getUserByEmail(string $email)
    {
        return (new Database('usuarios'))->select('email = "'. $email.'"')->fetchObject(self::class);
    }

}