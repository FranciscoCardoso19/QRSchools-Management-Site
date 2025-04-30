<?php
//gonçalo

class Cargo{
    public $id;
    public $nome;

    public function __construct($id, $nome) {
        $this->id = $id;
        $this->nome = $nome;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function toString(){
        return ('id: ' . $this->id . ', nome: ' . $this->nome );
    }
}
?>