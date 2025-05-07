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

    if (!isset($_POST['idUser']) || !isset($_POST['dataInventario'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $idUser = trim($_POST['idUser']);
    $dataInventario = trim($_POST['dataInventario']);

    if (strlen($idUser) === 0 || strlen($dataInventario) === 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $pdo->beginTransaction();

    $sql = 'INSERT INTO inventarios (id_user, data_inventario) VALUES (:idUser, :dataInventario)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $stmt->bindParam(':dataInventario', $dataInventario, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erro ao inserir o inventário na base de dados');
    }

    $pdo->commit();

    $result = [
        'success' => true,
        'message' => 'Inventário criado com sucesso',
    ];

    echo json_encode($result, JSON_PRETTY_PRINT);

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
