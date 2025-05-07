<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/cargo.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'psi_jwt_secret_key';

try {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
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

    $sqlSelect = 'SELECT * FROM cargos WHERE id = :id';

    $stmt = $pdo->prepare($sqlSelect);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    $cargo = null;
    if ($stmt->rowCount() == 0) {
        throw new Exception('Cargo não encontrado');
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cargo = new Cargo($row['id'], $row['nome']);
    }

    $result = [
        'success' => true,
        'data' => [
            'cargo' => $cargo,
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
