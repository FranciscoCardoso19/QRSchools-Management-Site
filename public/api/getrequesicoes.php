<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/Requisicao.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

try {
    $headers = getallheaders();
    $authorization = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwt = $matches[1];
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        throw new Exception('Método não permitido');
    }

    $sql = 'SELECT * FROM requisicoes';
    $stmt = $pdo->prepare($sql);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    $requisicoes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $requisicao = new Requisicao(
            $row['id'],
            $row['id_user'],
            $row['disciplina'],
            $row['dataPedido'],
            $row['dataPrevisaoEntrega'],
            $row['dataEntrega']
        );
        $requisicoes[] = $requisicao;
    }

    echo json_encode([
        'success' => true,
        'data' => ['requisicoes' => $requisicoes]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    $pdo = null;
}
