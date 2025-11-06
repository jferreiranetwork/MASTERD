<?php

require_once('connectdb.php');



$stmt = $pdo->query("SELECT * FROM projetos");
$projetos = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $projetos[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'imagem' => 'data:' . $row['tipo_mime'] . ';base64,' . base64_encode($row['imagem']),
        'descricao' => $row['descricao'],
        'tecnologias' => $row['tecnologia']

    ];
}

echo json_encode($projetos);