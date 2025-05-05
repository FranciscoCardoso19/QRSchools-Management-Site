<?php
include_once('../../backend/connection.php');

mysqli_begin_transaction($connection);

try{ 
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        throw new Exception('Método não permitido');
    }
    
    $postLenght = count($_POST);
    if($postLenght != 2){
        throw new Exception('Dados insuficientes');
    }
    
    if(!isset($_POST['nome']) ){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $nome = trim($_POST['nome']);

    if(strlen($nome) == 0 ){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $sql = 'INSERT INTO cargos  (nome) VALUES (?)'; 
            
    if($stmt = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $nome);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            if(!mysqli_stmt_affected_rows($stmt)){
                throw new Exception('Erro ao inserir o Cargo na base de dados');
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
        'message' => 'Cargo criado com sucesso',
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