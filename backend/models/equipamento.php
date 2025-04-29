<?php

class Equipamento{
    public $id;
    public $idCategoria;
    public $idEstado;
    public $name;
    public $numSerie;
    public $descricao;
    public $qrcode;

    public function __construct($id, $idCategoria, $idEstado, $name, $numSerie, $descricao, $qrcode) {
        $this->id = $id;
        $this->idCategoria = $idCategoria;
        $this->idEstado = $idEstado;
        $this->name = $name;
        $this->numSerie = $numSerie;
        $this->descricao = $descricao;
        $this->qrcode = $qrcode;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdCategoria(){
        return $this->idCategoria;
    }

    public function getIdEstado(){
        return $this->idEstado;
    }

    public function getName() {
        return $this->name;
    }

    public function getNumSerie() {
        return $this->numSerie;
    }

    public function getdescricao() {
        return $this->descricao;
    }

    public function getqrcode() {
        return $this->qrcode;
    }

    public function toString(){
        return ('id: ' . $this->id . ', idCategoria: ' . $this->idCategoria . ', idEstado: ' . $this->idEstado . ', name: ' . $this->name . ', numSerie: ' . $this->numSerie . ', descricao: ' . $this->descricao . ', qrcode: ' . $this->qrcode);
    }
}
?>