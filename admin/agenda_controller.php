<?php
session_start();
require_once 'connectdb.php';
header('Content-Type: application/json');
date_default_timezone_set('Europe/Lisbon');


// Configuração
$session_timeout = 300;

// Timeout de sessão
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    echo json_encode(['status'=>'error','message'=>'Sessão expirada']);
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// Verifica se está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['funcao'])) {
    echo json_encode(['status'=>'error','message'=>'Não autenticado']);
    exit;
}


// Função de escape simples
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Identificar método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// ------------------ ATUALIZAR ESTADO ------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // CSRF
    $headers = getallheaders();
    if (!isset($headers['X-CSRF-Token']) || $headers['X-CSRF-Token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['status'=>'error','message'=>'Token CSRF inválido']);
        exit;
    }

    // ------------------ CRIAR CONSULTA ------------------
    if (isset($input['tipo']) && $input['tipo'] === 'adicionar_consulta') {
        $clienteId = intval($input['cliente_id']);
        $dataHora = trim($input['data_hora']);
        $categoria = trim($input['categoria']);
        $organizador = trim($input['organizador']);


        // as consultas só podem ser marcadas até 72 horas antes da data/hora atual

        if ($_SESSION['funcao'] === 'cliente') {
            $clienteId = $_SESSION['user_id'];
            $dataConsulta = new DateTime($dataHora);   // data da consulta    
            $dataAtual = new DateTime();
            $diffHoras = ($dataConsulta->getTimestamp() - $dataAtual->getTimestamp()) / 3600;

            if ($diffHoras < 72) {
               echo json_encode(['status'=>'error','message' => 'Consultas devem ser marcadas com pelo menos 72 horas de antecedência.']);

               exit;
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO consultas (cliente_id, data, estado, organizador, categoria) VALUES (:cliente, :datahora, 'pendente', :organizador, :categoria)");
            $stmt->bindParam(':cliente', $clienteId, PDO::PARAM_INT);
            $stmt->bindParam(':datahora', $dataHora);
            $stmt->bindParam(':organizador', $organizador);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->execute();

            echo json_encode(['status'=>'success','message'=>'Consulta criada com sucesso']);
        } catch(PDOException $e) {
            echo json_encode(['status'=>'error','message'=>'Erro ao criar consulta']);

        }
        exit;
    }

    // ------------------ ATUALIZAR ESTADO ------------------
    if (!isset($input['id']) || !isset($input['estado'])) {
        echo json_encode(['status'=>'error','message'=>'Dados inválidos']);
        exit;
    }

    $id = intval($input['id']);
    $novoEstado = trim($input['estado']);

    if ($_SESSION['funcao'] === 'cliente' && $novoEstado !== 'cancelada') {
        echo json_encode(['status'=>'error','message'=>'Cliente só pode cancelar eventos']);
        exit;
    }


    if ($_SESSION['funcao'] === 'admin' && $novoEstado === 'cancelada') {
        
    

        $stmt = $pdo->prepare("delete from consultas where id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo json_encode(['status'=>'success','message'=>'Consulta eliminada com sucesso']);
        exit;

    }
    

    try {
        $stmt = $pdo->prepare("UPDATE consultas SET estado=:estado WHERE id=:id");
        $stmt->bindParam(':estado', $novoEstado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo json_encode(['status'=>'success','message'=>'Estado atualizado com sucesso']);
    } catch(PDOException $e) {
        echo json_encode(['status'=>'error','message'=>'Erro ao atualizar estado']);
    }
    exit;
}


// ------------------ CARREGAR EVENTOS + CLIENTES ------------------
if ($method === 'GET') {
    $userRole = $_SESSION['funcao'];
    $userId = $_SESSION['user_id'];
    $response = ['status'=>'success','funcao'=>$userRole];

    // Lista de clientes (apenas admin)
    if ($userRole === 'admin') {
        $stmt = $pdo->prepare("SELECT id, CONCAT(nome,' ',apelido) AS fullname FROM utilizadores WHERE funcao='cliente'");
        $stmt->execute();
        $response['clientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eventos
    try {
        if ($userRole === 'admin') {
            $stmt = $pdo->prepare("
                SELECT c.id, c.cliente_id, c.data, c.categoria, c.organizador, c.estado,
                CONCAT(u.nome,' ',u.apelido) AS nome_utilizador, u.imagem AS imagem_utilizador,
                u.funcao
                FROM consultas c
                JOIN utilizadores u ON c.cliente_id=u.id
            ");
        } else {
            $stmt = $pdo->prepare("
                SELECT c.id, c.cliente_id, c.data, c.categoria, c.organizador, c.estado,
                CONCAT(u.nome,' ',u.apelido) AS nome_utilizador, u.imagem AS imagem_utilizador,
                u.funcao
                FROM consultas c
                JOIN utilizadores u ON c.cliente_id=u.id
                WHERE c.cliente_id=:user_id
            ");
            $stmt->bindParam(':user_id',$userId,PDO::PARAM_INT);
        }
        $stmt->execute();
        $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $response['status'] = 'error';
        $response['message'] = 'Erro ao carregar eventos';
    }

    echo json_encode($response);
    exit;
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Método não permitido']);
