<?php
//gonçalo

class Estado {
    public $id;
    public $estado;

    public function __construct($id, $estado) {
        $this->id = $id;
        $this->estado = $estado;
    }

    public function getId() {
        return $this->id;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function toString(){
        return ('id: ' . $this->id . ', estado: ' . $this->estado );
    }
}
?>