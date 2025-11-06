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



$input = json_decode(file_get_contents('php://input'), true);

$action = $_POST['action'] ?? $_GET['action'] ?? '';


try {

    switch ($action) {
        case 'editar_perfil':
            $id = $_SESSION['user_id'];
            $nome = trim($input['nome']);
            $email = trim($input['email']);
            $password = trim($input['pw_atual']);
            $nova_password = trim($input['nova_pw']);
            $confirmar_password = trim($input['confirmar_pw']);

            if ($password !== $_SESSION['password']) {
                echo json_encode(['status' => 'error', 'message' => 'Senha atual incorreta.']);
                exit;
            }

            if ($nova_password !== $confirmar_password) {
                echo json_encode(['status' => 'error', 'message' => 'As passwords novas devem ser iguais.']);
                exit;
            }


            if (!empty($nova_password)) {
                $password = password_hash($nova_password, PASSWORD_DEFAULT);
            }


            $stmt = $pdo->prepare("UPDATE users SET nome = :nome, email = :email, password = :password WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

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
