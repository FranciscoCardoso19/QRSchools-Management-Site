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
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwt = $matches[1];
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Validação dos dados
    $requiredFields = ['id', 'id_user', 'disciplina', 'dataPedido', 'dataPrevisaoEntrega'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("Campo obrigatório ausente ou vazio: $field");
        }
    }

    $id = $_POST['id'];
    $id_user = $_POST['id_user'];
    $disciplina = $_POST['disciplina'];
    $dataPedido = $_POST['dataPedido'];
    $dataPrevisaoEntrega = $_POST['dataPrevisaoEntrega'];
    $dataEntrega = $_POST['dataEntrega'] ?? null;

    // Criar objeto (opcional, para manipular depois)
    $requisicao = new Requisicao($id, $id_user, $disciplina, $dataPedido, $dataPrevisaoEntrega, $dataEntrega);

    // Inserir no banco de dados
    $pdo->beginTransaction();

    $sql = 'INSERT INTO requisicoes (id, id_user, disciplina, data_pedido, data_previsao_entrega, data_entrega)
            VALUES (:id, :id_user, :disciplina, :dataPedido, :dataPrevisaoEntrega, :dataEntrega)';
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_STR);
    $stmt->bindParam(':disciplina', $disciplina, PDO::PARAM_STR);
    $stmt->bindParam(':dataPedido', $dataPedido);
    $stmt->bindParam(':dataPrevisaoEntrega', $dataPrevisaoEntrega);
    $stmt->bindParam(':dataEntrega', $dataEntrega);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erro ao inserir a Requisição na base de dados');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Requisição criada com sucesso',
        'data' => $requisicao->toString()
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