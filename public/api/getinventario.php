<?php
require '../../vendor/autoload.php';

include_once('../../backend/connection.php');
include_once('../../backend/models/inventario.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'psi_jwt_secret_key';

mysqli_begin_transaction($connection);

try{
    $headers = getallheaders();

    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    // Validação 1 Verificar se o metodo é GET
    if($_SERVER['REQUEST_METHOD'] != 'GET'){
        throw new Exception('Método não permitido');
    }

    if(preg_match('/Bearer\s(\S+)/', $authorization, $matches)){
        $jwt = $matches[1];
    }

    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Validação 2.1:
    // Validar se o id do utilizador está a ser enviado corretamente
    if(!isset($_GET['id'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    // Validação 4:
    // Validar se o id do utilizador existe na base de dados
    $id = $_GET['id'];

    $sqlSelect = 'SELECT * FROM inventarios WHERE id = ?';

    if($stmt = mysqli_prepare($connection, $sqlSelect)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        if(mysqli_stmt_execute($stmt)){

            $r = mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) == 0){
                throw new Exception('inventario não encontrado');
            }

            mysqli_stmt_bind_result($stmt, $id, $idUser, $dataInventario);
            mysqli_stmt_fetch($stmt);
            $inventario = new Inventario($id, $idUser, $dataInventario);

            //var_dump($stmt);
            //var_dump(mysqli_num_rows($result));
            //die;
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }

    $result = [
        'success' => true,
        'data' => [
            'inventario' => $inventario,
        ]
    ];

    echo(json_encode($result, JSON_PRETTY_PRINT));

}catch (ExpiredException $e) {
    // provided JWT is trying to be used after "exp" claim.
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo(json_encode($result));

} catch(Exception $e){
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo(json_encode($result));

}finally{
    mysqli_close($connection);
}

?>