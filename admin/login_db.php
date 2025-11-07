<?php
require 'connectdb.php'; // Inclui a conexão com a base de dados
session_start(); // Inicia a sessão PHP, necessária para login, CSRF, tentativas de login



// Inicializar contadores de tentativas de login
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0; // Contador de tentativas
    $_SESSION['last_attempt_time'] = time(); // Hora da última tentativa
}

$max_attempts = 5; // Número máximo de tentativas permitidas
$lockout_time = 300; // Bloqueio por 5 minutos se exceder tentativas

// Verifica se o usuário ultrapassou o limite de tentativas
if ($_SESSION['login_attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
    echo json_encode(['status' => 'error', 'message' => 'Muitas tentativas de login. Por favor, tente novamente mais tarde.']);
    exit();
}



// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
       echo json_encode(['status'=>'error','message'=>'Dados inválidos']);
       exit;
    }


    $username = trim($input['username'] ??  '');
    $password = trim($input['password'] ?? '');
    $csrf_token = $input['csrf_token'] ?? '';

    // Verifica CSRF
    if ($csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
        exit;
    }

    // Validação do email
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'O username deve ser um endereço de email válido.']);
        exit;
    }

    
    // Verifica se todos os campos foram preenchidos
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos.']);
    }
    
    // Limita o tamanho dos inputs para segurança
    if (strlen($username) > 50 || strlen($password) > 255) {
        echo json_encode(['status' => 'error', 'message' => 'Limite de tamanho excedido.']);
        exit;
    }

    // Prepara e executa a query para buscar o usuário
    $stmt = $pdo->prepare('SELECT * FROM utilizadores WHERE email = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(); // Busca o usuário na base de dados

    // Verifica se usuário existe e senha corresponde ao hash armazenado
    if ($user && password_verify($password, $user['password'])) {
        // Login bem-sucedido

        session_regenerate_id(true); // Evita fixação de sessão
        $_SESSION['user_id'] = $user['id']; // Guarda ID do usuário na sessão
        $_SESSION['funcao'] = $user['funcao']; // Guarda função do usuário na sessão
        $_SESSION['fullname'] = $user['nome'] . ' ' . $user['apelido']; // Guarda nome do usuário na sessão 
        $_SESSION['nome'] = $user['nome']; // Guarda o nome na sessão
        $_SESSION['apelido'] = $user['apelido']; // Guarda o apelido na sessão
        $_SESSION['email'] = $user['email']; // Guarda o email na sessão
        $_SESSION['password'] = $user['password'];  // Guarda a password na sessão, evitando XSS
        $_SESSION['LAST_ACTIVITY'] = time(); // Inicializa o controle de tempo de atividade
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gera um novo token CSRF
        // Guarda o username na sessão, evitando XSS
        $_SESSION['username'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        $_SESSION['login_attempts'] = 0; // Reset tentativas
        echo json_encode([
            'status' => 'login_success',
            'redirect' => './admin/' // Página para redirecionar após login bem-sucedido
        ]);
        exit();
    } else {
        // Login falhou
        $_SESSION['login_attempts'] += 1; // Incrementa tentativas
        $_SESSION['last_attempt_time'] = time(); // Atualiza hora da última tentativa
         echo json_encode([
          'status' => 'error',
          'message' => 'Credenciais inválidas.'
        ]);
    }
}
?>