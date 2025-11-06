<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Lisbon');
require_once 'connectdb.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {

    switch ($action) {

        case 'listar_noticias':
            $stmt = $pdo->query("SELECT id, titulo, descricao, autor, imagem, tipo_mime, data_publicacao FROM noticias ORDER BY id DESC");
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


        case 'ver_noticia':
            $id = intval($_GET['id']);
            $stmt = $pdo->prepare("SELECT id, titulo, descricao, autor, imagem, tipo_mime, data_publicacao FROM noticias WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $row['id'] = (int) $row['id'];
                $row['titulo'] = htmlspecialchars($row['titulo']);
                $row['descricao'] = htmlspecialchars($row['descricao']);
                $row['autor'] = htmlspecialchars($row['autor']);
                $row['imagem'] = 'data:' . $row['tipo_mime'] . ';base64,' . base64_encode($row['imagem']);
                $row['data_publicacao'] = date('d/m/Y', strtotime($row['data_publicacao']));

                echo json_encode(['status' => 'success', 'data' => $row]);
            } else {
              echo json_encode(['status' => 'error', 'message' => 'Notícia não encontrada']);
            }
            break;
            

    }

    

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
    exit;
}

?>