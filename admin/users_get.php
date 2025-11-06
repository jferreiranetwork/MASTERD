<?php
require_once 'connectdb.php';
session_start();

// Timeout de sessão
$session_timeout = 300;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Verifica se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['funcao'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

// validação CSRF

if (!isset($_SESSION['csrf_token'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
    exit;
}

// Define que o retorno será JSON
header('Content-Type: application/json');

// Obter o ID do usuário
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400); // Bad request
    echo json_encode(['status' => 'error', 'message' => 'ID de usuário inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, apelido, email, funcao FROM utilizadores WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Utilizador não encontrado.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na consulta: ' . $e->getMessage()
    ]);
}
?>
