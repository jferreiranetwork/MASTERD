<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Lisbon');
require_once 'connectdb.php';

if (!isset($_SESSION['user_id']) || $_SESSION['funcao'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {

    switch ($action) {

        case 'listar_noticias':
            $stmt = $pdo->query("SELECT id, titulo, descricao, data_publicacao, autor, imagem, tipo_mime FROM noticias ORDER BY id DESC");
            $noticias = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['id'] = (int) $row['id'];
                $row['titulo'] = htmlspecialchars($row['titulo']);
                $row['descricao'] = htmlspecialchars($row['descricao']);
                $row['autor'] = htmlspecialchars($row['autor']);
                $row['imagem'] = 'data:' . $row['tipo_mime'] . ';base64,' . base64_encode($row['imagem']);
                $row['data_publicacao'] = date('d/m/Y', strtotime($row['data_publicacao']));

                $noticias[] = $row;
            }

            echo json_encode(['status' => 'success', 'data' => $noticias]);
            break;

        case 'criar_noticia':
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $autor = trim($_POST['autor'] ?? '');
            $dadosImagem = null;
            $tipoMime = null;

            if (!empty($_FILES['imagem']['tmp_name']) && is_uploaded_file($_FILES['imagem']['tmp_name'])) {
                $imagemTmp = $_FILES['imagem']['tmp_name'];
                $tipoMime = mime_content_type($imagemTmp);
                $dadosImagem = file_get_contents($imagemTmp);
            }


            if ($titulo === '' || $descricao === '' || $autor === '') {
                echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
                exit;
            }


            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, descricao, autor, imagem, tipo_mime) VALUES (:titulo, :descricao, :autor, :imagem, :tipo_mime)");
            $ok = $stmt->execute([
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':autor' => $autor,
                ':imagem' => $dadosImagem,
                ':tipo_mime' => $tipoMime
            ]);

            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Notícia criada com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao criar notícia.']);
            }
            break;

        case 'alterar_noticia':
            $id = intval($_POST['id'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $autor = trim($_POST['autor'] ?? '');
            $imagemTmp = $_FILES['imagem']['tmp_name'] ?? null;

            if ($id <= 0 || $titulo === '' || $descricao === '' || $autor === '') {
                echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']);
                exit;
            }

            if ($imagemTmp) {
                $tipoMime = mime_content_type($imagemTmp);
                $dadosImagem = file_get_contents($imagemTmp);
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = :titulo, descricao = :descricao, autor = :autor, imagem = :imagem, tipo_mime = :tipo_mime WHERE id = :id");
                $params = [
                    ':titulo' => $titulo,
                    ':descricao' => $descricao,
                    ':autor' => $autor,
                    ':imagem' => $dadosImagem,
                    ':tipo_mime' => $tipoMime,
                    ':id' => $id
                ];
            } else {
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = :titulo, descricao = :descricao, autor = :autor WHERE id = :id");
                $params = [
                    ':titulo' => $titulo,
                    ':descricao' => $descricao,
                    ':autor' => $autor,
                    ':id' => $id
                ];
            }

            $ok = $stmt->execute($params);

            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Notícia atualizada com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar notícia.']);
            }
            break;

        case 'eliminar_noticia':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = isset($data['id']) ? intval($data['id']) : 0;

            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = :id");
            $ok = $stmt->execute([':id' => $id]);

            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Notícia eliminada com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao eliminar notícia.']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
    exit;
}
