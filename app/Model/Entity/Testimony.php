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
     * Método responsável por atualizar a vaga no banco
     * @return boolean
     */
    public function atualizar(){
        return (new Database('vagas'))->update('id = '.$this->id,[
            'titulo'    => $this->titulo,
            'descricao' => $this->descricao,
            'ativo'     => $this->ativo,
            'data'      => $this->data
        ]);
    }

    /**
     * Método responsável por excluir a vaga do banco
     * @return boolean
     */
    public function excluir(){
        return (new Database('vagas'))->delete('id = '.$this->id);
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