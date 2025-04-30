<?php
include_once('../../backend/connection.php');

mysqli_begin_transaction($connection);

try{ 
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        throw new Exception('Método não permitido');
    }
    
    $postLenght = count($_POST);
    if($postLenght != 6){
        throw new Exception('Dados insuficientes');
    }
    
    if(!isset($_POST['idCategoria']) || !isset($_POST['idEstado']) || !isset($_POST['name']) || !isset($_POST['numSerie']) || !isset($_POST['descricao']) || !isset($_POST['qrcode'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $idCategoria = trim($_POST['idCategoria']);
    $idEstado = trim($_POST['idEstado']);
    $name = trim($_POST['name']);
    $numSerie = trim($_POST['numSerie']);
    $descricao = trim($_POST['descricao']);
    $qrcode = trim($_POST['qrcode']);

    if(strlen($idCategoria) == 0 || strlen($idEstado) == 0 || strlen($name) == 0 || strlen($numSerie) == 0 || strlen($descricao) == 0 || strlen($qrcode) == 0){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $sql = 'INSERT INTO equipamentos  (id_categoria, id_estado, name, num_serie, descricao, qrcode) VALUES (?, ?, ?, ?, ?, ?)'; 
            
    if($stmt = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($stmt, 'iisiss', $idCategoria, $idEstado, $name, $numSerie, $descricao, $qrcode);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            if(!mysqli_stmt_affected_rows($stmt)){
                throw new Exception('Erro ao inserir o equipamento na base de dados');
            }
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }else{
        throw new Exception('Erro ao preparar a query');
    }

    mysqli_commit($connection);

    $result = [
        'success' => true,
        'message' => 'Equipamento criado com sucesso',
    ];

    echo(json_encode($result));

}catch(Exception $e){
    mysqli_rollback($connection);
    //echo('Error: ' . $e->getMessage());
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo(json_encode($result));

}finally{
    mysqli_close($connection);
}
?>