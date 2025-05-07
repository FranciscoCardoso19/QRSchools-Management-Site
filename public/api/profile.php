<?php
require '../../vendor/autoload.php';

include_once('../../backend/connection.php');
include_once('../../backend/models/user.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'psi_jwt_secret_key';

//var_dump($_POST);
//var_dump($_GET);

mysqli_begin_transaction($connection);

try{
    $headers = getallheaders();
    //var_dump($headers);
    //die;

    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if(preg_match('/Bearer\s(\S+)/', $authorization, $matches)){
        $jwt = $matches[1];
    }
    //var_dump($jwt);
    //die;
    
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Validação 1:
    // Verifica se o método é POST
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        throw new Exception('Método não permitido');
    }

    //Validação 2:
    // Apanhar o count do array post
    $postLenght = count($_POST);

    if($postLenght != 2){
        throw new Exception('Dados insuficientes');
    }

    // Validação 2.1:
    // Validar se as keys name e address estão a ser enviados corretamente
    if(!isset($_GET['id']) || !isset($_POST['name']) ||  !isset($_POST['password'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    // Validação 3:
    // Validar se todos os campos têm texto
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    if(strlen($name) == 0 || strlen($password) == 0 ){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    // Validação 4:
    // Validar se o id do utilizador existe na base de dados
    $id = $_GET['id'];

    $sqlSelect = 'SELECT * FROM users WHERE id = ?';

    if($stmt = mysqli_prepare($connection, $sqlSelect)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            $result = mysqli_stmt_get_result($stmt);
            //var_dump(mysqli_num_rows($result));
            //die;
            if(mysqli_num_rows($result) < 1){
                throw new Exception('Erro ao criar registo.');
            }
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }

    $sql = 'UPDATE users SET name = ?, password = ? WHERE id = ?';
    if($stmt = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($stmt, 'ssi', $name, $password, $id);

        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            //die;
            mysqli_commit($connection);
            echo json_encode([
                'success' => true,
                'message' => 'Registo actualizado com sucesso'
            ]);
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }

    mysqli_commit($connection);

    $sqlUpdate = 'UPDATE users SET name = ?, password = ? WHERE id = ?';
    if ($stmt = mysqli_prepare($connection, $sqlUpdate)) {
        mysqli_stmt_bind_param($stmt, 'ssi', $name, $password, $id);
        if (mysqli_stmt_execute($stmt)) {
            // Confirmar a transação
            mysqli_commit($connection);
            echo json_encode([
                'success' => true,
                'message' => 'Registo atualizado com sucesso'
            ]);
        } else {
            throw new Exception('Erro ao executar a query');
        }
    }

    // Fechar a transação e enviar resposta final
    $sqlSelect = 'SELECT * FROM users WHERE id = ?';
    if ($stmt = mysqli_prepare($connection, $sqlSelect)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $result = [
                    'success' => true,
                    'data' => [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => $row['password'],
                    ]
                ];
                echo json_encode($result, JSON_PRETTY_PRINT);
            } else {
                throw new Exception('Erro ao buscar o utilizador');
            }
        }
    }

} catch (Exception $e) {
    // Reverter a transação em caso de erro
    mysqli_rollback($connection);
    $result = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    echo json_encode($result);

} finally {
    // Fechar a conexão
    mysqli_close($connection);
}