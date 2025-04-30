<?php
// Francisco

class Sala {
    public $id;
    public $nome;
    public $piso;

    public function __construct($id, $nome, $piso) {
        $this->id = $id;
        $this->nome = $nome;
        $this->piso = $piso;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getPiso() {
        return $this->piso;
    }

    public function toString(){
        return ("Id: " . $this->id . 
                ", Nome: " . $this->nome . 
                ", Piso: " . $this->piso);
    }
}
?>