<?php
// andre

class Inventario{
    public $id;
    public $idUser;
    public $dataInventario;


    public function __construct($id, $idUser, $dataInventario) {
        $this->id = $id;
        $this->idUser = $idUser;
        $this->dataInventario = $dataInventario;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdUser() {
        return $this->idUser;
    }

    public function getDataInventario() {
        return $this->dataInventario;
    }

    public function toString(){
        return ("Id: " . $this->id . ", Id User: " . $this->idUser . ", Data Inventario: " . $this->dataInventario);
    }
}
?>