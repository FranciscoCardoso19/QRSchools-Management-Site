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

    if ( !isset($_POST['nome']) || !isset($_POST['referencia']) || !isset($_POST['tamanho']) || !isset($_POST['mobilidade']) || !isset($_POST['abertura']) || !isset($_POST['numPortas']) || !isset($_POST['material']) || !isset($_POST['cor']) || !isset($_POST['forma'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $nome = trim($_POST['nome']);
    $referencia = trim($_POST['referencia']);
    $tamanho = trim($_POST['tamanho']);
    $mobilidade = trim($_POST['mobilidade']);
    $abertura = trim($_POST['abertura']);
    $numPortas = trim($_POST['numPortas']);
    $material = trim($_POST['material']);
    $cor = trim($_POST['cor']);
    $forma = trim($_POST['forma']);

    if (strlen($nome) === 0 || strlen($referencia) === 0 || strlen($tamanho) === 0 || strlen($mobilidade) === 0 || strlen($abertura) === 0 || strlen($numPortas) === 0 || strlen($material) === 0 || strlen($cor) === 0 || strlen($forma) === 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $pdo->beginTransaction();

    $sql = 'INSERT INTO localizacao_categorias (nome, referencia, tamanho, mobilidade, abertura, num_portas, material, cor, forma) VALUES (:nome, :referencia, :tamanho, :mobilidade, :abertura, :numPortas, :material, :cor, :forma)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
    $stmt->bindParam(':tamanho', $tamanho, PDO::PARAM_STR);
    $stmt->bindParam(':mobilidade', $mobilidade, PDO::PARAM_STR);
    $stmt->bindParam(':abertura', $abertura, PDO::PARAM_STR);
    $stmt->bindParam(':numPortas', $numPortas, PDO::PARAM_INT);
    $stmt->bindParam(':material', $material, PDO::PARAM_STR);
    $stmt->bindParam(':cor', $cor, PDO::PARAM_STR);
    $stmt->bindParam(':forma', $forma, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erro ao inserir categoria Localizacao na base de dados');
    }

    $pdo->commit();

    $result = [
        'success' => true,
        'message' => 'categoria Localizacao criado com sucesso',
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
