<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/equipamento.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'psi_jwt_secret_key';

$pdo->beginTransaction();

try {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
        $jwt = $matches[1];
    }

    $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método não permitido');
    }

    $postLength = count($_POST);

    if ($postLength != 6) {
        throw new Exception('Dados insuficientes');
    }

    if (!isset($_GET['id']) || !isset($_POST['idCategoria']) || !isset($_POST['idEstado']) || !isset($_POST['name']) || !isset($_POST['numSerie']) || !isset($_POST['descricao']) || !isset($_POST['qrcode'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $idCategoria = trim($_POST['idCategoria']);
    $idEstado = trim($_POST['idEstado']);
    $name = trim($_POST['name']);
    $numSerie = trim($_POST['numSerie']);
    $descricao = trim($_POST['descricao']);
    $qrcode = trim($_POST['qrcode']);

    if (strlen($idCategoria) == 0 || strlen($idEstado) == 0 || strlen($name) == 0 || strlen($numSerie) == 0 || strlen($descricao) == 0 || strlen($qrcode) == 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $id = $_GET['id'];

    $sqlSelect = 'SELECT * FROM equipamentos WHERE id = :id';
    $stmt = $pdo->prepare($sqlSelect);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() < 1) {
        throw new Exception('Erro ao criar registo.');
    }

    $sqlUpdate = 'UPDATE equipamentos SET  id_categoria = :idCategoria, id_estado = :idEstado, name = :name, num_serie = :numSerie, descricao = :descricao, qrcode = :qrcode WHERE id = :id';
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
    $stmt->bindParam(':idEstado', $idEstado, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':numSerie', $numSerie, PDO::PARAM_STR);
    $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $stmt->bindParam(':qrcode', $qrcode, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Registo atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao executar a query');
    }

    $sqlSelectUpdated = 'SELECT * FROM equipamentos WHERE id = :id';
    $stmt = $pdo->prepare($sqlSelectUpdated);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($equipamento) {
        $result = [
            'success' => true,
            'data' => [
                'id' => $equipamento['id'],
                'id_categoria' => $equipamento['id_categoria'],
                'id_estado' => $equipamento['id_estado'],
                'name' => $equipamento['name'],
                'num_serie' => $equipamento['num_serie'],
                'descricao' => $equipamento['descricao'],
                'qrcode' => $equipamento['qrcode'],
            ]
        ];
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        throw new Exception('Erro ao buscar o utilizador');
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $result = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    echo json_encode($result);

} finally {
    $pdo = null;
}
