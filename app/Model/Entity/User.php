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
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return bool
     */
    public function cadastrar():bool
    {
        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('usuarios'))->insert([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     */
    public function atualizar(): bool
    {
        //ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('usuarios'))->update('id = '. $this->id, [
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    /**
     * Método responsável por excluir um usuário do banco de dados
     * @return boolean
     */
    public function excluir(): bool
    {
        //EXCLUI O DEPOIMENTO DO BANCO DE DADOS
        return (new Database('usuarios'))->delete('id = '.$this->id);
    }


    /**
     * Método responsável por retornar um usuário com base no seu ID
     *
     * @param integer $id
     * @return User
     */
    public static function getUserById(int $id): User
    {
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }




    /**
     * Método responsavel por retornar um usuário com base em seu e-mail
     * @param string $email
     * @return User
     */
    public static function getUserByEmail(string $email)
    {
        return self::getUsers('email = "'. $email.'"')->fetchObject(self::class);
       //return (new Database('usuarios'))->select('email = "'. $email.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     */
    public static function getUsers(string $where = null, string $order = null, string $limit = null, $fields = '*'): PDOStatement
    {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }

}