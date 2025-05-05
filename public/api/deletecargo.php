<?php
require '../../vendor/autoload.php';
include_once('../../backend/connection.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$key = 'psi_jwt_secret_key';

mysqli_begin_transaction($connection);

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

    // Tenta obter o ID do corpo ou da query string
    $id = isset($input['id']) ? intval($input['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

    if (!$id) {
        throw new Exception('ID do cargo não fornecido.');
    }

    $sqlDelete = 'DELETE FROM cargos WHERE id = ?';
    if ($stmt = mysqli_prepare($connection, $sqlDelete)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception('Cargo não encontrado ou já removido.');
            }
        } else {
            throw new Exception('Erro ao executar a query.');
        }

        mysqli_stmt_close($stmt);
    } else {
        throw new Exception('Erro ao preparar a query SQL.');
    }

    mysqli_commit($connection);

    echo json_encode([
        'success' => true,
        'message' => 'Cargo removido com sucesso.'
    ], JSON_PRETTY_PRINT);

} catch (ExpiredException $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'success' => false,
        'error' => 'Token expirado: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);

} finally {
    mysqli_close($connection);
}
?>
