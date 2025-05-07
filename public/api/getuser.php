<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/user.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

try {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método não permitido');
    }

    if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        $jwt = $matches[1];
    } else {
        throw new Exception('Token JWT não fornecido ou mal formatado');
    }

    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    if (!isset($_GET['id'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $id = $_GET['id'];

    $sql = 'SELECT id, name, email, password, id_cargo FROM users WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Utilizador não encontrado');
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $user = new User($row['id'], $row['name'], $row['email'], $row['password'], $row['id_cargo']);

    $result = [
        'success' => true,
        'data' => [
            'user' => $user,
        ]
    ];

    echo json_encode($result, JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
    echo json_encode([
        'success' => false,
        'erro' => 'Token expirado: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'erro' => $e->getMessage()
    ]);

} finally {
    $pdo = null;
}
