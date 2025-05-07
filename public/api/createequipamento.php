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

    if (
        !isset($_POST['idCategoria']) || !isset($_POST['idEstado']) || !isset($_POST['name']) ||
        !isset($_POST['numSerie']) || !isset($_POST['descricao']) || !isset($_POST['qrcode'])
    ) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $idCategoria = trim($_POST['idCategoria']);
    $idEstado = trim($_POST['idEstado']);
    $name = trim($_POST['name']);
    $numSerie = trim($_POST['numSerie']);
    $descricao = trim($_POST['descricao']);
    $qrcode = trim($_POST['qrcode']);

    if (empty($idCategoria) || empty($idEstado) || empty($name) || empty($numSerie) || empty($descricao) || empty($qrcode)
    ) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $pdo->beginTransaction();

    $sql = 'INSERT INTO equipamentos (id_categoria, id_estado, name, num_serie, descricao, qrcode) 
            VALUES (:idCategoria, :idEstado, :name, :numSerie, :descricao, :qrcode)';
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
    $stmt->bindParam(':idEstado', $idEstado, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':numSerie', $numSerie, PDO::PARAM_STR);
    $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $stmt->bindParam(':qrcode', $qrcode, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir o equipamento na base de dados');
    }

    $pdo->commit();

    $result = [
        'success' => true,
        'message' => 'Equipamento criado com sucesso',
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
}
?>
