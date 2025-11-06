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

if (!$input || !isset($input['id'], $input['nome'], $input['apelido'], $input['email'], $input['funcao'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$id = intval($input['id']);
$nome = trim($input['nome']);
$apelido = trim($input['apelido']);
$email = trim($input['email']);
$funcao = trim($input['funcao']);

if (!$nome || !$apelido || !$email || !$funcao) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Email inválido']);
    exit;
}

if (strlen($nome) > 100 || strlen($apelido) > 100 || strlen($email) > 255) {
    echo json_encode(['status' => 'error', 'message' => 'Os dados excedem o tamanho permitido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE utilizadores SET nome = :nome, apelido = :apelido, email = :email, funcao = :funcao WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':apelido', $apelido);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':funcao', $funcao);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Utilizador atualizado com sucesso']);
    } else {
        echo json_encode(['status' => 'warning', 'message' => 'Nenhuma alteração foi feita.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar utilizador: ' . $e->getMessage()]);
}
?>
