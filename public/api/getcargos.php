<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/cargo.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

try {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        $jwt = $matches[1];
    } else {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        throw new Exception('Método não permitido');
    }

    $sqlSelect = 'SELECT id, nome FROM cargos';

    $stmt = $pdo->prepare($sqlSelect);
    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    $cargos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cargo = new Cargo($row['id'], $row['nome']);
        $cargos[$row['id']] = $cargo;
    }

    $result = [
        'success' => true,
        'data' => [
            'cargos' => $cargos,
        ]
    ];

    echo json_encode($result, JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
    $result = [
        'success' => false,
        'error' => 'Token expirado: ' . $e->getMessage()
    ];
    echo json_encode($result);

} catch (Exception $e) {
    $result = [
        'success' => false,
        'error' => $e->getMessage()
    ];
    echo json_encode($result);

} finally {
    $pdo = null;
}
