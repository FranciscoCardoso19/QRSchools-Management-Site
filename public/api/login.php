<?php
require '../../vendor/autoload.php';

include_once('../../backend/connection.php');
include_once('../../backend/models/user.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//var_dump($_POST);
//echo('login')


try{
    // Validação 1:
    // Verifica se o método é POST
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        throw new Exception('Método não permitido');
    }

    // Validação 2:
    // Apanhar o count do array post
    $postLenght = count($_POST);
    
    if($postLenght != 2){
        throw new Exception('Dados insuficientes');
    }

    // Validação 2.1:
    // Validar se as keys email passowrd  estão a ser enviados corretamente
    if(!isset($_POST['email']) ||  !isset($_POST['password'])){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    //var_dump($_POST);
    //die;
    if(strlen($email) == 0 || strlen($password) == 0 ){
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

     // Validação 5:
    // Validar se o email ja é existente na base de dados
    $sqlSelect = 'SELECT * FROM users WHERE email = ? AND password = ?';

    if($stmt = mysqli_prepare($connection, $sqlSelect)){
        mysqli_stmt_bind_param($stmt, 'ss', $email, $password);

        if(mysqli_stmt_execute($stmt)){
            //var_dump($stmt);
            mysqli_stmt_bind_result($stmt, $id, $name, $email, $password, $idCargo);
            if(mysqli_stmt_fetch($stmt)){
                $user = new User($id, $name, $email, $password, $idCargo);
            }
        }else{
            throw new Exception('Erro ao executar a query');
        }
    }

    // Gerar o jwt
    $key = 'psi_jwt_secret_key';
    $payload = [
        'iss' => 'http://mydev.qrschools.com',
        'aud' => 'http://mydev.qrschools.com',
        'iat' => time(),
        'exp' => time() + 360000000,
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
        ]
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    //var_dump($jwt);
    //die;
    

    //var_dump($user);
    $result = [
        'success' => true,
        'data' => [
            'user'=> $user,
            'jwt' => $jwt
        ]
    ];

    echo(json_encode($result, JSON_PRETTY_PRINT));

} catch (InvalidArgumentException $e) {
    // provided key/key-array is empty or malformed.
} catch (DomainException $e) {
    // provided algorithm is unsupported OR
    // provided key is invalid OR
    // unknown error thrown in openSSL or libsodium OR
    // libsodium is required but not available.
} catch (SignatureInvalidException $e) {
    // provided JWT signature verification failed.
} catch (BeforeValidException $e) {
    // provided JWT is trying to be used before "nbf" claim OR
    // provided JWT is trying to be used before "iat" claim.
} catch (ExpiredException $e) {
    // provided JWT is trying to be used after "exp" claim.
} catch (UnexpectedValueException $e) {
    // provided JWT is malformed OR
    // provided JWT is missing an algorithm / using an unsupported algorithm OR
    // provided JWT algorithm does not match provided key OR
    // provided key ID in key/key-array is empty or invalid.
}catch(Exception $e){
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo(json_encode($result));

}finally{
    mysqli_close($connection);
}

?>