<?php

//Dados do utilizador da base de dados
$userName = 'qrschools_admin';
$userPassword = 'qrschoolsadmin';
$dataBase = 'qrschools';
$dns = 'localhost';

$connection = mysqli_connect($dns, $userName, $userPassword, $dataBase);

if (!$connection) {
    throw new Exception('Erro de conexão com a base de dados');
}

?>