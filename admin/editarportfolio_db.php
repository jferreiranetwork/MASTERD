<?php
session_start();
require_once 'connectdb.php';
header('Content-Type: application/json');

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
if (!isset($_SESSION['user_id']) || !isset($_SESSION['funcao']) || $_SESSION['funcao'] !== 'admin') {
    echo json_encode(['status'=>'error','message'=>'Não autenticado']);
    exit;
}



// Função de escape simples
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$headers = getallheaders();
    if (!isset($headers['X-CSRF-Token']) || $headers['X-CSRF-Token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['status'=>'error','message'=>'Token CSRF inválido']);
        exit;
    }


$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? null;


// ------------------ listar portfolio ------------------

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'listar_portfolio') {

    $stmt = $pdo->prepare("SELECT id, titulo, descricao, tecnologia, imagem, tipo_mime FROM projetos");
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($projetos as &$projeto) {
            $projeto['imagem'] = 'data:' . $projeto['tipo_mime'] . ';base64,' . base64_encode($projeto['imagem']);
        }
    echo json_encode(['status'=>'success','projetos'=>$projetos]);
    exit;
}


// --- listar portfolio pelo id quando abrir o modal editar ---

if ($method === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT id, titulo, descricao, tecnologia, imagem, tipo_mime FROM projetos WHERE id = ?");
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $projeto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($projeto) {
        $projeto['imagem'] = 'data:' . $projeto['tipo_mime'] . ';base64,' . base64_encode($projeto['imagem']);
        echo json_encode(['status'=>'success','projeto'=>$projeto]);
        exit;
    } else {
        echo json_encode(['status'=>'error','message'=>'Projeto não encontrado']);
        exit;
    }
}



// ------------------ alterar portfolio ------------------

if ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'alterar_portfolio') {
    $id = intval($_POST['id']);
    $titulo = escape($_POST['titulo']);
    $descricao = escape($_POST['descricao']);
    $tecnologia = escape($_POST['tecnologia']);

    $imagemTmp = $_FILES['imagem']['tmp_name'] ?? null;
    $dadosImagem = null;
    $tipoMime = null;

    if ($imagemTmp && is_uploaded_file($imagemTmp)) {
        $dadosImagem = file_get_contents($imagemTmp);
        $tipoMime = mime_content_type($imagemTmp);
    }

    if (empty($titulo) || empty($descricao) || empty($tecnologia)) {
        echo json_encode(['status'=>'error','message'=>'Dados incompletos']);
        exit;
    }

    if ($dadosImagem) {
        $stmt = $pdo->prepare("
            UPDATE projetos
            SET titulo = ?, descricao = ?, tecnologia = ?, tipo_mime = ?, imagem = ?
            WHERE id = ?
        ");
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $descricao);
        $stmt->bindParam(3, $tecnologia);
        $stmt->bindParam(4, $tipoMime);
        $stmt->bindParam(5, $dadosImagem, PDO::PARAM_LOB);
        $stmt->bindParam(6, $id);
    } else {
        $stmt = $pdo->prepare("
            UPDATE projetos
            SET titulo = ?, descricao = ?, tecnologia = ?
            WHERE id = ?
        ");
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $descricao);
        $stmt->bindParam(3, $tecnologia);
        $stmt->bindParam(4, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status'=>'success','message'=>'Projeto atualizado com sucesso']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Erro ao atualizar projeto']);
    }
    exit;
}




// ------------------ criar portfolio ------------------
if ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'criar_portfolio') {
    $titulo = escape($_POST['titulo']);
    $descricao = escape($_POST['descricao']);
    $tecnologia = escape($_POST['tecnologia']);
    $imagemTmp = $_FILES['imagem']['tmp_name'];
    $tipoMime = mime_content_type($imagemTmp); 
    $dadosImagem = file_get_contents($imagemTmp); 
    if (empty($titulo) || empty($descricao) || empty($tecnologia) || empty($imagemTmp)) {
        echo json_encode(['status'=>'error','message'=>'Dados incompletos']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO projetos (titulo, descricao, tecnologia, tipo_mime, imagem) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $titulo);
    $stmt->bindParam(2, $descricao);
    $stmt->bindParam(3, $tecnologia);
    $stmt->bindParam(4, $tipoMime);
    $stmt->bindParam(5, $dadosImagem, PDO::PARAM_LOB);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'success','message'=>'Projeto criado com sucesso']);
        exit;
    } else {
        echo json_encode(['status'=>'error','message'=>'Erro ao criar projeto']);
        exit;
    }
}


// ------------------ eliminar portfolio ------------------
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'])) {
        echo json_encode(['status'=>'error','message'=>'ID do projeto não fornecido']);
        exit;
    }
    $id = intval($input['id']);
    $stmt = $pdo->prepare("DELETE FROM projetos WHERE id = ?");
    $stmt->bindParam(1, $id);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'success','message'=>'Projeto eliminado com sucesso']);
        exit;
    } else {
        echo json_encode(['status'=>'error','message'=>'Erro ao eliminar projeto']);
        exit;
    }
}
?>

