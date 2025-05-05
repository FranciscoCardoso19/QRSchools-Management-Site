<?php
// Francisco

class Requisicao {
    public $id;
    public $id_user;
    public $disciplina;
    public $dataPedido;
    public $dataPrevisaoEntrega;
    public $dataEntrega;


    public function __construct($id, $id_user, $disciplina, $dataPedido, $dataPrevisaoEntrega, $dataEntrega) {
        $this->id = $id;
        $this->id_user = $id_user;
        $this->disciplina = $disciplina;
        $this->dataPedido = $dataPedido;
        $this->dataPrevisaoEntrega = $dataPrevisaoEntrega;
        $this->dataEntrega = $dataEntrega;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdUser() {
        return $this->id_user;
    }

    public function getDisciplina() {
        return $this->disciplina;
    }

    public function getDataPedido() {
        return $this->dataPedido;
    }

    public function getDataPrevisaoEntrega() {
        return $this->dataPrevisaoEntrega;
    }

    public function getDataEntrega() {
        return $this->dataEntrega;
    }

    public function toString(){
        return ("Id: " . $this->id . 
                ", IdUser: " . $this->id_user . 
                ", Disciplina: " . $this->disciplina . 
                ", DataPedido: " . $this->dataPedido . 
                ", DataPrevisaoEntrega: " . $this->dataPrevisaoEntrega . 
                ", DataEntrega: " . $this->dataEntrega);
    }
}
?>