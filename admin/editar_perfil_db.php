<?php
require_once 'connectdb.php';
session_start();
header('Content-Type: application/json');

$session_timeout = 300;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id']) ) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}



$input = $_POST; // ou json_decode(file_get_contents('php://input'), true);

$action = $_POST['action'] ?? $_GET['action'] ?? '';


try {

    switch ($action) {
        case 'editar_perfil':
            $id = $_SESSION['user_id'];
            $nome = trim($input['nome']);
            $apelido = trim($input['apelido']);
            $email = trim($input['email']);
            $password_atual = trim($input['pw_atual']);
            $nova_password = trim($input['nova_pw'] ?? '');
            $confirmar_password = trim($input['confirmar_pw'] ?? '');

            // Verifica a password atual
            if (!password_verify($password_atual, $_SESSION['password'])) {
                echo json_encode(['status' => 'error', 'message' => 'Password atual incorreta.']);
                exit;
            }

            // Se houver nova senha, verifica se bate com a confirmação
            if (!empty($nova_password) && $nova_password !== $confirmar_password) {
                echo json_encode(['status' => 'error', 'message' => 'As passwords novas devem ser iguais.']);
                exit;
            }


            $fields = 'nome = :nome, apelido = :apelido, email = :email';
            $params = [
                ':id' => $id,
                ':nome' => $nome,
                ':apelido' => $apelido,
                ':email' => $email
            ];

            if (!empty($nova_password)) {
                $fields .= ', password = :password';
                $params[':password'] = password_hash($nova_password, PASSWORD_DEFAULT);
            }

            $stmt = $pdo->prepare("UPDATE utilizadores SET $fields WHERE id = :id");
            $stmt->execute($params);

            echo json_encode(['status' => 'success', 'message' => 'Perfil atualizado com sucesso']);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
            exit;
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
    exit;
}

