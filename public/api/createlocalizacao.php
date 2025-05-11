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
    $authorization = $headers['Authorization'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        throw new Exception('Token JWT não fornecido ou mal formatado.');
    }

    $jwt = $matches[1];
    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Campos obrigatórios
    $requiredFields = ['id', 'idSala', 'idParentLocation', 'idEquipamento', 'idLocalizacaoCategoria', 'nome', 'descricao'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("Campo obrigatório ausente ou vazio: $field");
        }
    }

    // Coletando os dados
    $id = $_POST['id'];
    $idSala = $_POST['idSala'];
    $idParentLocation = $_POST['idParentLocation'];
    $idEquipamento = $_POST['idEquipamento'];
    $idLocalizacaoCategoria = $_POST['idLocalizacaoCategoria'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    $localizacao = new Localizacao($id, $idSala, $idParentLocation, $idEquipamento, $idLocalizacaoCategoria, $nome, $descricao);

    $pdo->beginTransaction();

    $sql = 'INSERT INTO localizacoes (
                id, id_sala, id_parent_location, id_equipamento, id_localizacao_categoria, nome, descricao
            ) VALUES (
                :id, :idSala, :idParentLocation, :idEquipamento, :idLocalizacaoCategoria, :nome, :descricao
            )';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':idSala', $idSala);
    $stmt->bindParam(':idParentLocation', $idParentLocation);
    $stmt->bindParam(':idEquipamento', $idEquipamento);
    $stmt->bindParam(':idLocalizacaoCategoria', $idLocalizacaoCategoria);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a query');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erro ao inserir a Localização na base de dados');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Localização criada com sucesso',
        'data' => $localizacao->toString()
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
