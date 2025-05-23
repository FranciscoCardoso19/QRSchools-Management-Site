<?php
require '../../vendor/autoload.php';

include_once('../../backend/db.php');
include_once('../../backend/models/user.php');

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

    if ($postLength != 2) {
        throw new Exception('Dados insuficientes');
    }

    if (!isset($_GET['id']) || !isset($_POST['name']) || !isset($_POST['password'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    if (strlen($name) == 0 || strlen($password) == 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $id = $_GET['id'];

    $sqlSelect = 'SELECT * FROM users WHERE id = :id';
    $stmt = $pdo->prepare($sqlSelect);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() < 1) {
        throw new Exception('Erro ao criar registo.');
    }

    $sqlUpdate = 'UPDATE users SET name = :name, password = :password WHERE id = :id';
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Registo atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao executar a query');
    }

    $sqlSelectUpdated = 'SELECT * FROM users WHERE id = :id';
    $stmt = $pdo->prepare($sqlSelectUpdated);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $result = [
            'success' => true,
            'data' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
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
