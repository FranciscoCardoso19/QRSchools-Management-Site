<?php
require '../../vendor/autoload.php';
include_once('../../backend/db.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

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

    $input = json_decode(file_get_contents("php://input"), true) ?? [];
    $id = isset($input['id']) ? intval($input['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

    if (!$id) {
        throw new Exception('ID do cargo não fornecido.');
    }

    $pdo->beginTransaction();

    $sqlDelete = 'DELETE FROM cargos WHERE id = :id';
    $stmt = $pdo->prepare($sqlDelete);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query.');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Cargo não encontrado ou já removido.');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Cargo removido com sucesso.'
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
