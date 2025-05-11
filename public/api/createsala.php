<?php
require '../../vendor/autoload.php';
include_once('../../backend/db.php');
include_once('../../backend/models/Sala.php');

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

    // Validação dos campos
    $requiredFields = ['id', 'nome', 'piso'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("Campo obrigatório ausente ou vazio: $field");
        }
    }

    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $piso = trim($_POST['piso']);

    $sala = new Sala($id, $nome, $piso);

    $pdo->beginTransaction();

    $sql = 'INSERT INTO salas (id, nome, piso) VALUES (:id, :nome, :piso)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':piso', $piso);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erro ao inserir a Sala na base de dados');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Sala criada com sucesso',
        'data' => $sala->toString()
    ], JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
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
