<?php

class User{
    public $id;
    public $name;
    public $email;
    public $password;
    public $idCargo;

    public function __construct($id, $name, $email, $password, $idCargo) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->idCargo = $idCargo;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getIdCargo() {
        return $this->idCargo;
    }

    public function toString(){
        return('id: '.$this->id.' name: '.$this->name.' email: '.$this->email.' password: '.$this->password.' idCargo: '.$this->idCargo);
    }
}
?>