<?php
require 'connectdb.php';
session_start();

// Definir tipo de resposta como JSON
header('Content-Type: application/json');

// Ler dados JSON do corpo da requisição
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Verificar CSRF token
if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Token CSRF inválido.'
    ]);
    exit;
}

// Receber e limpar os inputs
$nome     = trim($input['firstname'] ?? '');
$apelido  = trim($input['lastname'] ?? '');
$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$confirm_password = trim($input['confirm_password'] ?? '');

// Validar campos obrigatórios
if (empty($nome) || empty($apelido) || empty($email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Por favor, preencha todos os campos.'
    ]);
    exit;
}

// Validar e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email inválido.'
    ]);
    exit;
}

// Validar password
if (strlen($password) < 6) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A password deve ter pelo menos 6 caracteres.'
    ]);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'As passwords não coincidem.'
    ]);
    exit;
}

// Hash da password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Inserir na base de dados
try {
    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, apelido, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $apelido, $email, $hashedPassword]);

    // Sucesso
    echo json_encode([
        'status' => 'success',
        'message' => 'Conta criada com sucesso.'  // <- redirecionamento definido pelo backend, usado no frontend
    ]);
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Este email já está registado.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao criar conta, por favor tente novamente mais tarde.'
        ]);
    }
    exit;
}
}
?>
