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

    if (!isset($_GET['id'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $id = $_GET['id'];

    $sql = 'SELECT * FROM equipamentos WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Equipamento não encontrado');
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $equipamento = new Equipamento(
        $row['id'],
        $row['id_categoria'],
        $row['id_estado'],
        $row['name'],
        $row['num_serie'],
        $row['descricao'],
        $row['qrcode']
    );

    echo json_encode([
        'success' => true,
        'data' => ['equipamento' => $equipamento]
    ], JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Token expirado: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);

} finally {
    $pdo = null;
}
