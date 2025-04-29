<?php
require '../../vendor/autoload.php';

include_once('../../backend/connection.php');
include_once('../../backend/models/equipamento.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

try{
    $headers = getallheaders();

    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';


    if(preg_match('/Bearer\s(\S+)/', $authorization, $matches)){
        $jwt = $matches[1];
    }

    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Validação 1:
    // Verifica se o método é GET
    if($_SERVER['REQUEST_METHOD'] != 'GET'){
        throw new Exception('Método não permitido');
    }

    $sqlSelect = 'SELECT * FROM equipamentos';

    if($stmt = mysqli_prepare($connection, $sqlSelect)){
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $id, $idCategoria, $idEstado, $name, $numSerie, $descricao, $qrcode);
            
            $equipamento = [];

            while(mysqli_stmt_fetch($stmt)){
                $equipamento = new equipamento($id, $idCategoria, $idEstado, $name, $numSerie, $descricao, $qrcode);
                $equipamentos[$id] = $equipamento;
            }
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }

    $result = [
        'success' => true,
        'data' => [
            'equipamentos' => $equipamentos,
        ]
    ];

    echo(json_encode($result, JSON_PRETTY_PRINT));

}  catch (ExpiredException $e) {
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