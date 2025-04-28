<?php
include_once('../../backend/connection.php');

mysqli_begin_transaction($connection);

try{ 
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        throw new Exception('Método não permitido');
    }
    
    $postLenght = count($_POST);
    if($postLenght != 5){
        throw new Exception('Dados insuficientes');
    }
    
    if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || !isset($_POST['id_cargo'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $idCargo = trim($_POST['id_cargo']);

    if(strlen($name) == 0 || strlen($email) == 0 || strlen($password) == 0 || strlen($confirmPassword) == 0 || strlen($idCargo) == 0){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    if($password != $confirmPassword){
        throw new Exception('As passwords não coincidem');
    }

    
    $sqlSelect = 'SELECT * FROM users WHERE email = ?';

    if($stmt = mysqli_prepare($connection, $sqlSelect)){
        mysqli_stmt_bind_param($stmt, 's', $email);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            $result = mysqli_stmt_get_result($stmt);
            //var_dump(mysqli_num_rows($result));
            //die;
            if(mysqli_num_rows($result) > 0){
                throw new Exception('Erro ao criar registo.');
            }
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }
    

    $sql = 'INSERT INTO users (name, email, password, id_cargo) VALUES (?, ?, ?, ?)'; 
            
    if($stmt = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $password, $idCargo);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            if(!mysqli_stmt_affected_rows($stmt)){
                throw new Exception('Erro ao inserir o utilizador na base de dados');
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
        'message' => 'Utilizador registado com sucesso',
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