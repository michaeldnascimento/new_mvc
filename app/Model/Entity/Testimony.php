<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;
use PDOStatement;

class Testimony{

    /**
     * Identificador único do depoimento
     */
    public int $id;

    /**
     * Nome do usuário que fez o depoimento
     */
    public string $nome;

    /**
     * Mensagem do depoimento
     */
    public string $mensagem;

    /**
     * Data de publicação do depoimento
     */
    public string $data;


    /**
     * Método responsável por cadastrar uma nova vaga no banco
     */
    public function cadastrar(): bool
    {
        //DEFINIR A DATA
        $this->data = date('Y-m-d H:i:s');

        //INSERIR O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('depoimentos'))->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

        //RETORNAR SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instancia atual
     */
    public function atualizar(): bool
    {
        //ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('depoimentos'))->update('id = '. $this->id, [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }


    /**
     * Método responsável por excluir um depoimento do banco de dados
     * @return boolean
     */
    public function excluir(): bool
    {
        //EXCLUI O DEPOIMENTO DO BANCO DE DADOS
        return (new Database('depoimentos'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getTestimonyById(int $id)
    {
        return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }


    /**
     * Método responsável por retornar depoimentos
     */
    public static function getTestimonies(string $where = null, string $order = null, string $limit = null, $fields = '*'): PDOStatement
    {
        return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por buscar uma vaga com base em seu ID
     * @param  integer $id
     * @return Vaga
     */
    public static function getVaga($id){
        return (new Database('vagas'))->select('id = '.$id)
            ->fetchObject(self::class);
    }

}