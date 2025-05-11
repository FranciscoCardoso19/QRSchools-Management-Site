<?php
require '../../vendor/autoload.php';
include_once('../../backend/db.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

try {
    $headers = getallheaders();
    $authorization = $headers['Authorization'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwt = $matches[1];
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    $input = json_decode(file_get_contents("php://input"), true) ?? [];
    $id = $input['id'] ?? ($_GET['id'] ?? null);

    if (!$id) {
        throw new Exception('ID da requisição não fornecido.');
    }

    $pdo->beginTransaction();

    $sql = 'DELETE FROM requisicoes WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query.');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Requisição não encontrada ou já removida.');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Requisição removida com sucesso.'
    ], JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'error' => 'Token expirado: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);

} finally {
    $pdo = null;
}
