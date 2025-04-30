<?php
// andre

class LocalizacaoCategoria{
    public $id;
    public $nome;
    public $referencia;
    public $tamanho;
    public $mobilidade;
    public $abertura;
    public $numPortas;
    public $material;
    public $cor;
    public $forma;



    public function __construct($id, $nome, $referencia, $tamanho, $mobilidade, $abertura, $numPortas, $material, $cor, $forma) {
        $this->id = $id;
        $this->nome = $nome;
        $this->referencia = $referencia;
        $this->tamanho = $tamanho;
        $this->mobilidade = $mobilidade;
        $this->abertura = $abertura;
        $this->numPortas = $numPortas;
        $this->material = $material;
        $this->cor = $cor;
        $this->forma = $forma;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getReferencia() {
        return $this->referencia;
    }

    public function getTamanho() {
        return $this->tamanho;
    }

    public function getMobilidade() {
        return $this->mobilidade;
    }

    public function getAbertura() {
        return $this->abertura;
    }

    public function getNumPortas() {
        return $this->numPortas;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function getCor() {
        return $this->cor;
    }

    public function getForma() {
        return $this->forma;
    }

    public function toString(){
        return ("Id: " . $this->id . ", Nome: " . $this->nome . ", Referencia: " . $this->referencia . ", Tamanho: " . $this->tamanho . ", Mobilidade: " . $this->mobilidade . ", Abertura: " . $this->abertura . ", Num Portas: " . $this->numPortas . ", Material: " . $this->material . ", Cor: " . $this->cor . ", Forma: " . $this->forma);
    }
}
?>