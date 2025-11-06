<?php

session_start();

$session_timeout = 300; // 5 minutos


if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    // Sessão expirada por inatividade
    session_unset();     // limpa variáveis de sessão
    session_destroy();   // destrói a sessão
    header("Location: ../");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza o timestamp da última atividade

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit();
}

?>

<?php include './header.php'; ?>

<body>

  <?php include '../nav.php'; ?>

<div class="container pt-5">

  <div id ="content-admin">


</div>

</body>
</html>
