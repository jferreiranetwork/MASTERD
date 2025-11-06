<?php
require_once 'connectdb.php';
session_start();

$session_timeout = 300;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id']) || $_SESSION['funcao'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$id = intval($input['id']);


if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit;
}

if ($id == $_SESSION['user_id']) {
    echo json_encode(['status' => 'error', 'message' => 'Não pode eliminar o seu próprio utilizador.']);
    exit;
}


try {
    $stmt = $pdo->prepare("DELETE FROM utilizadores WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Utilizador não encontrado ou já eliminado.']);
        exit;
    }
    echo json_encode(['status' => 'success', 'message' => 'Utilizador eliminado com sucesso.']);
    // se der o erro de 1451 significa que o utilizador tem registos noutros lados (ex: consultas) e não pode ser eliminado informa o user
    if ($stmt->errorInfo()[1] == 1451) {
        echo json_encode(['status' => 'error', 'message' => 'Utilizador tem consultas associadas e nao pode ser eliminado.']);
        exit;
    }


} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro ao eliminar utilizador: ' . $e->getMessage()]);
}
exit;
?>
