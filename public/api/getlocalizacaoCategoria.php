<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/localizacaocategoria.php');

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

    $sql = 'SELECT * FROM localizacao_categorias WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('localizacao categorias não encontrado');
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $localizacaocategoria = new Localizacaocategoria($row['id'], $row['nome'], $row['referencia'], $row['tamanho'], $row['mobilidade'], $row['abertura'], $row['num_portas'], $row['material'], $row['cor'], $row['forma']);

    $result = [
        'success' => true,
        'data' => [
            'localizacaocategoria' => $localizacaocategoria,
        ]
    ];

    echo json_encode($result, JSON_PRETTY_PRINT);

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
