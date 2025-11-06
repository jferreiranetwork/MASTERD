<?php

require_once 'connectdb.php';

session_start();

$session_timeout = 300;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: ../");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id']) || $_SESSION['funcao'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

// valida token CSRF
$headers = getallheaders();
if (!isset($headers['X-CSRF-Token']) || $headers['X-CSRF-Token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido']);
    echo json_encode(['status' => 'error', 'message' => '$_SESSION[csrf_token]']);
    exit;
}

// Define que o retorno será JSON
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, CONCAT(nome, ' ', apelido) AS username, email, funcao FROM utilizadores");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results && count($results) > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $results
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nenhum usuário encontrado.'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na consulta, por favor tente novamente mais tarde.'
    ]);
}
?>