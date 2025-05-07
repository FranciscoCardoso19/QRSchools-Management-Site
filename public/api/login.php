<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/user.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (strlen($email) == 0 || strlen($password) == 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    // Preparar a consulta com PDO
    $sqlSelect = 'SELECT * FROM users WHERE email = :email AND password = :password';
    $stmt = $pdo->prepare($sqlSelect);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        throw new Exception('Credenciais inválidas');
    }

    $user = new User(
        $userData['id'],
        $userData['name'],
        $userData['email'],
        $userData['password'],
        $userData['id_cargo']
    );

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

    $result = [
        'success' => true,
        'data' => [
            'user' => $user,
            'jwt' => $jwt
        ]
    ];

    echo(json_encode($result, JSON_PRETTY_PRINT));

} catch (PDOException $e) {
    $result = [
        'success' => false,
        'erro' => 'Erro de base de dados: ' . $e->getMessage()
    ];
    echo(json_encode($result));
} catch (Exception $e) {
    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];
    echo(json_encode($result));
}
?>
