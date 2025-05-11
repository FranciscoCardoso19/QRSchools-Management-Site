<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/Localizacao.php');

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

    if (!isset($_GET['id'])) {
        throw new Exception('ID da localização não fornecido');
    }

    $id = $_GET['id'];

    $sql = 'SELECT * FROM localizacoes WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Localização não encontrada');
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $localizacao = new Localizacao(
        $row['id'],
        $row['id_sala'],
        $row['id_parent_location'],
        $row['id_equipamento'],
        $row['id_localizacao_categoria'],
        $row['nome'],
        $row['descricao']
    );

    echo json_encode([
        'success' => true,
        'data' => ['localizacao' => $localizacao]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    $pdo = null;
}
