<?php
// Francisco

class Localizacao {
    public $id;
    public $idSala;
    public $idParentLocation;
    public $idEquipamento;
    public $idLocalizacaoCategoria;
    public $nome;
    public $descricao;


    public function __construct($id, $idSala, $idParentLocation, $idEquipamento, $idLocalizacaoCategoria, $nome, $descricao) {
        $this->id = $id;
        $this->idSala = $idSala;
        $this->idParentLocation = $idParentLocation;
        $this->idEquipamento = $idEquipamento;
        $this->idLocalizacaoCategoria = $idLocalizacaoCategoria;
        $this->nome = $nome;
        $this->descricao = $descricao;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdSala() {
        return $this->idSala;
    }

    public function getIdParentLocation() {
        return $this->idParentLocation;
    }

    public function getIdEquipamento() {
        return $this->idEquipamento;
    }

    public function getIdLocalizacaoCategoria() {
        return $this->idLocalizacaoCategoria;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function toString(){
        return ("Id: " . $this->id . 
                ", IdSala: " . $this->idSala . 
                ", IdParentLocation: " . $this->idParentLocation . 
                ", IdEquipamento: " . $this->idEquipamento . 
                ", IdLocalizacaoCategoria: " . $this->idLocalizacaoCategoria . 
                ", Nome: " . $this->nome . 
                ", Descricao: " . $this->descricao);
    }
}
?>