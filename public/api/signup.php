<?php
include_once('../../backend/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método não permitido');
    }

    $postLength = count($_POST);
    if ($postLength != 5) {
        throw new Exception('Dados insuficientes');
    }

    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || !isset($_POST['id_cargo'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $idCargo = trim($_POST['id_cargo']);

    if (strlen($name) == 0 || strlen($email) == 0 || strlen($password) == 0 || strlen($confirmPassword) == 0 || strlen($idCargo) == 0) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    if ($password != $confirmPassword) {
        throw new Exception('As passwords não coincidem');
    }

    $pdo->beginTransaction();

    $sqlSelect = 'SELECT * FROM users WHERE email = :email';
    $stmt = $pdo->prepare($sqlSelect);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        throw new Exception('Erro ao criar registo.');
    }

    $sqlInsert = 'INSERT INTO users (name, email, password, id_cargo) VALUES (:name, :email, :password, :id_cargo)';
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':id_cargo', $idCargo, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir o utilizador na base de dados');
    }

    if ($stmt->rowCount() == 0) {
        throw new Exception('Erro ao inserir o utilizador na base de dados');
    }

    $pdo->commit();

    $result = [
        'success' => true,
        'message' => 'Utilizador registado com sucesso',
    ];

    echo json_encode($result);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $result = [
        'success' => false,
        'erro' => $e->getMessage()
    ];

    echo json_encode($result);

} finally {
    $pdo = null;
}
?>
