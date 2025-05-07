<?php
require '../../vendor/autoload.php';
include_once('../../backend/connection.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

mysqli_begin_transaction($connection);

try {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwt = $matches[1];
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
    
    $postLenght = count($_POST);
    if($postLenght != 2){
        throw new Exception('Dados insuficientes');
    }
    
    if(!isset($_POST['idUser']) || !isset($_POST['dataInventario'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $idUser = trim($_POST['idUser']);
    $dataInventario = trim($_POST['dataInventario']);

    if(strlen($idUser) == 0 || strlen($dataInventario) == 0){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }
    
    $sql = 'INSERT INTO inventarios  (id_user, data_inventario) VALUES (?, ?)'; 
            
    if($stmt = mysqli_prepare($connection, $sql)){
        mysqli_stmt_bind_param($stmt, 'is', $idUser, $dataInventario);
        
        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            if(!mysqli_stmt_affected_rows($stmt)){
                throw new Exception('Erro ao inserir o inventario na base de dados');
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
        'message' => 'Inventario criado com sucesso',
    ];

    echo(json_encode($result, JSON_PRETTY_PRINT));

}catch (ExpiredException $e) {
    // provided JWT is trying to be used after "exp" claim.
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo(json_encode($result));

} catch (ExpiredException $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'success' => false,
        'error' => 'Token expirado: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);

} finally {
    mysqli_close($connection);
}
?>