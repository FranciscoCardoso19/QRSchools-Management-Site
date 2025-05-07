<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php'); 
include_once('../../backend/models/equipamento.php');

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

    $sql = 'SELECT * FROM equipamentos';
    $stmt = $pdo->prepare($sql);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    $equipamentos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $equipamento = new Equipamento(
            $row['id'],
            $row['id_categoria'],
            $row['id_estado'],
            $row['name'],
            $row['num_serie'],
            $row['descricao'],
            $row['qrcode']
        );
        $equipamentos[$row['id']] = $equipamento;
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'equipamentos' => $equipamentos
        ]
    ], JSON_PRETTY_PRINT);

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
